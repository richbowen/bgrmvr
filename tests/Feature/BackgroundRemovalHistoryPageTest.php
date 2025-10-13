<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can access background removal history page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('background-removal.history'));

    $response->assertStatus(200);
    $response->assertSeeLivewire('background-removal-history');
});
