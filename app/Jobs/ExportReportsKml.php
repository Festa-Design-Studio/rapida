<?php

namespace App\Jobs;

use App\Enums\DamageLevel;
use App\Models\DamageReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ExportReportsKml implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $crisisId,
        public ?string $damageFilter = null,
    ) {}

    public function handle(): string
    {
        $query = DamageReport::where('crisis_id', $this->crisisId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($this->damageFilter) {
            $query->where('damage_level', $this->damageFilter);
        }

        $reports = $query->get();

        $placemarks = '';
        foreach ($reports as $r) {
            $damageLevel = $r->damage_level instanceof DamageLevel ? $r->damage_level->value : $r->damage_level;
            $name = htmlspecialchars("Report #{$r->id} - {$damageLevel}", ENT_XML1, 'UTF-8');
            $description = htmlspecialchars(
                "Infrastructure: {$r->infrastructure_type}, Submitted: {$r->submitted_at}",
                ENT_XML1,
                'UTF-8',
            );
            $lng = (float) $r->longitude;
            $lat = (float) $r->latitude;

            $placemarks .= <<<XML
        <Placemark>
          <name>{$name}</name>
          <description>{$description}</description>
          <Point><coordinates>{$lng},{$lat},0</coordinates></Point>
        </Placemark>

XML;
        }

        $kml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
  <Document>
    <name>RAPIDA Damage Reports</name>
{$placemarks}  </Document>
</kml>
XML;

        $filename = 'exports/rapida-reports-'.now()->format('Y-m-d-His').'.kml';
        Storage::put($filename, $kml);

        return $filename;
    }
}
