<div>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 min-h-screen">
        <!-- Header -->
        <div class="border-b border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            BGRMVR
                        </a>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('settings.credits') }}" class="text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Buy Credits
                        </a>
                        <a href="{{ route('dashboard') }}" class="text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Success/Error Messages -->
                @if (session('success'))
                    <div class="mb-8 rounded-md bg-green-50 p-4 border border-green-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-8 rounded-md bg-red-50 p-4 border border-red-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Page Header -->
                <div class="text-center mb-12">
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                        Choose Your Plan
                    </h1>
                    <p class="mt-4 text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto">
                        Subscribe to a plan that includes monthly credits and additional features. You can also purchase additional credits anytime.
                    </p>
                </div>

                <!-- Subscription Tiers -->
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($subscriptionTiers as $tier)
                        <div class="relative rounded-2xl border {{ isset($tier['popular']) ? 'border-2 border-indigo-600' : 'border border-slate-200 dark:border-slate-700' }} p-8 bg-white dark:bg-slate-800 shadow-sm {{ isset($tier['popular']) ? 'shadow-lg' : '' }}">
                            @if (isset($tier['popular']))
                                <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                                    <span class="inline-flex items-center rounded-full bg-indigo-600 px-4 py-1 text-sm font-medium text-white">Most Popular</span>
                                </div>
                            @endif

                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $tier['name'] }}</h3>
                            <p class="mt-4 text-sm text-slate-600 dark:text-slate-300">{{ $tier['description'] }}</p>

                            <p class="mt-6 flex items-baseline gap-x-1">
                                <span class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white">${{ number_format($tier['price'], 2) }}</span>
                                <span class="text-sm font-semibold leading-6 text-slate-600 dark:text-slate-300">per {{ $tier['period'] }}</span>
                            </p>

                            <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                @foreach ($tier['features'] as $feature)
                                    <li class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <button wire:click="subscribeTo('{{ $tier['id'] }}')" class="mt-8 block w-full rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled" wire:target="subscribeTo('{{ $tier['id'] }}')">
                                <span wire:loading.remove wire:target="subscribeTo('{{ $tier['id'] }}')">
                                    Subscribe
                                </span>
                                <span wire:loading wire:target="subscribeTo('{{ $tier['id'] }}')">
                                    Processing...
                                </span>
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- Additional Info -->
                <div class="mt-16 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700">
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Need more credits?</h2>
                        <p class="text-slate-600 dark:text-slate-300 mb-4">
                            Running low on your monthly credits? You can purchase additional credits anytime.
                        </p>
                        <a href="{{ route('settings.credits') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 transition-colors">
                            Buy Credits
                        </a>
                    </div>

                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700">
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">How subscriptions work</h2>
                        <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                            <li>• Monthly credits are added automatically</li>
                            <li>• Unused credits roll over to next month</li>
                            <li>• Cancel or change plan anytime</li>
                            <li>• Additional credits can be purchased separately</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
