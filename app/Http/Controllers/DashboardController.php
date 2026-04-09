<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function field(): View
    {
        return view('dashboard.field');
    }

    public function analyst(): View
    {
        return view('dashboard.analyst');
    }
}
