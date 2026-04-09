<?php

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

arch('models use UUIDs')
    ->expect('App\Models')
    ->toUse(HasUuids::class)
    ->ignoring('App\Models\User');

arch('jobs implement ShouldQueue')
    ->expect('App\Jobs')
    ->toImplement(ShouldQueue::class);

arch('controllers do not use DB facade directly')
    ->expect('App\Http\Controllers')
    ->not->toUse('Illuminate\Support\Facades\DB')
    ->ignoring('App\Http\Controllers\Api\ApiBuildingController');

arch('services do not depend on request')
    ->expect('App\Services')
    ->not->toUse('Illuminate\Http\Request');

arch('enums are string-backed')
    ->expect('App\Enums')
    ->toBeEnums();

arch('no env() calls outside config files')
    ->expect('App')
    ->not->toUse('env');
