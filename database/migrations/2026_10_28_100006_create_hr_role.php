<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Роль HR: бачить лише вакансії, кандидатів і налаштування HR.
     * Міграцією, а не сидером — щоб роль з'явилась на сервері разом із кодом.
     */
    public function up(): void
    {
        Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Role::where('name', 'hr')->where('guard_name', 'web')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
