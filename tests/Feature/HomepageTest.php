<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays dual pricing model messaging', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSee('Monthly Plans or Pay-as-You-Go')
        ->assertSee('Choose monthly subscription plans')
        ->assertSee('purchase credit packages')
        ->assertSee('Monthly Plans')
        ->assertSee('Pay-as-You-Go');
});

it('shows correct CTA options for guests', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSee('Start 7-Day Free Trial')
        ->assertSee('View Credit Packages');
});

it('shows homepage sections correctly', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSee('Remove Backgrounds')
        ->assertSee('Instantly')
        ->assertSee('7-day free trial on all plans')
        ->assertSee('Professional background removal');
});
