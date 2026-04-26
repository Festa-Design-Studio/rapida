<?php

namespace App\Jobs;

use App\Enums\DamageLevel;
use App\Models\DamageReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use PDO;

/**
 * Writes a minimal OGC GeoPackage (.gpkg) file containing damage reports as
 * Point features in EPSG:4326. GPKG is named in UNDP's challenge Q14 as one
 * of the three required standardized export formats. A GeoPackage is a
 * SQLite database with specific application_id and a fixed table schema --
 * we use PDO-SQLite directly to avoid a system-binary runtime dependency
 * (ogr2ogr) that Laravel Cloud may not ship.
 *
 * Spec: https://www.geopackage.org/spec130/
 */
class ExportReportsGpkg implements ShouldQueue
{
    use Queueable;

    /** GPKG application_id: ASCII GPKG as a 4-byte big-endian int (per spec). */
    private const APPLICATION_ID = 0x47504B47;

    /** GPKG user_version: 10300 = version 1.3.0. */
    private const USER_VERSION = 10300;

    public function __construct(
        public string $crisisId,
        public ?string $damageFilter = null,
    ) {}

    public function handle(): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'rapida-gpkg-').'.gpkg';

        $pdo = new PDO('sqlite:'.$tempPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->writeGpkgPragmas($pdo);
        $this->writeRequiredTables($pdo);
        $this->writeFeatureTable($pdo);
        $this->writeFeatures($pdo);

        $pdo = null;

        $filename = 'exports/rapida-reports-'.now()->format('Y-m-d-His').'.gpkg';
        Storage::put($filename, file_get_contents($tempPath));
        unlink($tempPath);

        return $filename;
    }

    private function writeGpkgPragmas(PDO $pdo): void
    {
        $pdo->exec('PRAGMA application_id = '.self::APPLICATION_ID);
        $pdo->exec('PRAGMA user_version = '.self::USER_VERSION);
    }

    /**
     * Three tables required by the GPKG spec: gpkg_spatial_ref_sys,
     * gpkg_contents, gpkg_geometry_columns.
     */
    private function writeRequiredTables(PDO $pdo): void
    {
        $pdo->exec('CREATE TABLE gpkg_spatial_ref_sys (
            srs_name TEXT NOT NULL,
            srs_id INTEGER PRIMARY KEY,
            organization TEXT NOT NULL,
            organization_coordsys_id INTEGER NOT NULL,
            definition TEXT NOT NULL,
            description TEXT
        )');

        $epsg4326 = 'GEOGCS["WGS 84",DATUM["WGS_1984",SPHEROID["WGS 84",6378137,298.257223563,AUTHORITY["EPSG","7030"]],AUTHORITY["EPSG","6326"]],PRIMEM["Greenwich",0,AUTHORITY["EPSG","8901"]],UNIT["degree",0.0174532925199433,AUTHORITY["EPSG","9122"]],AUTHORITY["EPSG","4326"]]';

        $pdo->prepare('INSERT INTO gpkg_spatial_ref_sys VALUES (?, ?, ?, ?, ?, ?)')
            ->execute(['WGS 84', 4326, 'EPSG', 4326, $epsg4326, 'longitude/latitude in WGS 84']);
        $pdo->prepare('INSERT INTO gpkg_spatial_ref_sys VALUES (?, ?, ?, ?, ?, ?)')
            ->execute(['Undefined cartesian SRS', -1, 'NONE', -1, 'undefined', 'placeholder']);
        $pdo->prepare('INSERT INTO gpkg_spatial_ref_sys VALUES (?, ?, ?, ?, ?, ?)')
            ->execute(['Undefined geographic SRS', 0, 'NONE', 0, 'undefined', 'placeholder']);

        $pdo->exec('CREATE TABLE gpkg_contents (
            table_name TEXT NOT NULL PRIMARY KEY,
            data_type TEXT NOT NULL,
            identifier TEXT UNIQUE,
            description TEXT DEFAULT "",
            last_change DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            min_x DOUBLE,
            min_y DOUBLE,
            max_x DOUBLE,
            max_y DOUBLE,
            srs_id INTEGER REFERENCES gpkg_spatial_ref_sys(srs_id)
        )');

        $pdo->exec('CREATE TABLE gpkg_geometry_columns (
            table_name TEXT NOT NULL,
            column_name TEXT NOT NULL,
            geometry_type_name TEXT NOT NULL,
            srs_id INTEGER NOT NULL,
            z TINYINT NOT NULL,
            m TINYINT NOT NULL,
            CONSTRAINT pk_geom_cols PRIMARY KEY (table_name, column_name),
            CONSTRAINT uk_gc_table_name UNIQUE (table_name)
        )');
    }

    private function writeFeatureTable(PDO $pdo): void
    {
        $pdo->exec('CREATE TABLE damage_reports (
            fid INTEGER PRIMARY KEY AUTOINCREMENT,
            geom BLOB,
            report_id TEXT,
            damage_level TEXT,
            infrastructure_type TEXT,
            crisis_type TEXT,
            submitted_at TEXT,
            completeness_score INTEGER,
            ai_confidence REAL,
            submitted_via TEXT
        )');

        $pdo->prepare('INSERT INTO gpkg_contents (table_name, data_type, identifier, description, srs_id) VALUES (?, ?, ?, ?, ?)')
            ->execute(['damage_reports', 'features', 'damage_reports', 'RAPIDA damage reports', 4326]);

        $pdo->prepare('INSERT INTO gpkg_geometry_columns VALUES (?, ?, ?, ?, ?, ?)')
            ->execute(['damage_reports', 'geom', 'POINT', 4326, 0, 0]);
    }

    private function writeFeatures(PDO $pdo): void
    {
        $query = DamageReport::where('crisis_id', $this->crisisId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($this->damageFilter) {
            $query->where('damage_level', $this->damageFilter);
        }

        $insert = $pdo->prepare('INSERT INTO damage_reports
            (geom, report_id, damage_level, infrastructure_type, crisis_type, submitted_at, completeness_score, ai_confidence, submitted_via)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

        $minX = $minY = INF;
        $maxX = $maxY = -INF;

        foreach ($query->cursor() as $r) {
            $lon = (float) $r->longitude;
            $lat = (float) $r->latitude;
            $minX = min($minX, $lon);
            $minY = min($minY, $lat);
            $maxX = max($maxX, $lon);
            $maxY = max($maxY, $lat);

            $damageLevel = $r->damage_level instanceof DamageLevel ? $r->damage_level->value : $r->damage_level;
            $submittedVia = $r->submitted_via?->value ?? (string) $r->submitted_via;

            $insert->execute([
                $this->encodeGpkgPoint($lon, $lat),
                (string) $r->id,
                (string) ($damageLevel ?? ''),
                (string) ($r->infrastructure_type ?? ''),
                (string) ($r->crisis_type ?? ''),
                (string) ($r->submitted_at ?? ''),
                (int) ($r->completeness_score ?? 0),
                $r->ai_confidence !== null ? (float) $r->ai_confidence : null,
                $submittedVia,
            ]);
        }

        if ($minX !== INF) {
            $pdo->prepare('UPDATE gpkg_contents SET min_x=?, min_y=?, max_x=?, max_y=? WHERE table_name=?')
                ->execute([$minX, $minY, $maxX, $maxY, 'damage_reports']);
        }
    }

    /**
     * GeoPackage Binary header (8 bytes) plus standard WKB Point (21 bytes).
     * Header layout per GPKG spec section 2.1.3:
     *   bytes 0-1: magic GP (0x47, 0x50)
     *   byte 2:    version (0x00)
     *   byte 3:    flags (envelope=0, byteorder=little=1 -> 0x01)
     *   bytes 4-7: srs_id as int32 LE (4326)
     * Then WKB Point: byteorder(0x01) + type(uint32 LE = 1) + X(double LE) + Y(double LE)
     */
    private function encodeGpkgPoint(float $lon, float $lat): string
    {
        $header = "\x47\x50\x00\x01".pack('V', 4326);
        $wkb = "\x01".pack('V', 1).pack('e', $lon).pack('e', $lat);

        return $header.$wkb;
    }
}
