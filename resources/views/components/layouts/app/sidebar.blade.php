<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                <flux:navlist.item icon="scissors" :href="route('background-removal.tool')" :current="request()->routeIs('background-removal.tool')" wire:navigate>{{ __('Remove Background') }}</flux:navlist.item>
                <flux:navlist.item icon="clock" :href="route('background-removal.history')" :current="request()->routeIs('background-removal.history')" wire:navigate>{{ __('History') }}</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <!-- Subscription Status Card -->
        @php
            $user = auth()->user();
            $subscriptionStatus = $user->subscription_status;
        @endphp

        @if ($subscriptionStatus === 'unsubscribed')
            <!-- Unsubscribed CTA -->
            <div class="mx-3 mb-4">
                <flux:callout color="orange">
                    <flux:callout.heading icon="exclamation-triangle">No Active Plan</flux:callout.heading>
                    <flux:callout.text>
                        You're currently on the free tier with no monthly credits. Subscribe to unlock unlimited background removal and get monthly credits.
                    </flux:callout.text>
                    <x-slot name="actions">
                        <flux:button variant="primary" size="sm" :href="route('settings.subscription')" wire:navigate class="w-full">
                            View Plans
                        </flux:button>
                    </x-slot>
                </flux:callout>
            </div>
        @elseif($subscriptionStatus === 'trial')
            <!-- Trial Status -->
            <div class="mx-3 mb-4">
                <flux:callout color="blue">
                    <flux:callout.heading icon="clock">Free Trial Active</flux:callout.heading>
                    <flux:callout.text>
                        @php
                            $currentTier = $user->current_subscription_tier;
                            $tierName = $currentTier['name'] ?? 'Premium';
                        @endphp
                        You're trying the {{ $tierName }} tier with {{ $user->trial_days_remaining }} {{ $user->trial_days_remaining === 1 ? 'day' : 'days' }} remaining. Enjoying the experience so far?
                    </flux:callout.text>
                    <x-slot name="actions">
                        <flux:button variant="outline" size="sm" :href="route('settings.subscription')" wire:navigate class="w-full">
                            Manage Subscription
                        </flux:button>
                    </x-slot>
                </flux:callout>
            </div>
        @endif

        <!-- User Section -->
        <flux:navlist variant="outline">
            <flux:navlist.item :href="route('settings.credits')" wire:navigate>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <flux:icon.currency-dollar class="size-4 text-zinc-500 dark:text-zinc-400" />
                        <span>{{ auth()->user()->credits->credits ?? 0 }} credits remaining</span>
                    </div>
                </div>
            </flux:navlist.item>
        </flux:navlist>

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            @php
                $userCredits = auth()->user()->credits;
                $totalPurchased = $userCredits->total_purchased ?? 0;
                $totalUsed = $userCredits->total_used ?? 0;
                $usagePercentage = $totalPurchased > 0 ? ($totalUsed / $totalPurchased) * 100 : 0;
            @endphp

            <!-- Custom Profile with Progress Indicator -->
            <flux:button variant="ghost" class="group flex w-full items-center gap-3 px-3 py-2 text-sm font-medium">
                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                    <!-- Default Avatar -->
                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white group-hover:opacity-0 transition-opacity duration-200">
                        {{ auth()->user()->initials() }}
                    </span>

                    <!-- Progress Indicator (shown on hover) -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <div class="relative w-9 h-9">
                            <!-- Background circle -->
                            <svg class="w-9 h-9 transform -rotate-90" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="2" opacity="0.2" />
                                <!-- Progress circle -->
                                <circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="{{ 2 * pi() * 8 }}" stroke-dashoffset="{{ 2 * pi() * 8 * (1 - $usagePercentage / 100) }}" class="transition-all duration-300 {{ $usagePercentage > 80 ? 'text-red-500' : ($usagePercentage > 60 ? 'text-yellow-500' : 'text-green-500') }}" stroke-linecap="round" />
                            </svg>
                            <!-- Percentage text -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-[10px] font-bold">{{ round($usagePercentage) }}%</span>
                            </div>
                        </div>
                    </div>
                </span>

                <div class="flex flex-1 items-center justify-between">
                    <div class="grid text-start">
                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                    </div>
                    <flux:icon name="chevrons-up-down" variant="mini" class="text-zinc-400" />
                </div>
            </flux:button>

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    <flux:menu.item :href="route('settings.subscription')" icon="credit-card" wire:navigate>{{ __('Subscription') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            @php
                $userCredits = auth()->user()->credits;
                $totalPurchased = $userCredits->total_purchased ?? 0;
                $totalUsed = $userCredits->total_used ?? 0;
                $usagePercentage = $totalPurchased > 0 ? ($totalUsed / $totalPurchased) * 100 : 0;
            @endphp

            <!-- Custom Profile with Progress Indicator -->
            <flux:button variant="ghost" class="group flex items-center gap-2 rounded-lg p-2">
                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                    <!-- Default Avatar -->
                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white group-hover:opacity-0 transition-opacity duration-200">
                        {{ auth()->user()->initials() }}
                    </span>

                    <!-- Progress Indicator (shown on hover) -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <div class="relative w-9 h-9">
                            <!-- Background circle -->
                            <svg class="w-9 h-9 transform -rotate-90" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="2" opacity="0.2" />
                                <!-- Progress circle -->
                                <circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="{{ 2 * pi() * 8 }}" stroke-dashoffset="{{ 2 * pi() * 8 * (1 - $usagePercentage / 100) }}" class="transition-all duration-300 {{ $usagePercentage > 80 ? 'text-red-500' : ($usagePercentage > 60 ? 'text-yellow-500' : 'text-green-500') }}" stroke-linecap="round" />
                            </svg>
                            <!-- Percentage text -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-[10px] font-bold">{{ round($usagePercentage) }}%</span>
                            </div>
                        </div>
                    </div>
                </span>
                <flux:icon name="chevron-down" variant="mini" class="text-zinc-400" />
            </flux:button>

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    <flux:menu.item :href="route('settings.subscription')" icon="credit-card" wire:navigate>{{ __('Subscription') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
