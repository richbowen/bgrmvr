<?php

use App\Models\BackgroundRemoval;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a background removal record', function () {
    $user = User::factory()->create();

    $removal = BackgroundRemoval::create([
        'user_id' => $user->id,
        'original_filename' => 'test.jpg',
        'original_path' => 'background-removals/originals/test.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 100000,
        'replicate_prediction_id' => 'test-prediction-id',
        'processing_cost' => 0.018,
    ]);

    expect($removal)->not->toBeNull();
    expect($removal->user_id)->toBe($user->id);
    expect($removal->original_filename)->toBe('test.jpg');
    expect(BackgroundRemoval::count())->toBe(1);
});
