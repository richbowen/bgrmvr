<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Subscription')" :subheading="__('Manage your subscription and billing')">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <div class="flex items-center">
                    <flux:icon name="check-circle" class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" />
                    <p class="text-sm text-green-700 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center">
                    <flux:icon name="exclamation-circle" class="h-5 w-5 text-red-500 mr-3 flex-shrink-0" />
                    <p class="text-sm text-red-700 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Current Subscription Status -->
        @if ($this->currentSubscription)
            <div class="mb-8 p-6 bg-slate-50 dark:bg-slate-800 rounded-lg border">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Current Plan</h3>

                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">
                            {{ $this->currentTier['name'] ?? 'Current Plan' }}
                        </p>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            ${{ $this->currentTier['price'] ?? '0' }}/month • {{ $this->currentTier['credits_included'] ?? '0' }} credits/month
                        </p>
                    </div>

                    <div class="text-right">
                        @if ($this->currentSubscription->canceled())
                            <flux:badge variant="warning">Cancelled</flux:badge>
                            <p class="text-xs text-slate-500 mt-1">
                                Ends {{ $this->currentSubscription->ends_at->format('M d, Y') }}
                            </p>
                        @elseif ($this->currentSubscription->onGracePeriod())
                            <flux:badge variant="warning">Cancelled</flux:badge>
                            <p class="text-xs text-slate-500 mt-1">
                                Active until {{ $this->currentSubscription->ends_at->format('M d, Y') }}
                            </p>
                        @elseif ($this->isOnTrial)
                            <flux:badge variant="info">Free Trial</flux:badge>
                            <p class="text-xs text-slate-500 mt-1">
                                Trial ends {{ $this->trialEndsAt }}
                                <br>
                                <span class="font-medium">{{ $this->trialDaysRemaining }} days remaining</span>
                            </p>
                        @else
                            <flux:badge variant="success">Active</flux:badge>
                            <p class="text-xs text-slate-500 mt-1">
                                @php
                                    $currentPeriodEnd = $this->currentSubscription->asStripeSubscription()->current_period_end ?? null;
                                @endphp
                                @if ($currentPeriodEnd)
                                    Next billing: {{ \Carbon\Carbon::createFromTimestamp($currentPeriodEnd)->format('M d, Y') }}
                                @else
                                    Active subscription
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex gap-3">
                    @if ($this->currentSubscription->canceled())
                        <flux:button wire:click="resumeSubscription" variant="primary" size="sm">
                            Resume Subscription
                        </flux:button>
                    @else
                        <flux:button wire:click="confirmCancelSubscription" variant="danger" size="sm">
                            Cancel Subscription
                        </flux:button>
                    @endif
                </div>
            </div>
        @else
            <!-- No Active Subscription -->
            <div class="mb-8 p-6 bg-slate-50 dark:bg-slate-800 rounded-lg border">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Current Plan</h3>

                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">
                            No Active Subscription
                        </p>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Choose a plan below to get started
                        </p>
                    </div>

                    <div class="text-right">
                        <flux:badge variant="outline">Free</flux:badge>
                    </div>
                </div>
            </div>
        @endif

        <!-- Available Plans -->
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    @if ($this->currentSubscription)
                        Change Plan
                    @else
                        Choose a Plan
                    @endif
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    @if ($this->currentSubscription)
                        Switch to a different plan that better fits your needs.
                    @else
                        Start with a subscription to get the best value on background removals.
                    @endif
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($this->subscriptionTiers as $tier)
                    <div class="relative p-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg flex flex-col">
                        <div class="mb-4">
                            <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $tier['name'] }}</h4>
                            @if (!empty($tier['description']))
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $tier['description'] }}</p>
                            @endif
                        </div>

                        <div class="mb-4">
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold text-slate-900 dark:text-white">${{ $tier['price'] }}</span>
                                <span class="text-sm text-slate-500 dark:text-slate-400 ml-1">/month</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $tier['credits_included'] }} credits included</p>
                        </div>

                        <ul class="space-y-2 mb-6 flex-grow">
                            @foreach ($tier['features'] as $feature)
                                <li class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                                    <flux:icon name="check" class="h-4 w-4 text-green-500 mr-2 flex-shrink-0" />
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>

                        @php
                            $isCurrentPlan = $this->currentTier && $this->currentTier['id'] === $tier['id'];
                        @endphp

                        <div class="mt-auto">
                            @if ($isCurrentPlan)
                                <flux:button disabled class="w-full">
                                    Current Plan
                                </flux:button>
                            @else
                                <flux:button wire:click="subscribeTo('{{ $tier['id'] }}')" variant="outline" class="w-full">
                                    @if ($this->currentSubscription)
                                        @if ($this->currentTier && $tier['price'] > $this->currentTier['price'])
                                            Upgrade to {{ $tier['name'] }}
                                        @elseif ($this->currentTier && $tier['price'] < $this->currentTier['price'])
                                            Downgrade to {{ $tier['name'] }}
                                        @else
                                            Switch to {{ $tier['name'] }}
                                        @endif
                                    @else
                                        Subscribe to {{ $tier['name'] }}
                                    @endif
                                </flux:button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-settings.layout>

    <!-- Cancel Confirmation Modal -->
    <flux:modal wire:model.self="showCancelConfirmation" name="cancel-subscription" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <div class="flex items-center mb-4">
                    <flux:icon name="exclamation-triangle" class="h-6 w-6 text-red-500 mr-3" />
                    <flux:heading size="lg">Cancel Subscription</flux:heading>
                </div>
                <flux:text>
                    <p>Are you sure you want to cancel your subscription?</p>
                    <p class="mt-2">You'll continue to have access to your current plan until the end of your billing period, after which your account will return to the free plan.</p>
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Keep Subscription</flux:button>
                </flux:modal.close>
                <flux:button wire:click="cancelSubscription" variant="danger">
                    Yes, Cancel Subscription
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>
