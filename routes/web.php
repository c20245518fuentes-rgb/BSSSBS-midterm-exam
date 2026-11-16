<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BillingInvoiceController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('register.store');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:3,1')
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', [AppointmentController::class, 'index']);
    Route::get('profile', [CustomerProfileController::class, 'show'])->name('profile.show');

    Route::resource('services', ServiceController::class)
        ->only(['index']);

    Route::resource('appointments', AppointmentController::class)
        ->only(['index', 'create', 'store']);

    Route::middleware('can:manage-studio')->group(function (): void {
        Route::resource('services', ServiceController::class)
            ->only(['create', 'store', 'update']);

        Route::resource('appointments', AppointmentController::class)
            ->only(['update']);

        Route::post('billing-invoices/{invoice}/pay', [BillingInvoiceController::class, 'pay'])
            ->name('billing-invoices.pay');
    });
});
