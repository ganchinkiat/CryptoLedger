<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'gan_chinkiat@hotmail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
