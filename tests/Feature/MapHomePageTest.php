<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the Report Damage button horizontally centered at the bottom of the map', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('left-1/2 -translate-x-1/2', escape: false);
    $response->assertSee('bottom-36', escape: false);
    $response->assertSee('sm:bottom-2.5', escape: false);
    $response->assertSee(__('rapida.report_damage'));
});
