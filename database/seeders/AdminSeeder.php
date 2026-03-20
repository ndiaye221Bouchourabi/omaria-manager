<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'ndiaye.cheikhh20@gmail.com',
            'password' => bcrypt('LwuJ6622'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
