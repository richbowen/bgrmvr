<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserCredit;

test('credit totals remain accurate after subscription changes', function () {
    // Create a user with some background removals
    $user = User::factory()->create();

    // Simulate some background removals
    $user->backgroundRemovals()->createMany([
        ['original_filename' => 'test1.jpg', 'original_path' => 'test1.jpg', 'mime_type' => 'image/jpeg', 'file_size' => 1000, 'processing_cost' => 1],
        ['original_filename' => 'test2.jpg', 'original_path' => 'test2.jpg', 'mime_type' => 'image/jpeg', 'file_size' => 1000, 'processing_cost' => 1],
        ['original_filename' => 'test3.jpg', 'original_path' => 'test3.jpg', 'mime_type' => 'image/jpeg', 'file_size' => 1000, 'processing_cost' => 1],
    ]);

    // Start with some credits and usage
    $userCredit = UserCredit::create([
        'user_id' => $user->id,
        'credits' => 100,
        'total_purchased' => 103,
        'total_used' => 3,
    ]);

    // Verify initial math is correct
    expect($userCredit->total_purchased - $userCredit->total_used)->toBe($userCredit->credits);

    // Simulate subscription tier change that sets credits to 500
    $userCredit->setCredits(500);
    $userCredit->refresh();

    // Verify math is still correct after tier change
    expect($userCredit->credits)->toBe(500);
    expect($userCredit->total_used)->toBe(3); // Should match actual background removals
    expect($userCredit->total_purchased)->toBe(503); // 500 + 3 used
    expect($userCredit->total_purchased - $userCredit->total_used)->toBe($userCredit->credits);
});

test('recalculateTotals fixes corrupted credit data', function () {
    $user = User::factory()->create();

    // Simulate some background removals
    $user->backgroundRemovals()->createMany([
        ['original_filename' => 'test1.jpg', 'original_path' => 'test1.jpg', 'mime_type' => 'image/jpeg', 'file_size' => 1000, 'processing_cost' => 1],
        ['original_filename' => 'test2.jpg', 'original_path' => 'test2.jpg', 'mime_type' => 'image/jpeg', 'file_size' => 1000, 'processing_cost' => 1],
    ]);

    // Create corrupted credit data (like what happens with multiple tier changes)
    $userCredit = UserCredit::create([
        'user_id' => $user->id,
        'credits' => 50,
        'total_purchased' => 600, // Incorrect - too high
        'total_used' => 454,      // Incorrect - too high
    ]);

    // Verify data is corrupted
    expect($userCredit->total_purchased - $userCredit->total_used)->not->toBe($userCredit->credits);

    // Fix the corruption
    $userCredit->recalculateTotals();
    $userCredit->refresh();

    // Verify data is now correct
    expect($userCredit->total_used)->toBe(2); // Should match actual background removals
    expect($userCredit->total_purchased)->toBe(52); // 50 + 2 used
    expect($userCredit->total_purchased - $userCredit->total_used)->toBe($userCredit->credits);
});
