<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\PasswordRecovery;
use App\Livewire\Auth\Register;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/password-recovery', PasswordRecovery::class)->name('password.recovery');
Route::get('/password-reset', fn () => 'oi')->name('password.reset');

Route::middleware('auth')->group(function () {
    Route::get('/', Welcome::class)->name('dashboard');
});
