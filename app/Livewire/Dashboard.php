<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Actions\RemoveBackground;
use App\Models\BackgroundRemoval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dashboard extends Component
{
    use WithFileUploads;

    // Quick upload properties
    #[Validate('required|image|max:10240')] // Max 10MB
    public $quickImage;

    public $isQuickProcessing = false;

    public $quickError = null;

    public function quickRemoveBackground(): void
    {
        $this->validate(['quickImage' => 'required|image|max:10240']);

        // Check if user has enough credits
        $user = Auth::user();
        $userCredits = $user->credits;

        if (! $userCredits->hasCredits(1)) {
            $this->quickError = 'Insufficient credits. Please purchase more credits to continue.';

            return;
        }

        $this->isQuickProcessing = true;
        $this->quickError = null;

        try {
            // Store the original image permanently
            $originalFilename = $this->quickImage->getClientOriginalName();
            $originalPath = $this->quickImage->store('background-removals/originals', 'public');

            // Convert image to base64 for API
            $imageContent = file_get_contents($this->quickImage->getRealPath());
            $mimeType = $this->quickImage->getMimeType();
            $base64Image = 'data:'.$mimeType.';base64,'.base64_encode($imageContent);

            // Call the remove background action
            $removeBackground = new RemoveBackground;
            $result = $removeBackground($base64Image);

            // Log the response for troubleshooting
            Log::info('Replicate API Response:', $result);

            // Deduct 1 credit from user's account
            if (! $userCredits->useCredits(1)) {
                throw new \Exception('Failed to deduct credits');
            }

            // Create database record
            $removal = BackgroundRemoval::create([
                'user_id' => $user->id,
                'original_filename' => $originalFilename,
                'original_path' => $originalPath,
                'mime_type' => $mimeType,
                'file_size' => $this->quickImage->getSize(),
                'replicate_prediction_id' => $result['id'] ?? null,
                'processing_cost' => 1, // 1 credit cost
            ]);

            // Handle the result - check multiple possible response formats
            if (isset($result['output']) && ! empty($result['output'])) {
                $processedUrl = $result['output'];
            } elseif (isset($result['urls']['output']) && ! empty($result['urls']['output'])) {
                $processedUrl = $result['urls']['output'];
            } elseif (isset($result['prediction']['output']) && ! empty($result['prediction']['output'])) {
                $processedUrl = $result['prediction']['output'];
            } else {
                throw new \Exception('Background removal failed: No output URL found in response');
            }

            // Download and store the processed image
            $processedImageContent = Http::get($processedUrl)->body();
            $processedFilename = 'processed_'.$originalFilename;
            $processedPath = 'background-removals/processed/'.$processedFilename;

            Storage::disk('public')->put($processedPath, $processedImageContent);

            // Update the database record
            $removal->update([
                'processed_path' => $processedPath,
                'processed_at' => now(),
            ]);

            // Reset form and redirect to history
            $this->reset(['quickImage', 'quickError']);
            session()->flash('message', 'Background removed successfully! Check your history for the result.');

            $this->redirect(route('background-removal.history'));
        } catch (\Exception $e) {
            $this->quickError = $e->getMessage();
            Log::error('Quick background removal failed', ['error' => $e->getMessage()]);
        } finally {
            $this->isQuickProcessing = false;
        }
    }

    public function resetQuickUpload(): void
    {
        $this->reset(['quickImage', 'quickError']);
        $this->isQuickProcessing = false;
    }

    public function render()
    {
        $user = Auth::user();

        // Get user statistics
        $stats = [
            'totalRemovals' => $user->backgroundRemovals()->count(),
            'completedRemovals' => $user->backgroundRemovals()->whereNotNull('processed_at')->count(),
            'creditsRemaining' => $user->credits->credits ?? 0,
            'creditsUsed' => $user->credits->total_used ?? 0,
            'creditsPurchased' => $user->credits->total_purchased ?? 0,
        ];

        // Get recent activity
        $recentRemovals = $user->backgroundRemovals()
            ->latest()
            ->limit(3)
            ->get();

        // Calculate additional stats
        $thisMonth = $user->backgroundRemovals()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        return view('livewire.dashboard', compact('stats', 'recentRemovals', 'thisMonth'));
    }
}
