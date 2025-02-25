<?php

use App\Enums\Can;
use App\Livewire\Admin;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\PasswordRecovery;
use App\Livewire\Auth\PasswordReset;
use App\Livewire\Auth\Register;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;

// region Login Flow
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/email-validation', fn () => 'oi')->middleware('auth')->name('email-validation');
Route::get('/password-recovery', PasswordRecovery::class)->name('password.recovery');
Route::get('/password-reset', PasswordReset::class)->name('password.reset');
// endregion

// region Authenticate
Route::middleware('auth')->group(function () {
    Route::get('/', Welcome::class)->name('dashboard');

    // region Admin
    Route::prefix('admin/')->middleware('can:' . Can::BE_AN_ADMIN->value)->group(function () {
        Route::get('/dashboard', Admin\Dashboard::class)->name('admin.dashboard');
        Route::get('/users', Admin\Users\Index::class)->name('admin.users');
    });
    // endregion
});
// endregion
