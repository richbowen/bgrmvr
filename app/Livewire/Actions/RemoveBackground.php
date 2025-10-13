<?php

namespace App\Livewire\Actions;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class RemoveBackground
{
    /**
     * Remove background from the given image using Replicate's Bria model.
     */
    public function __invoke(string $imageData): array
    {
        $this->validateImageData($imageData);

        $response = $this->sendReplicateRequest($imageData);

        if ($response->failed()) {
            throw new \Exception(
                'Failed to remove background: '.$response->body(),
                $response->status()
            );
        }

        return $response->json();
    }

    /**
     * Validate the image data (URL or base64).
     */
    private function validateImageData(string $imageData): void
    {
        if (empty($imageData)) {
            throw new InvalidArgumentException('Image data cannot be empty.');
        }

        // Check if it's a URL or base64 data
        if (! filter_var($imageData, FILTER_VALIDATE_URL) && ! $this->isBase64Image($imageData)) {
            throw new InvalidArgumentException('Invalid image data format. Must be a valid URL or base64 encoded image.');
        }
    }

    /**
     * Check if the data is base64 encoded image.
     */
    private function isBase64Image(string $data): bool
    {
        return preg_match('/^data:image\/[a-zA-Z]+;base64,/', $data) === 1;
    }

    /**
     * Send request to Replicate API.
     */
    private function sendReplicateRequest(string $imageData): Response
    {
        $apiToken = config('services.replicate.api_token');

        if (empty($apiToken)) {
            throw new \Exception('Replicate API token is not configured.');
        }

        return Http::withHeaders([
            'Authorization' => 'Bearer '.$apiToken,
            'Content-Type' => 'application/json',
            'Prefer' => 'wait',
        ])->post('https://api.replicate.com/v1/models/bria/remove-background/predictions', [
            'input' => [
                'image' => $imageData,
            ],
        ]);
    }
}
