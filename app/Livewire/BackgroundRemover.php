<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Actions\RemoveBackground;
use App\Models\BackgroundRemoval;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class BackgroundRemover extends Component
{
    use WithFileUploads;

    #[Validate('required|image|max:10240')] // Max 10MB
    public $image;

    public $isProcessing = false;

    public $originalImageUrl = null;

    public $processedImageUrl = null;

    public $error = null;

    public $processingProgress = 0;

    public function removeBackground(): void
    {
        $this->validate();

        // Check if user has access (either credits or active subscription/trial)
        $user = \Illuminate\Support\Facades\Auth::user();
        $userCredits = $user->credits;

        if (! $userCredits->hasCredits(1) && ! $user->subscribed('default') && ! $user->onTrial('default')) {
            $this->error = 'You need credits or an active subscription to remove backgrounds. Please subscribe or purchase credits to continue.';

            return;
        }

        $this->isProcessing = true;
        $this->error = null;
        $this->processingProgress = 10;

        try {
            // Store the original image permanently
            $this->processingProgress = 20;
            $originalFilename = $this->image->getClientOriginalName();
            $originalPath = $this->image->store('background-removals/originals', 'public');

            $this->processingProgress = 30;

            // Convert image to base64 for API
            $imageContent = file_get_contents($this->image->getRealPath());
            $mimeType = $this->image->getMimeType();
            $base64Image = 'data:'.$mimeType.';base64,'.base64_encode($imageContent);

            // Use the stored file URL for display
            $this->originalImageUrl = Storage::url($originalPath);

            Log::info('Image prepared for API:', [
                'mime_type' => $mimeType,
                'size_bytes' => strlen($imageContent),
                'filename' => $originalFilename,
            ]);

            $this->processingProgress = 50;

            // Call the remove background action
            $removeBackground = new RemoveBackground;
            $result = $removeBackground($base64Image);

            $this->processingProgress = 70;

            // Log the response for troubleshooting
            Log::info('Replicate API Response:', $result);

            // Deduct 1 credit from user's account (only if not on subscription/trial)
            if (! $user->subscribed('default') && ! $user->onTrial('default')) {
                if (! $userCredits->useCredits(1)) {
                    throw new \Exception('Failed to deduct credits');
                }
            }

            // Create database record
            $removal = BackgroundRemoval::create([
                'user_id' => $user->id,
                'original_filename' => $originalFilename,
                'original_path' => $originalPath,
                'mime_type' => $mimeType,
                'file_size' => $this->image->getSize(),
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

            $this->processingProgress = 85;

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

            $this->processingProgress = 100;
            $this->processedImageUrl = Storage::url($processedPath);

            Log::info('Background removal successful', [
                'removal_id' => $removal->id,
                'original_path' => $originalPath,
                'processed_path' => $processedPath,
            ]);

        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->processingProgress = 0;
            Log::error('Background removal failed', ['error' => $e->getMessage()]);
        } finally {
            $this->isProcessing = false;
        }
    }

    public function resetUpload(): void
    {
        $this->reset(['image', 'originalImageUrl', 'processedImageUrl', 'error', 'processingProgress']);
        $this->isProcessing = false;
    }

    public function render()
    {
        return view('livewire.background-remover');
    }
}
