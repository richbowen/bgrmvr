<?php

use App\Models\BackgroundRemoval;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake();
});

it('allows user to download their own files', function () {
    $user = User::factory()->create();

    // Create fake files first
    Storage::put('background-removals/originals/test.jpg', 'fake-original-content');
    Storage::put('background-removals/processed/test.jpg', 'fake-processed-content');

    // Create a background removal record
    $removal = BackgroundRemoval::create([
        'user_id' => $user->id,
        'original_filename' => 'test.jpg',
        'original_path' => 'background-removals/originals/test.jpg',
        'processed_path' => 'background-removals/processed/test.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 1000,
        'replicate_prediction_id' => 'test-id',
        'processing_cost' => 0.018,
    ]);

    // Test downloading original file using uuid
    $response = $this->actingAs($user)
        ->get(route('background-removal.download', ['type' => 'original', 'uuid' => $removal->uuid]));

    $response->assertStatus(200);
});
it('prevents user from downloading other users files', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create fake file
    Storage::put('background-removals/originals/test.jpg', 'fake-content');

    // Create a background removal record for user1
    $removal = BackgroundRemoval::create([
        'user_id' => $user1->id,
        'original_filename' => 'test.jpg',
        'original_path' => 'background-removals/originals/test.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 1000,
        'replicate_prediction_id' => 'test-id',
        'processing_cost' => 0.018,
    ]);

    // Try to access as user2 - should be forbidden (using uuid)
    $response = $this->actingAs($user2)
        ->get(route('background-removal.download', ['type' => 'original', 'uuid' => $removal->uuid]));

    $response->assertStatus(403);
});
