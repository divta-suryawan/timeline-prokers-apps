<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        User::create(array(
            'id' => rand(100, 500),
            'name' => 'Ayu Astuti S',
            'role' => 'admin',
            'email' => 'sekretarispena@gmail.com',
            'position' => 'Sekretaris',
            'password' => Hash::make('123456'),
        ));
    }
}
