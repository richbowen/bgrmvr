<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Credits extends Component
{
    public function getCreditPackagesProperty(): array
    {
        return config('stripe.credit_packages');
    }

    public function getCurrentCreditsProperty(): int
    {
        $user = Auth::user();

        if (! $user) {
            return 0;
        }

        return $user->credits->credits ?? 0;
    }

    public function purchaseCredits(string $packageId)
    {
        $package = collect($this->creditPackages)->firstWhere('id', $packageId);

        if (! $package) {
            session()->flash('error', 'Invalid package selected.');

            return;
        }

        $user = Auth::user();

        try {
            // Create Stripe checkout session
            $session = $user->checkout([
                $package['stripe_price_id'] => 1,
            ], [
                'success_url' => route('settings.credits').'?checkout_success=true&package='.$packageId,
                'cancel_url' => route('settings.credits').'?checkout_cancelled=true',
                'metadata' => [
                    'package_id' => $packageId,
                    'credits' => $package['credits'],
                    'user_id' => $user->id,
                ],
            ]);

            // Redirect to Stripe Checkout
            return redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to process payment. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.settings.credits');
    }
}
