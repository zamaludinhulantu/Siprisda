<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk membuat/menjamin akun super admin.
     */
    public function run(): void
    {
        $email = env('SUPERADMIN_EMAIL', 'superadmin@bappeda.local');
        $password = env('SUPERADMIN_PASSWORD', 'Bappeda@123');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );
    }
}
