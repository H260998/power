<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Set a valid ADMIN_EMAIL before initializing the administrator.');
        }

        if (Admin::where('email', $email)->exists()) {
            return;
        }

        if (! is_string($password) || strlen($password) < 12) {
            throw new \RuntimeException('Set ADMIN_PASSWORD with at least 12 characters before initializing the administrator.');
        }

        Admin::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Store Manager',
                'password' => Hash::make($password),
            ]
        );
    }
}
