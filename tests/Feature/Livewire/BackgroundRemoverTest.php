<?php

use App\Livewire\BackgroundRemover;
use App\Models\BackgroundRemoval;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    config(['services.replicate.api_token' => 'test-token']);
});

it('renders successfully', function () {
    Livewire::test(BackgroundRemover::class)
        ->assertStatus(200)
        ->assertSee('Remove Background')
        ->assertSee('Upload an image');
});

it('validates required image upload', function () {
    Livewire::test(BackgroundRemover::class)
        ->call('removeBackground')
        ->assertHasErrors(['image' => 'required']);
});

it('validates image file type', function () {
    $file = File::create('test.txt', 100);

    Livewire::test(BackgroundRemover::class)
        ->set('image', $file)
        ->assertHasErrors(['image']);
});

it('validates image file size', function () {
    $file = File::image('large.jpg')->size(15000); // 15MB

    Livewire::test(BackgroundRemover::class)
        ->set('image', $file)
        ->assertHasErrors(['image']);
});

it('prevents users without subscription or credits from processing', function () {
    $file = File::image('test.jpg', 800, 600)->size(100);
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BackgroundRemover::class)
        ->set('image', $file)
        ->call('removeBackground')
        ->assertSet('error', 'You need credits or an active subscription to remove backgrounds. Please subscribe or purchase credits to continue.');
});

it('successfully processes image and removes background', function () {
    // Mock the external API call to Replicate
    Http::fake([
        'https://api.replicate.com/v1/models/bria/remove-background/predictions' => Http::response([
            'id' => 'test-prediction-id',
            'status' => 'succeeded',
            'output' => 'https://example.com/processed-image.png',
        ], 200),
        // Mock the download of the processed image
        'https://example.com/processed-image.png' => Http::response('fake-processed-image-content', 200),
    ]);

    $file = File::image('test.jpg', 800, 600)->size(100);
    $user = User::factory()->create();

    // Give user a trial subscription so they can process images
    $user->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_trial_test',
        'stripe_status' => 'trialing',
        'stripe_price' => 'price_basic_monthly',
        'trial_ends_at' => now()->addDays(7),
    ]);

    $component = Livewire::actingAs($user)
        ->test(BackgroundRemover::class)
        ->set('image', $file)
        ->call('removeBackground');

    // Check for errors first
    $component->assertHasNoErrors();

    // If there's an error, throw it to see what's wrong
    $error = $component->get('error');
    if ($error) {
        throw new \Exception('Component failed with error: '.$error);
    }

    // Check that a database record was created
    expect(BackgroundRemoval::count())->toBe(1);

    $removal = BackgroundRemoval::first();
    expect($removal->user_id)->toBe($user->id);
    expect($removal->original_filename)->toBe('test.jpg');
    expect($removal->replicate_prediction_id)->toBe('test-prediction-id');
    expect((float) $removal->processing_cost)->toBe(1.0);
    expect($removal->processed_path)->not->toBeNull();
    expect($removal->processed_at)->not->toBeNull();
});
it('handles api errors gracefully', function () {
    Http::fake([
        '*' => Http::response(['error' => 'Invalid input'], 400),
    ]);

    $file = File::image('test.jpg', 800, 600)->size(100);
    $user = User::factory()->create();

    // Give user a trial subscription so they can attempt processing
    $user->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_trial_test',
        'stripe_status' => 'trialing',
        'stripe_price' => 'price_basic_monthly',
        'trial_ends_at' => now()->addDays(7),
    ]);

    Livewire::actingAs($user)
        ->test(BackgroundRemover::class)
        ->set('image', $file)
        ->call('removeBackground')
        ->assertSet('isProcessing', false)
        ->assertSet('error', 'Failed to remove background: {"error":"Invalid input"}')
        ->assertSee('Processing failed');
});

it('resets upload state correctly', function () {
    $file = File::image('test.jpg', 800, 600)->size(100);

    Livewire::test(BackgroundRemover::class)
        ->set('image', $file)
        ->set('processedImageUrl', 'https://example.com/test.png')
        ->set('error', 'Some error')
        ->call('resetUpload')
        ->assertSet('image', null)
        ->assertSet('processedImageUrl', null)
        ->assertSet('error', null)
        ->assertSet('processingProgress', 0)
        ->assertSet('isProcessing', false);
});

it('shows upload preview when image is selected', function () {
    $file = File::image('test.jpg', 800, 600)->size(100);

    Livewire::test(BackgroundRemover::class)
        ->set('image', $file)
        ->assertSee('test.jpg')
        ->assertSee('Choose different image');
});

it('shows processing state with progress', function () {
    $file = File::image('test.jpg', 800, 600)->size(100);

    $component = Livewire::test(BackgroundRemover::class)
        ->set('image', $file);

    // Set processing state manually to test UI
    $component->set('isProcessing', true)
        ->set('processingProgress', 50)
        ->assertSee('Processing your image...')
        ->assertSee('50% complete');
});
