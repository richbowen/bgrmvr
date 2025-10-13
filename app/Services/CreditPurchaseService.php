<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class CreditPurchaseService
{
    /**
     * Process a completed credit purchase from Stripe checkout.
     */
    public function processCheckoutSession(array $sessionData): bool
    {
        $metadata = $sessionData['metadata'] ?? [];

        if (! isset($metadata['user_id'], $metadata['credits'], $metadata['package_id'])) {
            return false;
        }

        $user = User::find($metadata['user_id']);
        if (! $user) {
            return false;
        }

        $credits = (int) $metadata['credits'];

        try {
            $userCredits = $user->credits;
            $userCredits->addCredits($credits);

            Log::info('Credits purchased successfully', [
                'user_id' => $user->id,
                'package_id' => $metadata['package_id'],
                'credits_added' => $credits,
                'total_credits' => $userCredits->credits,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to add credits', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
