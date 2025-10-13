<div class="space-y-6">
    <!-- Dashboard Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <flux:heading size="xl" level="1">Dashboard</flux:heading>
            <flux:subheading size="lg" class="mt-1">Welcome back! Here's your activity overview.</flux:subheading>
        </div>
        <div class="mt-4 lg:mt-0">
            <flux:button variant="primary" href="{{ route('background-removal.tool') }}" icon="scissors">
                Open Full Editor
            </flux:button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Removals -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10  rounded-lg flex items-center justify-center">
                        <flux:icon name="photo" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <flux:text size="xl" class="font-bold">{{ $stats['totalRemovals'] }}</flux:text>
                    <flux:text size="sm" variant="muted">Total Removals</flux:text>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10  rounded-lg flex items-center justify-center">
                        <flux:icon name="calendar" class="h-5 w-5 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <flux:text size="xl" class="font-bold">{{ $thisMonth }}</flux:text>
                    <flux:text size="sm" variant="muted">This Month</flux:text>
                </div>
            </div>
        </div>

        <!-- Credits Remaining -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center">
                        <flux:icon name="currency-dollar" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <flux:text size="xl" class="font-bold">{{ $stats['creditsRemaining'] }}</flux:text>
                    <flux:text size="sm" variant="muted">Credits Left</flux:text>
                </div>
            </div>
        </div>

        <!-- Credits Used -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10  rounded-lg flex items-center justify-center">
                        <flux:icon name="chart-bar" class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <flux:text size="xl" class="font-bold">{{ $stats['creditsUsed'] }}</flux:text>
                    <flux:text size="sm" variant="muted">Credits Used</flux:text>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Activity Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:items-start">
        <!-- Quick Background Remover -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg" level="2">Quick Remove</flux:heading>
                <flux:badge variant="outline" size="sm">1 credit</flux:badge>
            </div>

            @if (session()->has('message'))
                <flux:callout variant="success" icon="check-circle" class="mb-4">
                    {{ session('message') }}
                </flux:callout>
            @endif

            @if (!$isQuickProcessing)
                <form wire:submit="quickRemoveBackground" class="space-y-4 flex-1 flex flex-col">
                    <!-- File Upload Area -->
                    <div class="border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-4 text-center hover:border-indigo-400 transition-colors flex-1 flex items-center justify-center min-h-[200px]">
                        @if ($quickImage)
                            <div class="space-y-3">
                                @if ($quickImage->isPreviewable())
                                    <div class="flex items-center justify-center">
                                        <img src="{{ $quickImage->temporaryUrl() }}" alt="Preview" class="max-h-24 rounded-lg">
                                    </div>
                                @endif
                                <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $quickImage->getClientOriginalName() }}</p>
                                <flux:button type="button" wire:click="resetQuickUpload" variant="ghost" size="sm">
                                    Change image
                                </flux:button>
                            </div>
                        @else
                            <div class="space-y-3">
                                <svg class="mx-auto h-8 w-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div>
                                    <label for="quick-file-upload" class="cursor-pointer">
                                        <flux:text size="sm" class="block font-medium">
                                            Drop an image or click to upload
                                        </flux:text>
                                        <flux:text size="xs" variant="muted" class="block mt-1">
                                            PNG, JPG, GIF up to 10MB
                                        </flux:text>
                                    </label>
                                    <input id="quick-file-upload" wire:model="quickImage" type="file" accept="image/*" class="sr-only">
                                </div>
                            </div>
                        @endif
                    </div>

                    @error('quickImage')
                        <div class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</div>
                    @enderror

                    @if ($quickError)
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <div class="flex items-center space-x-2">
                                <flux:icon name="exclamation-triangle" class="h-4 w-4 text-red-500" />
                                <p class="text-sm text-red-700 dark:text-red-300">{{ $quickError }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($quickImage && $stats['creditsRemaining'] > 0)
                        <flux:button type="submit" class="w-full" icon="scissors">
                            Remove Background
                        </flux:button>
                    @elseif ($stats['creditsRemaining'] <= 0)
                        <flux:button href="{{ route('settings.credits') }}" variant="primary" class="w-full" icon="plus">
                            Buy Credits
                        </flux:button>
                    @endif
                </form>
            @else
                <!-- Processing State -->
                <div class="text-center py-8 space-y-4">
                    <div class="flex justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    </div>
                    <div>
                        <flux:heading size="sm">Processing your image...</flux:heading>
                        <flux:text size="xs" variant="muted">This usually takes a few seconds</flux:text>
                    </div>
                </div>
            @endif
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg" level="2">Recent Activity</flux:heading>
                <flux:button variant="ghost" size="sm" href="{{ route('background-removal.history') }}" icon="arrow-right">
                    View All
                </flux:button>
            </div>

            @if ($recentRemovals->count() > 0)
                <div class="space-y-3 flex-1">
                    @foreach ($recentRemovals as $removal)
                        <div class="flex items-center space-x-3 p-3 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
                            @if ($removal->processed_path)
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-lg object-cover" src="{{ $removal->processed_url }}" alt="Processed {{ $removal->original_filename }}">
                                </div>
                            @else
                                <div class="flex-shrink-0 h-10 w-10 bg-zinc-200 dark:bg-zinc-600 rounded-lg flex items-center justify-center">
                                    <flux:icon name="photo" class="h-5 w-5 text-zinc-400" />
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <flux:text size="sm" class="font-medium truncate">
                                    {{ $removal->original_filename }}
                                </flux:text>
                                <flux:text size="xs" variant="muted">
                                    {{ $removal->created_at->diffForHumans() }}
                                </flux:text>
                            </div>
                            <div class="flex-shrink-0">
                                @if ($removal->processed_at)
                                    <flux:badge variant="success" size="sm">Complete</flux:badge>
                                @else
                                    <flux:badge variant="warning" size="sm">Processing</flux:badge>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 flex-1 flex flex-col justify-center">
                    <flux:icon name="photo" class="mx-auto h-10 w-10 text-zinc-400" />
                    <flux:heading size="sm" class="mt-2">No activity yet</flux:heading>
                    <flux:text variant="muted" size="sm" class="mt-1">
                        Upload your first image to get started
                    </flux:text>
                </div>
            @endif
        </div>
    </div>

    <!-- Credit Usage Chart or Additional Info -->
    @if ($stats['creditsUsed'] > 0 || $stats['creditsPurchased'] > 0)
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <flux:heading size="lg" level="2" class="mb-4">Credit Usage</flux:heading>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="text-center">
                    <flux:text size="xl" class="font-bold">{{ $stats['creditsPurchased'] }}</flux:text>
                    <flux:text size="sm" variant="muted">Total Purchased</flux:text>
                </div>
                <div class="text-center">
                    <flux:text size="xl" class="font-bold">{{ $stats['creditsUsed'] }}</flux:text>
                    <flux:text size="sm" variant="muted">Credits Used</flux:text>
                </div>
                <div class="text-center">
                    <flux:text size="xl" class="font-bold">{{ $stats['creditsRemaining'] }}</flux:text>
                    <flux:text size="sm" variant="muted">Remaining</flux:text>
                </div>
            </div>

            @if ($stats['creditsPurchased'] > 0)
                <div class="mt-4">
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                        <div class="bg-zinc-500 h-2 rounded-full" style="width: {{ ($stats['creditsUsed'] / $stats['creditsPurchased']) * 100 }}%"></div>
                    </div>
                    <flux:text size="xs" variant="muted" class="mt-2 text-center">
                        {{ round(($stats['creditsUsed'] / $stats['creditsPurchased']) * 100, 1) }}% of purchased credits used
                    </flux:text>
                </div>
            @endif
        </div>
    @endif
</div>
