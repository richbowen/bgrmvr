<?php

use App\Http\Controllers\BackgroundRemovalController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('dashboard', \App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('remove-background', 'background-removal-tool')
    ->middleware(['auth', 'verified'])
    ->name('background-removal.tool');

Route::get('history', \App\Livewire\BackgroundRemovalHistory::class)
    ->middleware(['auth', 'verified'])
    ->name('background-removal.history');

Route::get('credits', \App\Livewire\Credits::class)
    ->middleware(['auth', 'verified'])
    ->name('credits');

Route::get('trial-signup/{tier?}', \App\Livewire\TrialSignup::class)
    ->name('trial-signup');

Route::get('tiers', \App\Livewire\Tiers::class)
    ->middleware(['auth', 'verified'])
    ->name('tiers');

Route::get('background-removal/{type}/{uuid}/view', [BackgroundRemovalController::class, 'view'])
    ->middleware(['auth'])
    ->name('background-removal.view')
    ->where(['type' => 'original|processed']);

Route::get('background-removal/{type}/{uuid}/download', [BackgroundRemovalController::class, 'download'])
    ->middleware(['auth'])
    ->name('background-removal.download')
    ->where(['type' => 'original|processed']);

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/subscription', \App\Livewire\Settings\Subscription::class)->name('settings.subscription');
    Route::get('settings/credits', \App\Livewire\Settings\Credits::class)->name('settings.credits');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

require __DIR__.'/auth.php';
