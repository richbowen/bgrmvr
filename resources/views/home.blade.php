<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>BGRMVR - Professional Background Remover</title>
    <meta name="description" content="Remove backgrounds from images instantly. Professional background removal with precision and speed.">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-zinc-50 via-blue-50 to-indigo-50 dark:from-zinc-900 dark:via-zinc-800 dark:to-zinc-900 text-zinc-900 dark:text-zinc-100 min-h-screen">
    <!-- Navigation -->
    <nav class="border-b border-zinc-200 dark:border-zinc-700 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        BGRMVR
                    </h1>
                </div>

                @if (Route::has('login'))
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden py-20 sm:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-6xl">
                    Remove Backgrounds
                    <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Instantly
                    </span>
                </h1>
                <p class="mt-6 text-lg leading-8 text-zinc-600 dark:text-zinc-300 max-w-2xl mx-auto">
                    Professional background removal for your images. Perfect for e-commerce, social media, and creative projects.
                    Choose between monthly plans with 7-day free trials or flexible pay-as-you-go credits.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-md bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                            Start Removing Backgrounds
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="rounded-md bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                            Start 7-Day Free Trial
                        </a>
                    @endauth
                    <a href="#pricing" class="text-base font-semibold leading-6 text-zinc-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        View Pricing <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Background decoration -->
        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
            <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -tranzinc-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white dark:bg-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base font-semibold leading-7 text-indigo-600 dark:text-indigo-400">Features</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-4xl">
                    Everything you need to remove backgrounds
                </p>
                <p class="mt-6 text-lg leading-8 text-zinc-600 dark:text-zinc-300">
                    Powered by advanced algorithms for professional results every time.
                </p>
            </div>

            <div class="mt-20 grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="relative p-6 bg-zinc-50 dark:bg-zinc-700 rounded-2xl">
                    <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">Lightning Fast</h3>
                    <p class="text-zinc-600 dark:text-zinc-300">Process images in seconds with our optimized technology. No more waiting around for results.</p>
                </div>

                <!-- Feature 2 -->
                <div class="relative p-6 bg-zinc-50 dark:bg-zinc-700 rounded-2xl">
                    <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">Advanced Precision</h3>
                    <p class="text-zinc-600 dark:text-zinc-300">Advanced algorithms detect even the finest details, preserving hair, fur, and complex edges perfectly.</p>
                </div>

                <!-- Feature 3 -->
                <div class="relative p-6 bg-zinc-50 dark:bg-zinc-700 rounded-2xl">
                    <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">Easy to Use</h3>
                    <p class="text-zinc-600 dark:text-zinc-300">Simply upload your image and let our technology do the work. No technical skills required.</p>
                </div>

                <!-- Feature 4 -->
                <div class="relative p-6 bg-zinc-50 dark:bg-zinc-700 rounded-2xl">
                    <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">Secure & Private</h3>
                    <p class="text-zinc-600 dark:text-zinc-300">Your images are processed securely and never stored. Complete privacy guaranteed.</p>
                </div>

                <!-- Feature 5 -->
                <div class="relative p-6 bg-zinc-50 dark:bg-zinc-700 rounded-2xl">
                    <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v18a1 1 0 01-1 1H4a1 1 0 01-1-1V3a1 1 0 011-1h2a1 1 0 011 1v1m0 0h8m-8 0V1a1 1 0 00-1-1H4a1 1 0 00-1 1v2M7 7h10m-5 3v6m-2-3h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">High Quality</h3>
                    <p class="text-zinc-600 dark:text-zinc-300">Professional-grade results suitable for commercial use, marketing, and print materials.</p>
                </div>

                <!-- Feature 6 -->
                <div class="relative p-6 bg-zinc-50 dark:bg-zinc-700 rounded-2xl">
                    <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">7-Day Free Trial</h3>
                    <p class="text-zinc-600 dark:text-zinc-300">Start with a full 7-day trial of any plan. No commitment, cancel anytime during the trial.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 bg-zinc-50 dark:bg-zinc-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base font-semibold leading-7 text-indigo-600 dark:text-indigo-400">Pricing</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-4xl">
                    Monthly Plans or Pay-as-You-Go
                </p>
                <p class="mt-6 text-lg leading-8 text-zinc-600 dark:text-zinc-300 max-w-2xl mx-auto">
                    Choose monthly subscription plans (with 7-day free trials) for regular usage, or purchase credit packages for flexible, one-time access.
                </p>
            </div>

            <!-- Subscription Plans Section (Primary Focus) -->
            <div class="mt-16">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4 max-w-7xl mx-auto">
                    @foreach ([
        ['name' => 'Starter', 'id' => 'starter', 'price' => '$3', 'credits' => '50 background removals/month', 'features' => ['7-day free trial', 'Ultra affordable entry', 'High-quality processing', 'Email support'], 'description' => 'Perfect for trying out'],
        ['name' => 'Basic', 'id' => 'basic', 'price' => '$7', 'credits' => '150 background removals/month', 'features' => ['7-day free trial', 'Great value upgrade', 'Priority processing', 'Email support'], 'description' => 'Great for light usage'],
        ['name' => 'Professional', 'id' => 'professional', 'price' => '$15', 'credits' => '400 background removals/month', 'features' => ['7-day free trial', 'Best value per removal', 'Priority support', 'API access'], 'popular' => true, 'description' => 'Most popular choice'],
        ['name' => 'Business', 'id' => 'business', 'price' => '$35', 'credits' => '1,000 background removals/month', 'features' => ['7-day free trial', 'Team management', 'Bulk processing', 'Priority support'], 'description' => 'For growing teams'],
    ] as $plan)
                        <div class="relative rounded-2xl border {{ isset($plan['popular']) ? 'border-2 border-indigo-600' : 'border-zinc-200 dark:border-zinc-700' }} p-6 bg-white dark:bg-zinc-800 shadow-sm {{ isset($plan['popular']) ? 'shadow-lg' : '' }}">
                            @if (isset($plan['popular']))
                                <div class="absolute -top-3 left-1/2 -tranzinc-x-1/2">
                                    <span class="inline-flex items-center rounded-full bg-indigo-600 px-3 py-1 text-xs font-medium text-white">Most Popular</span>
                                </div>
                            @endif

                            <h4 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $plan['name'] }}</h4>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $plan['description'] }}</p>
                            <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $plan['price'] }}</span>
                            <span class="text-sm font-semibold leading-6 text-zinc-600 dark:text-zinc-300">per month</span>
                            </p>

                            <ul role="list" class="mt-6 space-y-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                <li class="flex gap-x-3">
                                    <svg class="h-5 w-4 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    <span><strong>{{ $plan['credits'] }}</strong></span>
                                </li>
                                @foreach ($plan['features'] as $feature)
                                    <li class="flex gap-x-3">
                                        <svg class="h-5 w-4 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <a href="{{ route('trial-signup', $plan['id']) }}" class="mt-6 block w-full rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                                Start 7-Day Free Trial
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pricing options info -->
            <div class="mt-16 text-center">
                <div class="inline-flex items-center px-6 py-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">7-day free trial on all plans • No credit card required upfront</span>
                </div>
                <div class="mt-6 max-w-3xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-zinc-800 p-6 rounded-lg border border-zinc-200 dark:border-zinc-600">
                            <h3 class="font-semibold text-zinc-900 dark:text-white mb-2">Monthly Plans</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Subscription plans with monthly credit allocations. Start with a 7-day free trial.
                            </p>
                        </div>
                        <div class="bg-white dark:bg-zinc-800 p-6 rounded-lg border border-zinc-200 dark:border-zinc-600">
                            <h3 class="font-semibold text-zinc-900 dark:text-white mb-2">Pay-as-You-Go</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                <a href="{{ auth()->check() ? route('settings.credits') : route('credits') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Purchase credit packages</a> for flexible, one-time usage without subscription.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-indigo-600 dark:bg-indigo-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Ready to remove backgrounds?
            </h2>
            <p class="mt-6 text-lg leading-8 text-indigo-100 max-w-2xl mx-auto">
                Join thousands of users who trust BGRMVR for their background removal needs.
                Start with a 7-day free trial or purchase credits for instant access.
            </p>
            <div class="mt-10">
                @auth
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('dashboard') }}" class="rounded-md bg-white px-6 py-3 text-base font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-colors">
                            Start Processing
                        </a>
                        <a href="{{ route('trial-signup', 'professional') }}" class="rounded-md border border-white px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-colors">
                            Start Free Trial
                        </a>
                        <a href="{{ route('settings.credits') }}" class="rounded-md border border-white px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-colors">
                            Buy Credits
                        </a>
                    </div>
                @else
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('trial-signup', 'professional') }}" class="rounded-md bg-white px-6 py-3 text-base font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-colors">
                            Start 7-Day Free Trial
                        </a>
                        <a href="{{ route('credits') }}" class="rounded-md border border-white px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-colors">
                            View Credit Packages
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-zinc-900 dark:bg-zinc-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h3 class="text-2xl font-bold text-white mb-4">BGRMVR</h3>
                <p class="text-zinc-400 mb-8">Professional background removal for everyone.</p>
                <div class="text-sm text-zinc-500">
                    <p>&copy; {{ date('Y') }} BGRMVR. Powered by advanced technology.</p>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
