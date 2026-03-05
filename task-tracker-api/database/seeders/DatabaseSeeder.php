<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Energeek',
            'email' => 'admin@energeek.id',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $categories = ['TODO', 'IN PROGRESS', 'TESTING', 'DONE', 'PENDING'];
        
        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
