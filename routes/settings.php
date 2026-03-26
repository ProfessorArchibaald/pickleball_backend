<?php

use App\Http\Controllers\Settings\Profile\DeleteController as DeleteProfileController;
use App\Http\Controllers\Settings\Profile\EditController as EditProfileController;
use App\Http\Controllers\Settings\Profile\UpdateController as UpdateProfileController;
use App\Http\Controllers\Settings\Security\EditController as EditSecurityController;
use App\Http\Controllers\Settings\Security\UpdatePasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', EditProfileController::class)->name('profile.edit');
    Route::patch('settings/profile', UpdateProfileController::class)->name('profile.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::delete('settings/profile', DeleteProfileController::class)->name('profile.destroy');

    Route::get('settings/security', EditSecurityController::class)->name('security.edit');

    Route::put('settings/password', UpdatePasswordController::class)
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::inertia('settings/appearance', 'settings/Appearance')->name('appearance.edit');
});
