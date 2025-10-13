<?php

use App\Livewire\BackgroundRemover;
use App\Models\BackgroundRemoval;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    config(['services.replicate.api_token' => 'test-token']);
});

it('can create database record manually', function () {
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

    expect(BackgroundRemoval::count())->toBe(1);
});

it('debug component call without processing', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(BackgroundRemover::class);

    expect($component->get('isProcessing'))->toBe(false);
    expect($component->get('error'))->toBeNull();
});
