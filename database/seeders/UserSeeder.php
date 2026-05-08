<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing users if needed
        User::query()->delete();


        // Create default user
        User::create([
            'name' => 'Default User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
