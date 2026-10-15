<?php

namespace Database\Seeders;

use App\Models\MUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ApotekerSeeder extends Seeder
{
    public function run(): void
    {
        MUser::updateOrCreate(
            ['email' => 'apoteker@klinik.com'],
            [
                'name' => 'Apoteker Klinik',
                'password' => Hash::make('Apoteker#2026'),
                'role' => 'apoteker',
            ]
        );
    }
}
