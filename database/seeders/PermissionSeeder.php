<?php

namespace Database\Seeders;

use App\Enums\Can;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::query()->create(['key' => Can::BE_AN_ADMIN->value]);
    }
}
