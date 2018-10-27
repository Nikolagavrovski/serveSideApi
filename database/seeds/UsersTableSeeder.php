<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = [
          'name' => 'Nikola Gavrovski',
          'email' => 'nikolagavr@gmail.com',
          'password' => Hash::make('password')
        ];
        User::create($user1);

    }
}
