<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\CreditPurchaseService;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class HandleStripeWebhookEvents
{
    public function __construct(
        private readonly CreditPurchaseService $creditPurchaseService
    ) {}

    /**
     * Handle the webhook received event.
     */
    public function handle(WebhookReceived $event): void
    {
        $webhookType = $event->payload['type'] ?? null;

        match ($webhookType) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event),
            'customer.subscription.created' => $this->handleSubscriptionCreated($event),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event),
            'invoice.payment_succeeded' => $this->handleInvoicePaymentSucceeded($event),
            default => null,
        };
    }

    /**
     * Handle completed checkout sessions for credit purchases.
     */
    private function handleCheckoutSessionCompleted(WebhookReceived $event): void
    {
        $session = $event->payload['data']['object'];

        // Only process if this is a credit purchase (not a subscription)
        if (! isset($session['metadata']['credits'])) {
            return;
        }

        $processed = $this->creditPurchaseService->processCheckoutSession($session);

        if (! $processed) {
            Log::warning('Failed to process credit purchase', [
                'session_id' => $session['id'] ?? null,
                'metadata' => $session['metadata'] ?? [],
            ]);
        }
    }

    /**
     * Handle when a subscription is created - grant initial credits.
     */
    private function handleSubscriptionCreated(WebhookReceived $event): void
    {
        $subscription = $event->payload['data']['object'];
        $customerId = $subscription['customer'] ?? null;

        Log::info('Webhook: customer.subscription.created received', [
            'customer_id' => $customerId,
            'subscription_id' => $subscription['id'] ?? null,
            'status' => $subscription['status'] ?? null,
            'items_count' => count($subscription['items']['data'] ?? []),
            'first_price_id' => $subscription['items']['data'][0]['price']['id'] ?? null,
        ]);

        if (! $customerId) {
            Log::error('Webhook: No customer ID found in subscription created event');

            return;
        }

        $this->grantSubscriptionCredits($customerId, $subscription);
    }

    /**
     * Handle successful invoice payments - grant monthly credits for recurring subscriptions.
     */
    private function handleInvoicePaymentSucceeded(WebhookReceived $event): void
    {
        $invoice = $event->payload['data']['object'];
        $subscriptionId = $invoice['subscription'] ?? null;
        $customerId = $invoice['customer'] ?? null;

        // Only process subscription invoices (not one-time payments)
        if (! $subscriptionId || ! $customerId) {
            return;
        }

        // Skip the first invoice (handled by subscription.created)
        if ($invoice['billing_reason'] === 'subscription_create') {
            return;
        }

        // Grant credits for recurring billing
        $this->grantSubscriptionCredits($customerId, null, $subscriptionId);
    }

    /**
     * Grant credits based on subscription tier.
     */
    private function grantSubscriptionCredits(string $customerId, ?array $subscription = null, ?string $subscriptionId = null): void
    {
        try {
            // Find user by Stripe customer ID
            $user = \App\Models\User::where('stripe_id', $customerId)->first();

            if (! $user) {
                Log::warning('User not found for subscription credit grant', [
                    'customer_id' => $customerId,
                    'subscription_id' => $subscriptionId,
                ]);

                return;
            }

            // Get price ID from subscription or fetch from Stripe
            $priceId = null;
            if ($subscription) {
                $priceId = $subscription['items']['data'][0]['price']['id'] ?? null;
            } elseif ($subscriptionId) {
                // Fetch subscription from Stripe to get price ID
                $stripe = new \Stripe\StripeClient(config('cashier.secret'));
                $stripeSubscription = $stripe->subscriptions->retrieve($subscriptionId);
                $priceId = $stripeSubscription->items->data[0]->price->id ?? null;
            }

            if (! $priceId) {
                Log::warning('No price ID found for subscription credit grant', [
                    'customer_id' => $customerId,
                    'subscription_id' => $subscriptionId,
                ]);

                return;
            }

            // Debug: Log the price ID we're processing
            Log::info('Processing subscription webhook', [
                'customer_id' => $customerId,
                'price_id' => $priceId,
                'subscription_id' => $subscriptionId,
                'subscription_data' => $subscription ? 'present' : 'not_present',
            ]);

            // Find matching tier
            $tiers = config('stripe.subscription_tiers');
            $tier = collect($tiers)->firstWhere('stripe_price_id', $priceId);

            if (! $tier) {
                Log::error('No tier found for price ID - this causes fallback to wrong tier!', [
                    'price_id' => $priceId,
                    'customer_id' => $customerId,
                    'available_tiers' => collect($tiers)->pluck('stripe_price_id')->toArray(),
                    'subscription_id' => $subscriptionId,
                ]);

                return;
            }

            // Log successful tier match
            Log::info('Successfully matched subscription tier', [
                'customer_id' => $customerId,
                'price_id' => $priceId,
                'tier_name' => $tier['name'],
                'credits_to_add' => $tier['credits_included'],
            ]);

            // Check for duplicate processing - prevent granting credits twice for the same subscription
            $subscriptionIdToCheck = $subscription['id'] ?? $subscriptionId;
            if ($subscriptionIdToCheck) {
                // Check if we've already processed this subscription for initial credits
                $existingSubscription = $user->subscriptions()->where('stripe_id', $subscriptionIdToCheck)->first();
                if ($existingSubscription && $subscription) {
                    // This is a subscription_created event, but we already have this subscription
                    Log::info('Skipping duplicate subscription credit grant', [
                        'user_id' => $user->id,
                        'subscription_id' => $subscriptionIdToCheck,
                        'tier' => $tier['name'],
                        'reason' => 'subscription_already_exists',
                    ]);

                    return;
                }
            }

            // Grant credits
            $creditsToAdd = $tier['credits_included'];
            $userCredits = $user->credits;
            $userCredits->addCredits($creditsToAdd);

            Log::info('Subscription credits granted', [
                'user_id' => $user->id,
                'tier' => $tier['name'],
                'credits_added' => $creditsToAdd,
                'total_credits' => $userCredits->credits,
                'event_type' => $subscription ? 'subscription_created' : 'recurring_payment',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to grant subscription credits', [
                'customer_id' => $customerId,
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle when a subscription is deleted/cancelled.
     */
    private function handleSubscriptionDeleted(WebhookReceived $event): void
    {
        $subscription = $event->payload['data']['object'];
        $customerId = $subscription['customer'] ?? null;
        $subscriptionId = $subscription['id'] ?? null;

        if (! $customerId || ! $subscriptionId) {
            return;
        }

        $this->handleTrialCancellation($customerId, $subscriptionId, $subscription);
    }

    /**
     * Handle when a subscription is updated (e.g., trial cancelled).
     */
    private function handleSubscriptionUpdated(WebhookReceived $event): void
    {
        $subscription = $event->payload['data']['object'];
        $customerId = $subscription['customer'] ?? null;
        $subscriptionId = $subscription['id'] ?? null;

        if (! $customerId || ! $subscriptionId) {
            return;
        }

        // Check if this is a trial cancellation
        $status = $subscription['status'] ?? null;
        $cancelAtPeriodEnd = $subscription['cancel_at_period_end'] ?? false;

        if ($cancelAtPeriodEnd && $status === 'trialing') {
            $this->handleTrialCancellation($customerId, $subscriptionId, $subscription);
        }
    }

    /**
     * Handle trial cancellation - reset user to Free tier with no credits.
     */
    private function handleTrialCancellation(string $customerId, string $subscriptionId, array $subscription): void
    {
        try {
            // Find user by Stripe customer ID
            $user = \App\Models\User::where('stripe_id', $customerId)->first();

            if (! $user) {
                Log::warning('User not found for trial cancellation', [
                    'customer_id' => $customerId,
                    'subscription_id' => $subscriptionId,
                ]);

                return;
            }

            // Check if user was on trial
            $localSubscription = $user->subscriptions()->where('stripe_id', $subscriptionId)->first();

            if (! $localSubscription || ! $localSubscription->onTrial()) {
                Log::info('Subscription not on trial, skipping trial cancellation logic', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscriptionId,
                ]);

                return;
            }

            // Reset user credits to 0 (back to Free tier)
            $userCredits = $user->credits;
            $previousCredits = $userCredits->credits;
            $userCredits->setCredits(0);

            Log::info('Trial cancelled - user reset to Free tier', [
                'user_id' => $user->id,
                'subscription_id' => $subscriptionId,
                'previous_credits' => $previousCredits,
                'new_credits' => 0,
                'event_type' => 'trial_cancelled',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle trial cancellation', [
                'customer_id' => $customerId,
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
