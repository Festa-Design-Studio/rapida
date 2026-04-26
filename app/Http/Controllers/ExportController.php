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
        $crisis = Crisis::where('status', 'active')->firstOrFail();

        $job = new ExportReportsCsv(
            crisisId: $crisis->id,
            damageFilter: $request->query('damage_level'),
            startDate: $request->query('start_date'),
            endDate: $request->query('end_date'),
        );
        $filename = $job->handle();

        return response()->streamDownload(function () use ($filename) {
            echo Storage::get($filename);
        }, basename($filename), ['Content-Type' => 'text/csv']);
    }

    public function geojson(Request $request): StreamedResponse
    {
        $crisis = Crisis::where('status', 'active')->firstOrFail();

        $job = new ExportReportsGeoJson(
            crisisId: $crisis->id,
            damageFilter: $request->query('damage_level'),
        );
        $filename = $job->handle();

        return response()->streamDownload(function () use ($filename) {
            echo Storage::get($filename);
        }, basename($filename), ['Content-Type' => 'application/geo+json']);
    }

    public function kml(Request $request): StreamedResponse
    {
        $crisis = Crisis::where('status', 'active')->firstOrFail();

        $job = new ExportReportsKml(
            crisisId: $crisis->id,
            damageFilter: $request->query('damage_level'),
        );
        $filename = $job->handle();

        return response()->streamDownload(function () use ($filename) {
            echo Storage::get($filename);
        }, basename($filename), ['Content-Type' => 'application/vnd.google-earth.kml+xml']);
    }

    public function shapefile(Request $request): StreamedResponse
    {
        $crisis = Crisis::where('status', 'active')->firstOrFail();

        $job = new ExportReportsShapefile(
            crisisId: $crisis->id,
            damageFilter: $request->query('damage_level'),
        );
        $filename = $job->handle();

        return response()->streamDownload(function () use ($filename) {
            echo Storage::get($filename);
        }, basename($filename), ['Content-Type' => 'application/zip']);
    }

    public function pdf(Request $request): StreamedResponse
    {
        $crisis = Crisis::where('status', 'active')->firstOrFail();

        $job = new ExportReportsPdf(
            crisisId: $crisis->id,
            damageFilter: $request->query('damage_level'),
        );
        $filename = $job->handle();

        return response()->streamDownload(function () use ($filename) {
            echo Storage::get($filename);
        }, basename($filename), ['Content-Type' => 'application/pdf']);
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
