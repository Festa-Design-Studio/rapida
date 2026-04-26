<?php

namespace App\Services;

use App\DataTransferObjects\LandmarkImportResult;
use App\Models\Crisis;
use App\Models\Landmark;

/**
 * Parses a Landmark CSV and inserts each row through the Eloquent
 * model so audit-log + factory observers fire normally.
 *
 * Canonical column order (header row required):
 *   name, type, latitude, longitude, crisis_slug
 *
 * Other column orders are NOT auto-detected — operators get a clear
 * error pointing at the bad row rather than silent column-shift bugs
 * that only surface as wrong-coords reports weeks later.
 *
 * Geocoding is NOT supported in v1: rows missing numeric lat/lng are
 * rejected. Operators who paste address-only CSVs from QGIS should
 * geocode there first.
 */
class LandmarkBulkImportService
{
    private const REQUIRED_HEADERS = ['name', 'type', 'latitude', 'longitude', 'crisis_slug'];

    private const VALID_TYPES = [
        'hospital', 'school', 'mosque', 'church', 'market',
        'government', 'bridge', 'water_source', 'shelter', 'other',
    ];

    public function __construct(private readonly ?string $addedBy = null) {}

    /**
     * Parse the CSV at $path and insert valid rows. Returns a
     * LandmarkImportResult with imported/skipped counts and per-row
     * errors keyed to the operator's CSV line numbers.
     */
    public function import(string $path): LandmarkImportResult
    {
        $handle = @fopen($path, 'r');
        if ($handle === false) {
            return new LandmarkImportResult(
                errors: [['row' => 0, 'reason' => 'Could not open CSV file.']],
            );
        }

        try {
            $header = $this->readHeader($handle);
            if ($header === null) {
                return new LandmarkImportResult(
                    errors: [['row' => 1, 'reason' => 'Missing or invalid header row. Expected: '.implode(', ', self::REQUIRED_HEADERS)],
                    ],
                );
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];
            $rowNumber = 1;

            while (($columns = fgetcsv($handle)) !== false) {
                $rowNumber++;
                if ($columns === [null] || $columns === []) {
                    continue;
                }

                $row = @array_combine($header, $columns);
                if ($row === false) {
                    $skipped++;
                    $errors[] = ['row' => $rowNumber, 'reason' => 'Wrong number of columns.'];

                    continue;
                }

                $error = $this->validateRow($row);
                if ($error !== null) {
                    $skipped++;
                    $errors[] = ['row' => $rowNumber, 'reason' => $error];

                    continue;
                }

                $crisis = Crisis::where('slug', trim($row['crisis_slug']))->first();
                if (! $crisis) {
                    $skipped++;
                    $errors[] = ['row' => $rowNumber, 'reason' => "Crisis slug '{$row['crisis_slug']}' not found."];

                    continue;
                }

                Landmark::create([
                    'crisis_id' => $crisis->id,
                    'name' => trim($row['name']),
                    'type' => trim($row['type']),
                    'latitude' => (float) $row['latitude'],
                    'longitude' => (float) $row['longitude'],
                    'added_by' => $this->addedBy,
                ]);
                $imported++;
            }

            return new LandmarkImportResult(
                imported: $imported,
                skipped: $skipped,
                errors: $errors,
            );
        } finally {
            fclose($handle);
        }
    }

    /**
     * @return array<int, string>|null Header column names in CSV order, or null if invalid.
     */
    private function readHeader($handle): ?array
    {
        $header = fgetcsv($handle);
        if ($header === false || $header === []) {
            return null;
        }

        // Strip UTF-8 BOM from the first column if present (Excel exports ship it).
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        $normalised = array_map(fn ($h) => strtolower(trim($h)), $header);

        if (array_diff(self::REQUIRED_HEADERS, $normalised) !== []) {
            return null;
        }

        return $normalised;
    }

    /**
     * @param  array<string, string>  $row
     */
    private function validateRow(array $row): ?string
    {
        if (trim($row['name'] ?? '') === '') {
            return 'Missing landmark name.';
        }

        if (! in_array(trim($row['type'] ?? ''), self::VALID_TYPES, true)) {
            return 'Invalid type. Allowed: '.implode(', ', self::VALID_TYPES).'.';
        }

        if (! is_numeric($row['latitude'] ?? null)) {
            return 'Latitude is not a number.';
        }
        $lat = (float) $row['latitude'];
        if ($lat < -90 || $lat > 90) {
            return 'Latitude out of range (-90 to 90).';
        }

        if (! is_numeric($row['longitude'] ?? null)) {
            return 'Longitude is not a number.';
        }
        $lng = (float) $row['longitude'];
        if ($lng < -180 || $lng > 180) {
            return 'Longitude out of range (-180 to 180).';
        }

        if (trim($row['crisis_slug'] ?? '') === '') {
            return 'Missing crisis_slug.';
        }

        return null;
    }
}
