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
                        <div class="flex items-center gap-2">
                            <span class="text-slate-600 dark:text-slate-300">Current Credits:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ auth()->user()->credits->credits ?? 0 }}
                            </span>
                        </div>
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
                        Purchase Credits
                    </h1>
                    <p class="mt-4 text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto">
                        Buy credits to remove backgrounds from your images. Credits never expire and can be used anytime.
                    </p>
                </div>

                <!-- Credit Packages -->
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($this->creditPackages as $package)
                        <div class="relative rounded-2xl border {{ isset($package['popular']) ? 'border-2 border-indigo-600' : 'border border-slate-200 dark:border-slate-700' }} p-8 bg-white dark:bg-slate-800 shadow-sm {{ isset($package['popular']) ? 'shadow-lg' : '' }}">
                            @if (isset($package['popular']))
                                <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                                    <span class="inline-flex items-center rounded-full bg-indigo-600 px-4 py-1 text-sm font-medium text-white">Most Popular</span>
                                </div>
                            @endif

                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $package['name'] }}</h3>
                            <p class="mt-4 text-sm text-slate-600 dark:text-slate-300">{{ $package['description'] }}</p>

                            <p class="mt-6 flex items-baseline gap-x-1">
                                <span class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white">${{ number_format($package['price'], 0) }}</span>
                                <span class="text-sm font-semibold leading-6 text-slate-600 dark:text-slate-300">one-time</span>
                            </p>

                            <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    <span><strong class="text-slate-900 dark:text-white">{{ number_format($package['credits']) }} credits</strong> for background removal</span>
                                </li>
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    <span>${{ number_format($package['price_per_credit'], 3) }} per image processed</span>
                                </li>
                                @if ($package['savings'])
                                    <li class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ $package['savings'] }}</span>
                                    </li>
                                @endif
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Credits never expire</span>
                                </li>
                            </ul>

                            <button wire:click="purchaseCredits('{{ $package['id'] }}')" class="mt-8 block w-full rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled" wire:target="purchaseCredits('{{ $package['id'] }}')">
                                <span wire:loading.remove wire:target="purchaseCredits('{{ $package['id'] }}')">
                                    Buy Credits
                                </span>
                                <span wire:loading wire:target="purchaseCredits('{{ $package['id'] }}')">
                                    Processing...
                                </span>
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- Free credits info -->
                <div class="mt-16 text-center">
                    <div class="inline-flex items-center px-6 py-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">New users get 5 free credits when they sign up!</span>
                    </div>
                </div>

                <!-- How it works -->
                <div class="mt-16 bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700">
                    <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-6">How it works</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">1</span>
                            </div>
                            <h3 class="font-medium text-slate-900 dark:text-white mb-2">Choose a package</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300">Select the credit package that best fits your needs</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">2</span>
                            </div>
                            <h3 class="font-medium text-slate-900 dark:text-white mb-2">Secure payment</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300">Pay securely with Stripe - we don't store your payment info</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">3</span>
                            </div>
                            <h3 class="font-medium text-slate-900 dark:text-white mb-2">Start removing</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300">Credits are added instantly and you can start processing images</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
