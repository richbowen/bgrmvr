<?php

declare(strict_types=1);

use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('user can see no subscription message when not subscribed', function () {
    Livewire::test('settings.subscription')
        ->assertSee('No Active Subscription')
        ->assertSee('Free')
        ->assertSee('Choose a Plan');
});

test('subscription cancel confirmation modal can be opened and closed', function () {
    Livewire::test('settings.subscription')
        ->assertSet('showCancelConfirmation', false)
        ->call('confirmCancelSubscription')
        ->assertSet('showCancelConfirmation', true)
        ->set('showCancelConfirmation', false)
        ->assertSet('showCancelConfirmation', false);
});

test('displays subscription tiers from config', function () {
    config([
        'stripe.subscription_tiers' => [
            [
                'id' => 'starter',
                'name' => 'Starter Plan',
                'price' => 10,
                'features' => ['100 credits/month'],
                'stripe_price_id' => 'price_test123',
                'description' => 'Perfect for getting started',
                'credits_included' => 100,
            ],
        ],
    ]);

    Livewire::test('settings.subscription')
        ->assertSee('Starter Plan')
        ->assertSee('$10')
        ->assertSee('Perfect for getting started')
        ->assertSee('100 credits included');
});

test('shows correct button text for current plan', function () {
    config([
        'stripe.subscription_tiers' => [
            [
                'id' => 'starter',
                'name' => 'Starter Plan',
                'price' => 10,
                'features' => ['100 credits/month'],
                'stripe_price_id' => 'price_test123',
                'description' => 'Perfect for getting started',
                'credits_included' => 100,
            ],
        ],
    ]);

    Livewire::test('settings.subscription')
        ->assertSee('Subscribe to Starter Plan');
});
