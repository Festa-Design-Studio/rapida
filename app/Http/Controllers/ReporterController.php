<?php

namespace App\Http\Controllers;

use App\Models\Crisis;
use Illuminate\View\View;

class ReporterController extends Controller
{
    public function show(Crisis $crisis): View
    {
        return view('templates.crisis-wizard', compact('crisis'));
    }
}
