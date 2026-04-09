<?php

namespace App\Console\Commands;

use App\Models\Building;
use App\Models\Crisis;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ImportBuildingFootprints extends Command
{
    protected $signature = 'rapida:import-footprints {crisis_slug} {geojson_path}';

    protected $description = 'Import Microsoft ML Building Footprints GeoJSON into buildings table';

    public function handle(): int
    {
        $crisis = Crisis::where('slug', $this->argument('crisis_slug'))->firstOrFail();
        $path = $this->argument('geojson_path');

        if (! file_exists($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($path), true);
        $featureCount = count($data['features'] ?? []);
        $this->info("Importing {$featureCount} footprints for crisis: {$crisis->name}");

        $bar = $this->output->createProgressBar($featureCount);
        $isPgsql = Schema::getConnection()->getDriverName() === 'pgsql';

        foreach (array_chunk($data['features'], 500) as $chunk) {
            DB::transaction(function () use ($chunk, $crisis, $isPgsql): void {
                foreach ($chunk as $feature) {
                    $id = Str::uuid()->toString();
                    $msId = $feature['properties']['id'] ?? $feature['properties']['ms_building_id'] ?? null;

                    if ($isPgsql) {
                        DB::statement(
                            'INSERT INTO buildings (id, crisis_id, footprint_geom, ms_building_id, created_at, updated_at)
                             VALUES (?, ?, ST_GeomFromGeoJSON(?), ?, NOW(), NOW())
                             ON CONFLICT DO NOTHING',
                            [$id, $crisis->id, json_encode($feature['geometry']), $msId]
                        );
                    } else {
                        Building::create([
                            'crisis_id' => $crisis->id,
                            'ms_building_id' => $msId,
                        ]);
                    }
                }
            });
            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done. '.Building::where('crisis_id', $crisis->id)->count().' buildings in database.');

        return self::SUCCESS;
    }
}
