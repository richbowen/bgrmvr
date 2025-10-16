<?php

declare(strict_types=1);

use App\Listeners\HandleStripeWebhookEvents;
use App\Models\User;
use Laravel\Cashier\Events\WebhookReceived;

beforeEach(function () {
    // Skip these tests if Stripe configuration is not properly set up
    if (! config('cashier.secret') || ! config('cashier.key')) {
        $this->markTestSkipped('Stripe credentials not configured');
    }

    // Also check if the Stripe configuration has actual price IDs (not defaults)
    $tiers = config('stripe.subscription_tiers');
    $hasActualPriceIds = collect($tiers)->contains(function ($tier) {
        return $tier['stripe_price_id'] && str_starts_with($tier['stripe_price_id'], 'price_1');
    });

    if (! $hasActualPriceIds) {
        $this->markTestSkipped('Stripe price IDs not configured with actual values');
    }

    $this->user = User::factory()->create([
        'stripe_id' => 'cus_test123',
    ]);
});

test('grants credits when subscription is created', function () {
    $initialCredits = $this->user->credits->credits;

    // Mock subscription created webhook
    $webhookPayload = [
        'type' => 'customer.subscription.created',
        'data' => [
            'object' => [
                'customer' => 'cus_test123',
                'items' => [
                    'data' => [
                        [
                            'price' => [
                                'id' => 'price_1SHg7f3EscZGQC3Lwre9mGBF', // Starter plan
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $event = new WebhookReceived($webhookPayload);
    $listener = app(HandleStripeWebhookEvents::class);
    $listener->handle($event);

    // Should have added 50 credits (Starter plan)
    expect($this->user->fresh()->credits->credits)->toBe($initialCredits + 50);
});

test('skips initial subscription invoice', function () {
    $initialCredits = $this->user->credits->credits;

    // Mock invoice payment succeeded webhook (initial subscription)
    $webhookPayload = [
        'type' => 'invoice.payment_succeeded',
        'data' => [
            'object' => [
                'customer' => 'cus_test123',
                'subscription' => 'sub_test123',
                'billing_reason' => 'subscription_create', // Initial subscription
            ],
        ],
    ];

    $event = new WebhookReceived($webhookPayload);
    $listener = app(HandleStripeWebhookEvents::class);
    $listener->handle($event);

    // Should NOT have added credits (initial invoice is skipped)
    expect($this->user->fresh()->credits->credits)->toBe($initialCredits);
});

test('can directly add subscription credits', function () {
    // Create a subscription for the user
    $subscription = $this->user->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_test123',
        'stripe_status' => 'active',
        'stripe_price' => 'price_1SHg7f3EscZGQC3Lwre9mGBF', // Starter plan
        'quantity' => 1,
    ]);

    // Also create subscription item
    $subscription->items()->create([
        'stripe_id' => 'si_test123',
        'stripe_product' => 'prod_test123',
        'stripe_price' => 'price_1SHg7f3EscZGQC3Lwre9mGBF',
        'quantity' => 1,
    ]);

    $initialCredits = $this->user->credits->credits;

    // Test the credit granting logic directly
    $tiers = config('stripe.subscription_tiers');
    $tier = collect($tiers)->firstWhere('stripe_price_id', 'price_1SHg7f3EscZGQC3Lwre9mGBF');

    expect($tier)->not->toBeNull();

    $creditsToAdd = $tier['credits_included'];
    $this->user->credits->addCredits($creditsToAdd);

    // Should have added 50 credits (Starter plan)
    expect($this->user->fresh()->credits->credits)->toBe($initialCredits + 50);
});
