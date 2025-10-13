<div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ($creditPackages as $package)
        <div class="relative rounded-2xl border {{ $package['popular'] ?? false ? 'border-2 border-indigo-600' : 'border-slate-200 dark:border-slate-700' }} p-8 bg-white dark:bg-slate-800 {{ $package['popular'] ?? false ? 'shadow-lg' : 'shadow-sm' }}">
            @if ($package['popular'] ?? false)
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

            <button wire:click="purchaseCredits('{{ $package['id'] }}')" wire:loading.attr="disabled" wire:target="purchaseCredits('{{ $package['id'] }}')" class="mt-8 block w-full rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors disabled:opacity-50">
                <span wire:loading.remove wire:target="purchaseCredits('{{ $package['id'] }}')">Buy Credits</span>
                <span wire:loading wire:target="purchaseCredits('{{ $package['id'] }}')">Processing...</span>
            </button>
        </div>
    @endforeach
</div>
