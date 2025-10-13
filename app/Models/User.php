<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factory_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's background removals
     */
    public function backgroundRemovals(): HasMany
    {
        return $this->hasMany(BackgroundRemoval::class);
    }

    /**
     * Get the user's credits
     */
    public function credits(): HasOne
    {
        return $this->hasOne(UserCredit::class);
    }

    /**
     * Get or create user credits record
     */
    public function getCreditsAttribute(): UserCredit
    {
        return $this->credits()->firstOrCreate([
            'user_id' => $this->id,
        ], [
            'credits' => 0, // No free credits - users get 7-day trial instead
            'total_purchased' => 0,
            'total_used' => 0,
        ]);
    }

    /**
     * Check if user has an active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscribed('default');
    }

    /**
     * Check if user is on trial
     */
    public function isOnTrial(): bool
    {
        return $this->onTrial('default');
    }

    /**
     * Get subscription status for display
     */
    public function getSubscriptionStatusAttribute(): string
    {
        if ($this->isOnTrial()) {
            return 'trial';
        }

        if ($this->hasActiveSubscription()) {
            return 'subscribed';
        }

        return 'unsubscribed';
    }

    /**
     * Get trial days remaining
     */
    public function getTrialDaysRemainingAttribute(): int
    {
        if (! $this->isOnTrial()) {
            return 0;
        }

        $subscription = $this->subscription('default');
        if (! $subscription?->trial_ends_at) {
            return 0;
        }

        return max(0, (int) ceil(now()->diffInDays($subscription->trial_ends_at, false)));
    }

    /**
     * Get the current subscription tier
     */
    public function getCurrentSubscriptionTierAttribute(): ?array
    {
        if (! $this->hasActiveSubscription()) {
            return null;
        }

        $subscription = $this->subscription('default');
        $stripePriceId = $subscription?->items?->first()?->stripe_price;

        if (! $stripePriceId) {
            return null;
        }

        $tiers = config('stripe.subscription_tiers');

        return collect($tiers)->firstWhere('stripe_price_id', $stripePriceId);
    }
}
