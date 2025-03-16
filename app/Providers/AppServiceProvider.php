<?php

namespace App\Providers;

use App\Enums\Can;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::unguard();

        foreach (Can::cases() as $can) {
            Gate::define(
                $can->value,
                fn (User $user) => $user->hasPermissionTo($can)
            );
        }
    }
}
