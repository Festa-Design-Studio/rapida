<?php

namespace App\Jobs;

use App\Enums\DamageLevel;
use App\Models\DamageReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Shapefile\Geometry\Point;
use Shapefile\Shapefile;
use Shapefile\ShapefileWriter;
use ZipArchive;

class ExportReportsShapefile implements ShouldQueue
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

        $tempDir = storage_path('app/exports/shapefile-'.now()->format('Y-m-d-His'));
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $shpPath = $tempDir.'/rapida-reports.shp';

        $shapefile = new ShapefileWriter($shpPath);
        $shapefile->setShapeType(Shapefile::SHAPE_TYPE_POINT);
        $shapefile->addField('REPORT_ID', Shapefile::DBF_TYPE_CHAR, 40, 0);
        $shapefile->addField('DMG_LEVEL', Shapefile::DBF_TYPE_CHAR, 20, 0);
        $shapefile->addField('INFRA_TYPE', Shapefile::DBF_TYPE_CHAR, 50, 0);
        $shapefile->addField('SUBMIT_AT', Shapefile::DBF_TYPE_CHAR, 30, 0);
        $shapefile->addField('COMPLETE', Shapefile::DBF_TYPE_NUMERIC, 5, 0);
        $shapefile->addField('AI_CONF', Shapefile::DBF_TYPE_FLOAT, 10, 4);

        foreach ($reports as $r) {
            $damageLevel = $r->damage_level instanceof DamageLevel ? $r->damage_level->value : $r->damage_level;

            $point = new Point((float) $r->longitude, (float) $r->latitude);
            $point->setData('REPORT_ID', (string) $r->id);
            $point->setData('DMG_LEVEL', (string) ($damageLevel ?? ''));
            $point->setData('INFRA_TYPE', (string) ($r->infrastructure_type ?? ''));
            $point->setData('SUBMIT_AT', (string) ($r->submitted_at ?? ''));
            $point->setData('COMPLETE', (int) ($r->completeness_score ?? 0));
            $point->setData('AI_CONF', (float) ($r->ai_confidence ?? 0));

            $shapefile->writeRecord($point);
        }

        $shapefile = null;

        // Write PRJ after closing ShapefileWriter to avoid it being overwritten
        $prjContent = 'GEOGCS["GCS_WGS_1984",DATUM["D_WGS_1984",SPHEROID["WGS_1984",6378137.0,298.257223563]],PRIMEM["Greenwich",0.0],UNIT["Degree",0.0174532925199433]]';
        file_put_contents($tempDir.'/rapida-reports.prj', $prjContent);

        $zipFilename = 'exports/rapida-reports-'.now()->format('Y-m-d-His').'.zip';
        $zipPath = storage_path('app/'.$zipFilename);

        if (! is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach (glob($tempDir.'/rapida-reports.*') as $file) {
            $zip->addFile($file, basename($file));
        }

        $zip->close();

        // Clean up temp shapefile directory
        foreach (glob($tempDir.'/*') as $file) {
            unlink($file);
        }
        rmdir($tempDir);

        return $zipFilename;
    }
}
