<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Tiers extends Component
{
    public function getSubscriptionTiersProperty(): array
    {
        return config('stripe.subscription_tiers');
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
            // Create Stripe checkout session for subscription
            $session = $user->checkout([
                $tier['stripe_price_id'] => 1,
            ], [
                'success_url' => route('dashboard').'?subscription_success=true&tier='.$tierId,
                'cancel_url' => route('settings.subscription').'?subscription_cancelled=true',
                'mode' => 'subscription',
                'metadata' => [
                    'tier_id' => $tierId,
                    'user_id' => $user->id,
                ],
            ]);

            // Redirect to Stripe Checkout
            return redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to process subscription. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.tiers');
    }
}
