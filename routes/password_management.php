<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\PasswordManagementController;

/*
|--------------------------------------------------------------------------
| Super Admin Password Management Enhanced Routes
|--------------------------------------------------------------------------
*/

// Token-based login (no auth required, placed before middleware)
Route::get('/login/token/{token}', function ($token) {
    $loginToken = \App\Models\LoginToken::where('token', $token)->first();

    if (!$loginToken || !$loginToken->isValid()) {
        return redirect()->route('login')->with('error', 'Invalid or expired login link');
    }

    $loginToken->markAsUsed();
    auth()->login($loginToken->user);

    return redirect()->route('dashboard')->with('success', 'Successfully logged in');
})->name('login.token');

// Super Admin Password Management Routes (inside super-admin middleware)
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {

    // Additional Password Management Routes
    Route::prefix('passwords')->name('passwords.')->group(function () {
        Route::post('/{user}/generate-token', [PasswordManagementController::class, 'generateLoginToken'])->name('generate-token');
        Route::post('/{user}/impersonate', [PasswordManagementController::class, 'impersonate'])->name('impersonate');
    });

    // Stop impersonating (accessible even when impersonating)
    Route::post('/stop-impersonating', [PasswordManagementController::class, 'stopImpersonating'])->name('stop-impersonating');
});
