<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PenjualSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Penjual',
            'email' => 'penjual@gmail.com',
            'password' => '12345678',
            'role' => 'penjual',
        ]);
    }
}