<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows buy credits button when user has no credits', function () {
    $user = User::factory()->create();

    // Ensure user has 0 credits (this is the default for new users)
    expect($user->credits->credits)->toBe(0);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200)
        ->assertSee('Buy Credits');
});

it('credits page loads without errors', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/credits');

    $response->assertStatus(200)
        ->assertSee('Buy credits to remove backgrounds')
        ->assertSee('Credits never expire');
});

it('credits page displays credit packages', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/credits');

    $response->assertStatus(200)
        ->assertSee('10 Credits')
        ->assertSee('25 Credits')
        ->assertSee('50 Credits')
        ->assertSee('100 Credits');
});

it('buy credits button links to settings credits page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);

    // Check that the Buy Credits button has the correct href
    $response->assertSee('href="'.route('settings.credits').'"', false);
});

it('settings credits page loads without errors', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/settings/credits');

    $response->assertStatus(200)
        ->assertSee('Credits');
});

it('homepage links to trial signup for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200)
        ->assertSee('trial-signup/professional')
        ->assertSee('href="'.route('settings.credits').'"', false);
});
it('homepage links to trial signup for guests', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSee('trial-signup/professional')
        ->assertSee('href="'.route('credits').'"', false);
});
