<?php

use Conkard\Http\Controllers\ContactController;
use Conkard\Http\Controllers\V1\Auth\AuthenticatedSessionController;
use Conkard\Http\Controllers\V1\Auth\EmailVerificationNotificationController;
use Conkard\Http\Controllers\V1\Auth\NewPasswordController;
use Conkard\Http\Controllers\V1\Auth\PasswordResetLinkController;
use Conkard\Http\Controllers\V1\Auth\RegisteredUserController;
use Conkard\Http\Controllers\V1\Auth\VerifyEmailController;
use Conkard\Http\Controllers\V1\Card\CardController;
use Conkard\Http\Controllers\V1\Card\CardFieldTypeController;
use Conkard\Http\Controllers\V1\Card\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/register', [RegisteredUserController::class, 'store'])
            ->middleware('guest')
            ->name('register');

        Route::post('/login', [AuthenticatedSessionController::class, 'store'])
            ->middleware('guest')
            ->name('login');

        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
            ->middleware('guest')
            ->name('password.email');

        Route::post('/reset-password', [NewPasswordController::class, 'store'])
            ->middleware('guest')
            ->name('password.store');

        Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['auth', 'signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware(['auth', 'throttle:6,1'])
            ->name('verification.send');

        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->middleware('auth')
            ->name('logout');
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('cards/field-types', CardFieldTypeController::class)
            ->name('cards.field-types');

        Route::apiResource('cards', CardController::class);
        Route::apiResource('cards/{card}/images', ImageController::class)
            ->only(['store', 'destroy'])
            ->names('cards.images');

        Route::apiResource('contacts', ContactController::class)->except('update');
    });

});
