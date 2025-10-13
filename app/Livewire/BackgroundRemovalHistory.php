<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\BackgroundRemoval;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class BackgroundRemovalHistory extends Component
{
    use WithPagination;

    public function render()
    {
        $history = auth()->user()
            ->backgroundRemovals()
            ->latest()
            ->paginate(10);

        return view('livewire.background-removal-history', compact('history'));
    }

    public function delete(BackgroundRemoval $removal): void
    {
        $this->authorize('delete', $removal);

        // Delete the files from storage
        if ($removal->original_path && Storage::exists($removal->original_path)) {
            Storage::delete($removal->original_path);
        }

        if ($removal->processed_path && Storage::exists($removal->processed_path)) {
            Storage::delete($removal->processed_path);
        }

        $removal->delete();

        session()->flash('message', 'Background removal record deleted successfully.');
    }
}
