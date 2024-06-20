<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\PasswordRecovery;
use App\Livewire\Auth\PasswordReset;
use App\Livewire\Auth\Register;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;

//region Login Flow
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/password-recovery', PasswordRecovery::class)->name('password.recovery');
Route::get('/password-reset', PasswordReset::class)->name('password.reset');
//endregion

//region Authenticate
Route::middleware('auth')->group(function () {
    Route::get('/', Welcome::class)->name('dashboard');

    //region Admin
    Route::prefix('admin/')->middleware('can:be-an-admin')->group(function () {
        Route::get('/dashboard', fn () => 'Admin')->name('admin.dashboard');
    });
    //endregion
});
//endregion
