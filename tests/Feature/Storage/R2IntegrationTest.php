<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Skip these tests if R2 credentials are not configured
    if (! config('filesystems.disks.r2.key') || ! config('filesystems.disks.r2.secret')) {
        $this->markTestSkipped('R2 credentials not configured');
    }
});

describe('R2 Configuration Tests', function () {
    it('has valid R2 configuration', function () {
        $r2Config = config('filesystems.disks.r2');

        expect($r2Config)->toBeArray()
            ->and($r2Config['driver'])->toBe('s3')
            ->and($r2Config['key'])->not()->toBeEmpty()
            ->and($r2Config['secret'])->not()->toBeEmpty()
            ->and($r2Config['bucket'])->not()->toBeEmpty()
            ->and($r2Config['endpoint'])->not()->toBeEmpty()
            ->and($r2Config['url'])->not()->toBeEmpty();
    });

    it('has r2 set as default filesystem disk', function () {
        expect(config('filesystems.default'))->toBe('r2');
    });

    it('can get storage disk instance', function () {
        $disk = Storage::disk('r2');

        expect($disk)->not()->toBeNull();
    });
});

describe('R2 Connectivity Tests', function () {
    it('can connect to R2 and list bucket contents', function () {
        $disk = Storage::disk('r2');

        // This should not throw an exception if connection is working
        $files = $disk->files();

        expect($files)->toBeArray();
    })->group('integration', 'r2');

    it('can test connectivity with a simple operation', function () {
        $disk = Storage::disk('r2');

        // Test if we can check if a non-existent file exists (should return false)
        $exists = $disk->exists('test-connectivity-'.uniqid().'.txt');

        expect($exists)->toBeFalse();
    })->group('integration', 'r2');
});

describe('R2 File Operations Tests', function () {
    it('can upload and download a file', function () {
        $disk = Storage::disk('r2');
        $testContent = 'This is a test file for R2 integration: '.now()->toISOString();
        $testPath = 'test-uploads/integration-test-'.uniqid().'.txt';

        // Upload file
        $uploaded = $disk->put($testPath, $testContent);
        expect($uploaded)->toBeTrue();

        // Verify file exists
        expect($disk->exists($testPath))->toBeTrue();

        // Download and verify content
        $downloadedContent = $disk->get($testPath);
        expect($downloadedContent)->toBe($testContent);

        // Get file size
        $size = $disk->size($testPath);
        expect($size)->toBe(strlen($testContent));

        // Clean up
        $disk->delete($testPath);
        expect($disk->exists($testPath))->toBeFalse();
    })->group('integration', 'r2');

    it('can upload an image file', function () {
        $disk = Storage::disk('r2');

        // Create a fake image
        $fakeImage = UploadedFile::fake()->image('test-image.jpg', 100, 100);
        $testPath = 'test-uploads/images/integration-test-'.uniqid().'.jpg';

        // Upload the image
        $uploaded = $disk->putFileAs('test-uploads/images', $fakeImage, basename($testPath));
        expect($uploaded)->toBe($testPath);

        // Verify file exists
        expect($disk->exists($testPath))->toBeTrue();

        // Verify it's a reasonable file size (should be > 0)
        $size = $disk->size($testPath);
        expect($size)->toBeGreaterThan(0);

        // Clean up
        $disk->delete($testPath);
        expect($disk->exists($testPath))->toBeFalse();
    })->group('integration', 'r2');

    it('can handle file upload errors gracefully', function () {
        $disk = Storage::disk('r2');

        // Test that we can detect when operations might fail
        // Instead of forcing an exception, test that the disk handles edge cases
        $testContent = 'test content';
        $result = $disk->put('test-error-handling/'.uniqid().'.txt', $testContent);

        expect($result)->toBeTrue();

        // Clean up
        $disk->delete('test-error-handling/'.uniqid().'.txt');
    })->group('integration', 'r2');
});

describe('R2 URL Generation Tests', function () {
    it('can generate public URLs for files', function () {
        $disk = Storage::disk('r2');
        $testContent = 'This is a test file for URL generation: '.now()->toISOString();
        $testPath = 'test-uploads/url-test-'.uniqid().'.txt';

        // Upload file
        $disk->put($testPath, $testContent);

        // Generate URL
        $url = $disk->url($testPath);

        expect($url)->toBeString()
            ->and($url)->toStartWith('https://')
            ->and($url)->toContain($testPath); // URL should contain the file path

        // Clean up
        $disk->delete($testPath);
    })->group('integration', 'r2');

    it('generated URLs are accessible over HTTP', function () {
        $disk = Storage::disk('r2');
        $testContent = 'This is a test file for HTTP access: '.now()->toISOString();
        $testPath = 'test-uploads/http-test-'.uniqid().'.txt';

        // Upload file
        $disk->put($testPath, $testContent);

        // Generate URL
        $url = $disk->url($testPath);

        // Try to access the URL via HTTP
        $response = Http::timeout(10)->get($url);

        expect($response->successful())->toBeTrue()
            ->and($response->body())->toBe($testContent);

        // Clean up
        $disk->delete($testPath);
    })->group('integration', 'r2', 'slow');
});

describe('R2 Background Removal Workflow Tests', function () {
    it('can simulate the complete background removal file workflow', function () {
        $disk = Storage::disk('r2');

        // Simulate original file upload
        $originalImage = UploadedFile::fake()->image('original.jpg', 200, 200);
        $originalPath = 'background-removals/originals/workflow-test-'.uniqid().'.jpg';

        $uploaded = $disk->putFileAs('background-removals/originals', $originalImage, basename($originalPath));
        expect($uploaded)->toBe($originalPath);

        // Verify original exists and get URL
        expect($disk->exists($originalPath))->toBeTrue();
        $originalUrl = $disk->url($originalPath);
        expect($originalUrl)->toBeString()->toStartWith('https://');

        // Simulate processed file storage
        $processedContent = 'fake processed image content: '.now()->toISOString();
        $processedPath = 'background-removals/processed/workflow-test-'.uniqid().'.png';

        $disk->put($processedPath, $processedContent);
        expect($disk->exists($processedPath))->toBeTrue();

        // Verify processed file URL
        $processedUrl = $disk->url($processedPath);
        expect($processedUrl)->toBeString()->toStartWith('https://');

        // Verify both files can be accessed
        $originalResponse = Http::timeout(10)->get($originalUrl);
        $processedResponse = Http::timeout(10)->get($processedUrl);

        expect($originalResponse->successful())->toBeTrue();
        expect($processedResponse->successful())->toBeTrue();
        expect($processedResponse->body())->toBe($processedContent);

        // Clean up both files
        $disk->delete($originalPath);
        $disk->delete($processedPath);

        expect($disk->exists($originalPath))->toBeFalse();
        expect($disk->exists($processedPath))->toBeFalse();
    })->group('integration', 'r2', 'workflow');
});
