<?php

use App\Livewire\Actions\RemoveBackground;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Config::set('services.replicate.api_token', 'test-token');
});

test('successfully removes background from image', function () {
    Http::fake([
        'api.replicate.com/*' => Http::response([
            'id' => 'test-prediction-id',
            'status' => 'succeeded',
            'output' => 'https://example.com/output-image.png',
        ], 200),
    ]);

    $action = new RemoveBackground;
    $result = $action('https://example.com/test-image.jpg');

    expect($result)->toBeArray()
        ->and($result['status'])->toBe('succeeded')
        ->and($result['output'])->toBe('https://example.com/output-image.png');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.replicate.com/v1/models/bria/remove-background/predictions'
            && $request['input']['image'] === 'https://example.com/test-image.jpg'
            && $request->header('Authorization')[0] === 'Bearer test-token'
            && $request->header('Content-Type')[0] === 'application/json'
            && $request->header('Prefer')[0] === 'wait';
    });
});

test('throws exception when image url is empty', function () {
    $action = new RemoveBackground;

    expect(fn () => $action(''))->toThrow(InvalidArgumentException::class, 'Image data cannot be empty.');
});

test('throws exception when image url is invalid', function () {
    $action = new RemoveBackground;

    expect(fn () => $action('not-a-valid-url'))->toThrow(InvalidArgumentException::class, 'Invalid image data format. Must be a valid URL or base64 encoded image.');
});

test('throws exception when api token is not configured', function () {
    Config::set('services.replicate.api_token', null);

    $action = new RemoveBackground;

    expect(fn () => $action('https://example.com/test-image.jpg'))
        ->toThrow(Exception::class, 'Replicate API token is not configured.');
});

test('throws exception when api request fails', function () {
    Http::fake([
        'api.replicate.com/*' => Http::response([
            'error' => 'Invalid input',
        ], 400),
    ]);

    $action = new RemoveBackground;

    expect(fn () => $action('https://example.com/test-image.jpg'))
        ->toThrow(Exception::class, 'Failed to remove background:');
});

test('throws exception when api returns server error', function () {
    Http::fake([
        'api.replicate.com/*' => Http::response('Internal Server Error', 500),
    ]);

    $action = new RemoveBackground;

    expect(fn () => $action('https://example.com/test-image.jpg'))
        ->toThrow(Exception::class, 'Failed to remove background:');
});

test('validates url format correctly', function () {
    Http::fake([
        'api.replicate.com/*' => Http::response([
            'status' => 'succeeded',
            'output' => 'https://example.com/output-image.png',
        ], 200),
    ]);

    $action = new RemoveBackground;

    // Valid URLs should work
    $result1 = $action('https://example.com/image.jpg');
    $result2 = $action('http://example.com/image.png');
    $result3 = $action('https://cdn.example.com/path/to/image.webp');

    expect($result1)->toBeArray();
    expect($result2)->toBeArray();
    expect($result3)->toBeArray();
});
