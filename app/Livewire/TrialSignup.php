<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TrialSignup extends Component
{
    public string $tierId = '';

    public function mount(?string $tier = null)
    {
        // Default to Professional tier if none specified
        $this->tierId = $tier ?? 'professional';

        // If user is not authenticated, redirect to register with intent to signup for trial
        if (! Auth::check()) {
            return redirect()->route('register', ['trial' => $this->tierId]);
        }

        // If authenticated, immediately start the trial signup process
        $this->startTrial();
    }

    public function startTrial()
    {
        $user = Auth::user();

        // Get tier configuration
        $tiers = config('stripe.subscription_tiers');
        $tier = collect($tiers)->firstWhere('id', $this->tierId);

        if (! $tier) {
            session()->flash('error', 'Invalid subscription tier selected.');

            return redirect()->route('settings.subscription');
        }

        try {
            // Create Stripe checkout session for subscription with trial
            $session = $user->checkout([
                $tier['stripe_price_id'] => 1,
            ], [
                'success_url' => route('dashboard').'?subscription_success=true&tier='.$this->tierId,
                'cancel_url' => route('home').'?trial_cancelled=true',
                'mode' => 'subscription',
                'subscription_data' => [
                    'trial_period_days' => 7,
                ],
                'metadata' => [
                    'tier_id' => $this->tierId,
                    'user_id' => $user->id,
                    'source' => 'homepage_trial',
                ],
            ]);

            // Redirect to Stripe Checkout
            return redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to process trial signup. Please try again.');

            return redirect()->route('settings.subscription');
        }
    }

    public function render()
    {
        return view('livewire.trial-signup');
    }
}
