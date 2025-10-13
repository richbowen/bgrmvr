<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\CreditPurchaseService;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->service = app(CreditPurchaseService::class);
});

test('processes checkout session with valid metadata', function () {
    Log::spy();

    $sessionData = [
        'metadata' => [
            'user_id' => $this->user->id,
            'credits' => '50',
            'package_id' => 'starter_pack',
        ],
    ];

    $initialCredits = $this->user->credits->credits;
    $result = $this->service->processCheckoutSession($sessionData);

    expect($result)->toBeTrue();
    expect($this->user->fresh()->credits->credits)->toBe($initialCredits + 50);

    Log::shouldHaveReceived('info')
        ->with('Credits purchased successfully', [
            'user_id' => $this->user->id,
            'package_id' => 'starter_pack',
            'credits_added' => 50,
            'total_credits' => $initialCredits + 50,
        ]);
});

test('fails with missing metadata', function () {
    $sessionData = [
        'metadata' => [
            'user_id' => $this->user->id,
            // missing credits and package_id
        ],
    ];

    $result = $this->service->processCheckoutSession($sessionData);

    expect($result)->toBeFalse();
});

test('fails with invalid user id', function () {
    $sessionData = [
        'metadata' => [
            'user_id' => '99999',
            'credits' => '50',
            'package_id' => 'starter_pack',
        ],
    ];

    $result = $this->service->processCheckoutSession($sessionData);

    expect($result)->toBeFalse();
});
