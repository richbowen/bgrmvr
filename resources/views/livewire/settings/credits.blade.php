<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Credits')" :subheading="__('Purchase credits for flexible background removal')">
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

        <!-- Current Credit Balance -->
        <div class="mb-8 p-6 bg-slate-50 dark:bg-slate-800 rounded-lg border">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Current Balance</h3>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold">
                        {{ $this->currentCredits }}
                    </p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Credits available for background removal
                    </p>
                </div>

                <div class="text-right">
                    <flux:icon name="currency-dollar" class="h-12 w-12 text-slate-400" />
                </div>
            </div>
        </div>

        <!-- Credit Packages -->
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Purchase Credits
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    Buy credits for pay-as-you-go usage. Credits never expire and can be used anytime.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($this->creditPackages as $package)
                    <div class="relative p-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg flex flex-col">
                        <div class="mb-4">
                            <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $package['name'] }}</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $package['description'] }}</p>
                        </div>

                        <div class="mb-4">
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold text-slate-900 dark:text-white">${{ number_format($package['price'], 2) }}</span>
                                <span class="text-sm text-slate-500 dark:text-slate-400 ml-1">one-time</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                {{ $package['credits'] }} credits • ${{ number_format($package['price_per_credit'], 3) }} per credit
                            </p>
                            @if ($package['savings'])
                                <p class="text-sm text-green-600 dark:text-green-400 font-medium">
                                    {{ $package['savings'] }}
                                </p>
                            @endif
                        </div>

                        <div class="mb-6 flex-grow">
                            <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                                <flux:icon name="check" class="h-4 w-4 text-green-500 mr-2 flex-shrink-0" />
                                Credits never expire
                            </div>
                            <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                                <flux:icon name="check" class="h-4 w-4 text-green-500 mr-2 flex-shrink-0" />
                                Use anytime for background removal
                            </div>
                            <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                                <flux:icon name="check" class="h-4 w-4 text-green-500 mr-2 flex-shrink-0" />
                                Perfect for overflow usage
                            </div>
                        </div>

                        <div class="mt-auto">
                            <flux:button wire:click="purchaseCredits('{{ $package['id'] }}')" variant="outline" class="w-full">
                                Purchase {{ $package['credits'] }} Credits
                            </flux:button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Info Section -->
            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-start">
                    <flux:icon name="information-circle" class="h-5 w-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" />
                    <div>
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">
                            Credits vs Subscriptions
                        </h4>
                        <p class="text-sm text-blue-700 dark:text-blue-200">
                            Subscriptions offer better value for regular usage, while credits are perfect for occasional use or when you exceed your monthly limits. Credits purchased here can be used alongside any active subscription.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </x-settings.layout>
</section>
