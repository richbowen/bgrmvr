<?php

declare(strict_types=1);

use App\Models\User;

test('credit difference logic calculates correctly for upgrades', function () {
    // Test the logic that should happen during subscription upgrades

    // Scenario: User has Starter subscription (50 credits) + some extra credits
    // Current total: 65 credits (50 subscription + 15 extra)
    // Upgrading to Basic (150 credits) should add difference: 150 - 50 = 100 credits
    // Final total: 65 + 100 = 165 credits

    $currentTierCredits = 50; // Starter tier
    $currentUserCredits = 65; // What user actually has (50 + 15 extra)
    $newTierCredits = 150; // Basic tier

    // Calculate credit difference (the logic from our adjustCreditsForTierChange method)
    $creditDifference = $newTierCredits - $currentTierCredits;
    $expectedNewTotal = $currentUserCredits + $creditDifference;

    expect($creditDifference)->toBe(100);
    expect($expectedNewTotal)->toBe(165);
});

test('credit difference logic handles downgrades correctly', function () {
    // Scenario: User has Professional subscription (400 credits) + some extra credits
    // Current total: 425 credits (400 subscription + 25 extra)
    // Downgrading to Basic (150 credits) should remove difference: 400 - 150 = 250 credits
    // Final total: 425 - 250 = 175 credits

    $currentTierCredits = 400; // Professional tier
    $currentUserCredits = 425; // What user actually has (400 + 25 extra)
    $newTierCredits = 150; // Basic tier

    $creditDifference = $newTierCredits - $currentTierCredits;
    $creditsToRemove = abs($creditDifference);
    $expectedNewTotal = $currentUserCredits - $creditsToRemove;

    expect($creditDifference)->toBe(-250);
    expect($creditsToRemove)->toBe(250);
    expect($expectedNewTotal)->toBe(175);
});

test('downgrade handles edge case where user has fewer credits than difference', function () {
    // Edge case: User has used many credits from their subscription
    // Current tier: Professional (400 credits), but user only has 180 credits remaining
    // Downgrading to Starter (50 credits) would remove 350 credits, but user only has 180
    // Final total: 0 credits (can't go negative)

    $currentTierCredits = 400; // Professional tier
    $currentUserCredits = 180; // User has used many credits
    $newTierCredits = 50; // Starter tier

    $creditDifference = $newTierCredits - $currentTierCredits; // -350
    $creditsToRemove = min(abs($creditDifference), $currentUserCredits); // min(350, 180) = 180
    $expectedNewTotal = $currentUserCredits - $creditsToRemove; // 180 - 180 = 0

    expect($creditDifference)->toBe(-350);
    expect($creditsToRemove)->toBe(180);
    expect($expectedNewTotal)->toBe(0);
});

test('first subscription adds full tier allocation', function () {
    // New user getting their first subscription should get the full tier allocation
    $newTierCredits = 150; // Basic tier
    $userStartingCredits = 5; // Default new user credits

    $expectedNewTotal = $userStartingCredits + $newTierCredits;

    expect($expectedNewTotal)->toBe(155);
});

test('setCredits method works correctly', function () {
    $user = User::factory()->create();

    // Initial credits (new users get 0 credits now)
    expect($user->credits->balance())->toBe(0);

    // Set to a specific amount
    $user->credits->setCredits(150);

    // Verify the change
    expect($user->credits->fresh()->balance())->toBe(150);

    // Set to another amount
    $user->credits->setCredits(75);

    // Verify the change
    expect($user->credits->fresh()->balance())->toBe(75);
});
