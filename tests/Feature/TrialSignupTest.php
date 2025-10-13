<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects unauthenticated users to register with trial parameter', function () {
    $response = $this->get('/trial-signup/professional');

    $response->assertRedirect('/register?trial=professional');
});

it('starts trial for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/trial-signup/professional');

    // Should redirect to Stripe (external URL)
    $response->assertStatus(302);

    // Check the redirect location contains Stripe
    $location = $response->headers->get('Location');
    expect($location)->toContain('checkout.stripe.com');
});

it('homepage trial buttons link to trial signup', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSee('href="'.route('trial-signup', 'starter').'"', false)
        ->assertSee('href="'.route('trial-signup', 'basic').'"', false)
        ->assertSee('href="'.route('trial-signup', 'professional').'"', false)
        ->assertSee('href="'.route('trial-signup', 'business').'"', false);
});

it('register page captures trial parameter', function () {
    $response = $this->get('/register?trial=professional');

    $response->assertStatus(200);
});

it('handles invalid tier gracefully', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/trial-signup/invalid-tier');

    $response->assertRedirect('/settings/subscription');
    $response->assertSessionHas('error', 'Invalid subscription tier selected.');
});
