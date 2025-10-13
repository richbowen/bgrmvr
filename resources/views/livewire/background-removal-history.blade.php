<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">History</flux:heading>
            <flux:subheading size="lg">View and manage your background removal history</flux:subheading>
        </div>
        <flux:badge variant="outline" size="sm">{{ $history->total() }} total</flux:badge>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('message') }}
        </flux:callout>
    @endif

    @if ($history->count() > 0)
        <div class="w-full overflow-x-auto">
            <div class="flex flex-col">
                <table class="[:where(&)]:min-w-full table-fixed border-separate border-spacing-0 isolate text-zinc-800 whitespace-nowrap [&_dialog]:whitespace-normal [&_[popover]]:whitespace-normal" data-flux-table="">
                    <thead class="" data-flux-columns="">
                        <tr>
                            <th class="py-3 px-3 first:ps-0 last:pe-0 text-start text-sm font-medium text-zinc-800 dark:text-white border-b border-zinc-800/10 dark:border-white/20" data-flux-column="">
                                <div class="flex">Image</div>
                            </th>
                            <th class="py-3 px-3 first:ps-0 last:pe-0 text-start text-sm font-medium text-zinc-800 dark:text-white border-b border-zinc-800/10 dark:border-white/20" data-flux-column="">
                                <div class="flex">Details</div>
                            </th>
                            <th class="py-3 px-3 first:ps-0 last:pe-0 text-start text-sm font-medium text-zinc-800 dark:text-white border-b border-zinc-800/10 dark:border-white/20" data-flux-column="">
                                <div class="flex">Status</div>
                            </th>
                            <th class="py-3 px-3 first:ps-0 last:pe-0 text-start text-sm font-medium text-zinc-800 dark:text-white border-b border-zinc-800/10 dark:border-white/20" data-flux-column="">
                                <div class="flex">Cost</div>
                            </th>
                            <th class="py-3 px-3 first:ps-0 last:pe-0 text-start text-sm font-medium text-zinc-800 dark:text-white border-b border-zinc-800/10 dark:border-white/20" data-flux-column="">
                                <div class="flex">Date</div>
                            </th>
                            <th class="py-3 px-3 first:ps-0 last:pe-0 text-start text-sm font-medium text-zinc-800 dark:text-white border-b border-zinc-800/10 dark:border-white/20" data-flux-column="">
                                <div class="flex"></div>
                            </th>
                        </tr>
                    </thead>
                    <tbody data-flux-rows="">
                        @foreach ($history as $removal)
                            <tr wire:key="history-{{ $removal->id }}" class="" data-flux-row="">
                                <td class="py-3 px-3 first:ps-0 last:pe-0 text-sm text-zinc-500 dark:text-zinc-300 not-in-[tr:first-child]:border-t border-zinc-800/10 dark:border-white/20" data-flux-cell="">
                                    <div class="flex items-center">
                                        @if ($removal->processed_path)
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <img class="h-12 w-12 rounded-md object-cover" src="{{ $removal->processed_url }}" alt="Processed {{ $removal->original_filename }}">
                                            </div>
                                        @else
                                            <div class="flex-shrink-0 h-12 w-12 bg-zinc-100 dark:bg-zinc-800 rounded-md flex items-center justify-center">
                                                <flux:icon name="photo" class="h-6 w-6 text-zinc-400" />
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3 first:ps-0 last:pe-0 text-sm text-zinc-500 dark:text-zinc-300 not-in-[tr:first-child]:border-t border-zinc-800/10 dark:border-white/20" data-flux-cell="">
                                    <div class="space-y-1">
                                        <div class="text-sm font-medium text-zinc-800 dark:text-white">
                                            {{ $removal->original_filename }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $removal->file_size_formatted }}
                                        </div>
                                        @if ($removal->replicate_prediction_id)
                                            <div class="text-xs text-zinc-400 dark:text-zinc-500 font-mono">
                                                ID: {{ Str::limit($removal->replicate_prediction_id, 12) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3 first:ps-0 last:pe-0 text-sm text-zinc-500 dark:text-zinc-300 not-in-[tr:first-child]:border-t border-zinc-800/10 dark:border-white/20" data-flux-cell="">
                                    @if ($removal->processed_at)
                                        <flux:badge variant="success" size="sm">
                                            Completed
                                        </flux:badge>
                                    @else
                                        <flux:badge variant="warning" size="sm">
                                            Processing
                                        </flux:badge>
                                    @endif
                                </td>
                                <td class="py-3 px-3 first:ps-0 last:pe-0 text-sm font-medium text-zinc-800 dark:text-white not-in-[tr:first-child]:border-t border-zinc-800/10 dark:border-white/20" data-flux-cell="">
                                    @if ($removal->processing_cost > 0)
                                        {{ number_format($removal->processing_cost) }} {{ $removal->processing_cost == 1 ? 'credit' : 'credits' }}
                                    @else
                                        <span class="text-zinc-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 first:ps-0 last:pe-0 text-sm text-zinc-500 dark:text-zinc-300 not-in-[tr:first-child]:border-t border-zinc-800/10 dark:border-white/20 whitespace-nowrap" data-flux-cell="">
                                    {{ $removal->created_at->format('M j, g:i A') }}
                                </td>
                                <td class="py-3 px-3 first:ps-0 last:pe-0 text-sm text-zinc-500 dark:text-zinc-300 not-in-[tr:first-child]:border-t border-zinc-800/10 dark:border-white/20" data-flux-cell="">
                                    <flux:dropdown position="bottom end" offset="-25">
                                        <flux:button variant="ghost" size="sm" class="h-8 w-8 -mt-1.5 -mb-1.5">
                                            <flux:icon name="ellipsis-horizontal" class="h-5 w-5" />
                                        </flux:button>

                                        <flux:menu>
                                            @if ($removal->processed_path)
                                                <flux:menu.item href="{{ $removal->original_view_url }}" icon="eye" target="_blank">
                                                    View Original
                                                </flux:menu.item>
                                                <flux:menu.item href="{{ $removal->processed_url }}" icon="arrow-down-tray" icon="arrow-down-tray" target="_blank">
                                                    Download Processed
                                                </flux:menu.item>
                                                <flux:menu.separator />
                                            @endif
                                            <flux:menu.item wire:click="delete({{ $removal->id }})" icon="trash" wire:confirm="Are you sure you want to delete this background removal? This action cannot be undone." variant="danger">
                                                Delete
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-center">
            {{ $history->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <flux:icon name="photo" class="mx-auto h-12 w-12 text-zinc-400" />
            <flux:heading size="lg" class="mt-4 text-zinc-900 dark:text-zinc-100">No background removals yet</flux:heading>
            <flux:text class="mt-2 text-zinc-500 dark:text-zinc-400">
                Start by uploading an image to remove its background.
            </flux:text>
            <div class="mt-6">
                <flux:button variant="primary" href="{{ route('background-removal.tool') }}">
                    Upload Your First Image
                </flux:button>
            </div>
        </div>
    @endif
</div>
