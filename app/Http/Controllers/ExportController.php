<?php

namespace App\Http\Controllers;

use App\Jobs\ExportReportsCsv;
use App\Jobs\ExportReportsGeoJson;
use App\Jobs\ExportReportsGpkg;
use App\Jobs\ExportReportsKml;
use App\Jobs\ExportReportsPdf;
use App\Jobs\ExportReportsShapefile;
use App\Models\Crisis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function csv(Request $request): StreamedResponse
    {
        $crisis = $this->resolveCrisis($request);

        $job = new ExportReportsCsv(
            crisisId: $crisis->id,
            damageFilter: $request->query('damage_level'),
            startDate: $request->query('start_date'),
            endDate: $request->query('end_date'),
        );

        return $this->streamFile($job->handle(), 'text/csv');
    }

    public function geojson(Request $request): StreamedResponse
    {
        $crisis = $this->resolveCrisis($request);

        $job = new ExportReportsGeoJson(
            crisisId: $crisis->id,
            damageFilter: $request->query('damage_level'),
        );

        return $this->streamFile($job->handle(), 'application/geo+json');
    }

    public function kml(Request $request): StreamedResponse
    {
        $crisis = $this->resolveCrisis($request);

        $job = new ExportReportsKml(
            crisisId: $crisis->id,
            damageFilter: $request->query('damage_level'),
        );

        return $this->streamFile($job->handle(), 'application/vnd.google-earth.kml+xml');
    }

    public function shapefile(Request $request): StreamedResponse
    {
        $crisis = $this->resolveCrisis($request);

        $job = new ExportReportsShapefile(
            crisisId: $crisis->id,
            damageFilter: $request->query('damage_level'),
        );

        return $this->streamFile($job->handle(), 'application/zip');
    }

    public function pdf(Request $request): StreamedResponse
    {
        $crisis = $this->resolveCrisis($request);

        $job = new ExportReportsPdf(
            crisisId: $crisis->id,
            damageFilter: $request->query('damage_level'),
        );

        return $this->streamFile($job->handle(), 'application/pdf');
    }

    /**
     * Resolve the target crisis from the request: explicit ?crisis_slug= takes
     * precedence; otherwise fall back to the most recently created active crisis.
     * 404s if the requested slug does not match an active crisis, or if no
     * active crisis exists at all.
     */
    private function resolveCrisis(Request $request): Crisis
    {
        $slug = $request->query('crisis_slug');

        if ($slug !== null) {
            return Crisis::where('slug', $slug)
                ->where('status', 'active')
                ->firstOrFail();
        }

        return Crisis::where('status', 'active')
            ->latest('created_at')
            ->firstOrFail();
    }

    private function streamFile(string $filename, string $contentType): StreamedResponse
    {
        return response()->streamDownload(function () use ($filename) {
            echo Storage::get($filename);
        }, basename($filename), ['Content-Type' => $contentType]);
    }

    public function gpkg(Request $request): StreamedResponse
    {
        $crisis = Crisis::where('status', 'active')->firstOrFail();

        $job = new ExportReportsGpkg(
            crisisId: $crisis->id,
            damageFilter: $request->query('damage_level'),
        );
        $filename = $job->handle();

        return response()->streamDownload(function () use ($filename) {
            echo Storage::get($filename);
        }, basename($filename), ['Content-Type' => 'application/geopackage+sqlite3']);
    }
}
