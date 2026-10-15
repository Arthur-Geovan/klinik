<?php

namespace Database\Seeders;

use App\Models\MUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MUser::query()->delete();

        $users = [
            [
                'name'          => 'Administrator',
                'email'         => 'admin@gmail.com',
                'password'      => Hash::make('123456'),
                'login_password' => '123456',
                'role'          => 'admin',
            ],
            [
                'name'          => 'Petugas',
                'email'         => 'petugas@gmail.com',
                'password'      => Hash::make('123456'),
                'login_password' => '123456',
                'role'          => 'dokter',
            ],
        ];

        foreach ($users as $user) {
            MUser::create($user);
        }
    }
}