<?php

declare(strict_types=1);

use App\Models\User;

it('correctly identifies unsubscribed users', function () {
    $user = User::factory()->create();

    expect($user->hasActiveSubscription())->toBeFalse()
        ->and($user->isOnTrial())->toBeFalse()
        ->and($user->subscription_status)->toBe('unsubscribed')
        ->and($user->trial_days_remaining)->toBe(0);
});

it('correctly identifies trial users', function () {
    $user = User::factory()->create();

    // Simulate a trial subscription using a valid price ID
    $user->createAsStripeCustomer();
    $subscription = $user->newSubscription('default', 'price_1SHg8K3EscZGQC3LDFMu0b7L') // Basic tier
        ->trialDays(7)
        ->create('pm_card_visa');

    expect($user->fresh()->isOnTrial())->toBeTrue()
        ->and($user->fresh()->subscription_status)->toBe('trial')
        ->and($user->fresh()->trial_days_remaining)->toBeGreaterThan(0);
});

it('prioritizes trial status over subscription status', function () {
    $user = User::factory()->create();

    // Create a subscription with trial using a valid price ID
    $user->createAsStripeCustomer();
    $subscription = $user->newSubscription('default', 'price_1SHg8K3EscZGQC3LDFMu0b7L') // Basic tier
        ->trialDays(7)
        ->create('pm_card_visa');

    // Even though they have a subscription, they should show as trial
    expect($user->fresh()->hasActiveSubscription())->toBeTrue()
        ->and($user->fresh()->isOnTrial())->toBeTrue()
        ->and($user->fresh()->subscription_status)->toBe('trial');
});

it('correctly identifies subscribed users after trial ends', function () {
    $user = User::factory()->create();

    // Create a subscription with no trial (simulating ended trial) using a valid price ID
    $user->createAsStripeCustomer();
    $subscription = $user->newSubscription('default', 'price_1SHg8K3EscZGQC3LDFMu0b7L') // Basic tier
        ->create('pm_card_visa');

    expect($user->fresh()->hasActiveSubscription())->toBeTrue()
        ->and($user->fresh()->isOnTrial())->toBeFalse()
        ->and($user->fresh()->subscription_status)->toBe('subscribed');
});

it('returns null tier for unsubscribed users', function () {
    $user = User::factory()->create();

    expect($user->current_subscription_tier)->toBeNull();
});

it('detects current subscription tier correctly', function () {
    $user = User::factory()->create();

    // Create a subscription with a known price ID
    $user->createAsStripeCustomer();
    $subscription = $user->newSubscription('default', 'price_1SHg9L3EscZGQC3L1zsKTDft') // Professional tier
        ->create('pm_card_visa');

    $tier = $user->fresh()->current_subscription_tier;

    expect($tier)->not->toBeNull()
        ->and($tier['name'])->toBe('Professional')
        ->and($tier['credits_included'])->toBe(400);
});

it('detects tier correctly for trial users', function () {
    $user = User::factory()->create();

    // Create a trial subscription
    $user->createAsStripeCustomer();
    $subscription = $user->newSubscription('default', 'price_1SHg8K3EscZGQC3LDFMu0b7L') // Basic tier
        ->trialDays(7)
        ->create('pm_card_visa');

    $user = $user->fresh();

    expect($user->isOnTrial())->toBeTrue()
        ->and($user->subscription_status)->toBe('trial')
        ->and($user->current_subscription_tier)->not->toBeNull()
        ->and($user->current_subscription_tier['name'])->toBe('Basic')
        ->and($user->current_subscription_tier['credits_included'])->toBe(150);
});
