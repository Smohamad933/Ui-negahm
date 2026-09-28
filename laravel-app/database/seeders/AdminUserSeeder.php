<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Idempotent: only creates the default admin the first time the app runs
     * (mirrors ensureDefaultAdmin() from the previous Next.js version).
     */
    public function run(): void
    {
        if (AdminUser::query()->count() > 0) {
            return;
        }

        $username = env('ADMIN_USERNAME', 'Mohusyn');
        $password = env('ADMIN_PASSWORD', 'Smosh1387');

        AdminUser::query()->create([
            'username' => $username,
            'password_hash' => Hash::make($password),
            'name' => 'مدیر سایت',
        ]);

        $this->command?->info("Default admin created -> username: {$username} / password: {$password}");
    }
}
