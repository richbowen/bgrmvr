<?php

declare(strict_types=1);

use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('subscription settings page renders correctly', function () {
    $this->actingAs($this->user)
        ->get(route('settings.subscription'))
        ->assertSuccessful()
        ->assertSeeLivewire('settings.subscription')
        ->assertSee('Subscription')
        ->assertSee('Current Plan');
});

test('credits settings page renders correctly', function () {
    $this->actingAs($this->user)
        ->get(route('settings.credits'))
        ->assertSuccessful()
        ->assertSeeLivewire('settings.credits')
        ->assertSee('Credits')
        ->assertSee('Current Balance');
});

test('subscription component can load subscription tiers', function () {
    Livewire::test(\App\Livewire\Settings\Subscription::class)
        ->assertSet('subscriptionTiers', fn ($tiers) => count($tiers) > 0)
        ->assertSee('Starter');
});

test('credits component can load credit packages', function () {
    Livewire::test(\App\Livewire\Settings\Credits::class)
        ->assertSet('creditPackages', fn ($packages) => count($packages) > 0)
        ->assertSee('10 Credits');
});
