<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'credits',
        'total_purchased',
        'total_used',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'total_purchased' => 'integer',
            'total_used' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Add credits to the user's account
     */
    public function addCredits(int $amount): void
    {
        $this->increment('credits', $amount);
        $this->increment('total_purchased', $amount);
    }

    /**
     * Use credits from the user's account
     */
    public function useCredits(int $amount): bool
    {
        if ($this->credits < $amount) {
            return false;
        }

        $this->decrement('credits', $amount);
        $this->increment('total_used', $amount);

        return true;
    }

    /**
     * Check if user has enough credits
     */
    public function hasCredits(int $amount): bool
    {
        return $this->credits >= $amount;
    }

    /**
     * Get the current credit balance
     */
    public function balance(): int
    {
        return $this->credits;
    }

    /**
     * Set the exact credit amount (useful for subscription tier changes)
     * This method updates total_purchased to maintain accurate accounting
     */
    public function setCredits(int $amount): void
    {
        $actualUsage = $this->user->backgroundRemovals()->count();

        $this->update([
            'credits' => $amount,
            'total_purchased' => $amount + $actualUsage,
            'total_used' => $actualUsage,
        ]);
    }

    /**
     * Recalculate and fix credit totals based on actual usage
     */
    public function recalculateTotals(): void
    {
        $actualUsage = $this->user->backgroundRemovals()->count();

        // Calculate what total_purchased should be based on current credits + actual usage
        $calculatedPurchased = $this->credits + $actualUsage;

        $this->update([
            'total_used' => $actualUsage,
            'total_purchased' => $calculatedPurchased,
        ]);
    }
}
