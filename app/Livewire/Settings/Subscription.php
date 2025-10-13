<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Subscription extends Component
{
    public bool $showCancelConfirmation = false;

    public function getSubscriptionTiersProperty(): array
    {
        return config('stripe.subscription_tiers');
    }

    public function getCurrentSubscriptionProperty()
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        if ($user->subscribed('default')) {
            return $user->subscription('default');
        }

        return null;
    }

    public function getCurrentTierProperty(): ?array
    {
        if (! $this->currentSubscription) {
            return null;
        }

        $stripePriceId = $this->currentSubscription->items->first()?->stripe_price;

        if (! $stripePriceId) {
            return null;
        }

        return collect($this->subscriptionTiers)->firstWhere('stripe_price_id', $stripePriceId);
    }

    public function getIsOnTrialProperty(): bool
    {
        $user = Auth::user();

        return $user && $user->onTrial('default');
    }

    public function getTrialEndsAtProperty(): ?string
    {
        $user = Auth::user();
        if (! $user || ! $user->onTrial('default')) {
            return null;
        }

        $subscription = $user->subscription('default');

        return $subscription?->trial_ends_at?->format('M j, Y');
    }

    public function getTrialDaysRemainingProperty(): int
    {
        $user = Auth::user();
        if (! $user || ! $user->onTrial('default')) {
            return 0;
        }

        $subscription = $user->subscription('default');
        if (! $subscription || ! $subscription->trial_ends_at) {
            return 0;
        }

        // Use ceiling to round up partial days - gives users the full trial experience
        return max(0, (int) ceil(now()->diffInDays($subscription->trial_ends_at, false)));
    }

    public function subscribeTo(string $tierId)
    {
        $tier = collect($this->subscriptionTiers)->firstWhere('id', $tierId);

        if (! $tier) {
            session()->flash('error', 'Invalid subscription tier selected.');

            return;
        }

        $user = Auth::user();

        try {
            // If user has an active subscription, swap the plan instead of creating new
            if ($user->subscribed('default')) {
                $currentTier = $this->currentTier;
                $subscription = $user->subscription('default');
                $subscription->swap($tier['stripe_price_id']);

                // Handle credit allocation for upgrade/downgrade
                $creditDifference = $this->adjustCreditsForTierChange($currentTier, $tier);

                $actionText = $this->getUpgradeActionText($currentTier, $tier);
                $creditMessage = $this->getCreditChangeMessage($creditDifference);
                session()->flash('success', "Successfully {$actionText} to {$tier['name']}!{$creditMessage}");

                return $this->redirect(route('settings.subscription'), navigate: true);
            }

            // Create Stripe checkout session for new subscription with 7-day trial
            $session = $user->checkout([
                $tier['stripe_price_id'] => 1,
            ], [
                'success_url' => route('settings.subscription').'?subscription_success=true&tier='.$tierId,
                'cancel_url' => route('settings.subscription').'?subscription_cancelled=true',
                'mode' => 'subscription',
                'subscription_data' => [
                    'trial_period_days' => 7,
                ],
                'metadata' => [
                    'tier_id' => $tierId,
                    'user_id' => $user->id,
                ],
            ]);

            // Redirect to Stripe Checkout
            return redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to process subscription. Please try again. Error: '.$e->getMessage());
        }
    }

    public function confirmCancelSubscription(): void
    {
        $this->showCancelConfirmation = true;
    }

    public function cancelSubscription()
    {
        $user = Auth::user();

        try {
            if ($user->subscribed('default')) {
                $subscription = $user->subscription('default');
                $subscription->cancel();

                $endsAt = $subscription->ends_at->format('F j, Y');
                session()->flash('success', "Subscription cancelled successfully. You can continue using your plan until {$endsAt}.");
            } else {
                session()->flash('error', 'No active subscription found to cancel.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to cancel subscription. Please try again or contact support.');
        }

        $this->showCancelConfirmation = false;
    }

    public function resumeSubscription()
    {
        $user = Auth::user();

        try {
            $subscription = $user->subscription('default');

            if ($subscription && $subscription->canceled()) {
                $subscription->resume();
                session()->flash('success', 'Subscription resumed successfully. Your plan will continue as normal.');
            } else {
                session()->flash('error', 'No cancelled subscription found to resume.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to resume subscription. Please try again or contact support.');
        }
    }

    public function grantSubscriptionCredits()
    {
        $user = Auth::user();

        if (! $user->subscribed()) {
            session()->flash('error', 'No active subscription found.');

            return;
        }

        $currentTier = $this->currentTier;
        if (! $currentTier) {
            session()->flash('error', 'Unable to determine subscription tier.');

            return;
        }

        try {
            $creditsToAdd = $currentTier['credits_included'];
            $userCredits = $user->credits;
            $userCredits->addCredits($creditsToAdd);

            session()->flash('success', "Added {$creditsToAdd} credits for your {$currentTier['name']} subscription!");
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to grant subscription credits. Please contact support.');
        }
    }

    public function render()
    {
        return view('livewire.settings.subscription');
    }

    /**
     * Adjust user credits when changing subscription tiers by adding/removing the difference
     */
    protected function adjustCreditsForTierChange(?array $currentTier, array $newTier): int
    {
        $user = Auth::user();
        $credits = $user->credits;

        if (! $currentTier) {
            // First subscription - add the full tier allocation
            $credits->addCredits($newTier['credits_included']);

            return $newTier['credits_included'];
        }

        // Calculate the difference between tier allocations
        $creditDifference = $newTier['credits_included'] - $currentTier['credits_included'];

        if ($creditDifference > 0) {
            // Upgrade - add the difference
            $credits->addCredits($creditDifference);
        } elseif ($creditDifference < 0) {
            // Downgrade - remove credits (but don't go below 0)
            $creditsToRemove = min(abs($creditDifference), $credits->balance());
            $credits->useCredits($creditsToRemove);

            // Return the actual amount removed (might be less than the difference)
            return -$creditsToRemove;
        }

        return $creditDifference;
    }

    /**
     * Get the appropriate action text for subscription changes
     */
    protected function getUpgradeActionText(?array $currentTier, array $newTier): string
    {
        if (! $currentTier) {
            return 'subscribed';
        }

        $currentPrice = $currentTier['price'];
        $newPrice = $newTier['price'];

        if ($newPrice > $currentPrice) {
            return 'upgraded';
        } elseif ($newPrice < $currentPrice) {
            return 'downgraded';
        } else {
            return 'changed';
        }
    }

    /**
     * Get the credit change message for subscription updates
     */
    protected function getCreditChangeMessage(int $creditDifference): string
    {
        if ($creditDifference > 0) {
            return " You've received {$creditDifference} additional credits!";
        } elseif ($creditDifference < 0) {
            $creditsRemoved = abs($creditDifference);

            return " {$creditsRemoved} credits have been removed from your account.";
        }

        return ' Your credit balance remains unchanged.';
    }
}
