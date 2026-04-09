<?php

use App\Jobs\ArchiveCrisisData;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new ArchiveCrisisData)->dailyAt('02:00')->name('archive-crisis-data');
