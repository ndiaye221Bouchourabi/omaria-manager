<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'ndiaye.cheikhh20@gmail.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('Admin@1234'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}