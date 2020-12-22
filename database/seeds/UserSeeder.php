<?php

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Hash as Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'admin',
            'name' => 'Admin Admin',
            'role' => 1,
            'frakcio_id' => 1,
            'frakcio' => 'gyor_1',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
        ]);

        DB::table('users')->insert([
            'username' => 'frakcioadmin_gyor',
            'name' => 'Frakció Admin',
            'role' => 2,
            'frakcio_id' => 1,
            'frakcio' => 'gyor_1',
            'email' => 'fragyor@gmail.com',
            'password' => Hash::make('frakcioadmin'),
        ]);

        DB::table('users')->insert([
            'username' => 'frakcioadmin_sopron',
            'name' => 'Frakció Admin',
            'role' => 2,
            'frakcio_id' => 2,
            'frakcio' => 'sopron_1',
            'email' => 'frasopron@gmail.com',
            'password' => Hash::make('frakcioadmin'),
        ]);

        DB::table('users')->insert([
            'username' => 'frakciovezeto',
            'name' => 'Frakciovezeto Frakciovezeto',
            'role' => 3,
            'frakcio_id' => 1,
            'frakcio' => 'gyor_1',
            'email' => 'frakciovezeto@gmail.com',
            'password' => Hash::make('frakciovezeto'),
        ]);

        DB::table('users')->insert([
            'username' => 'kepviselo',
            'name' => 'Kepviselo Kepviselo',
            'role' => 4,
            'frakcio_id' => 1,
            'frakcio' => 'gyor_1',
            'email' => 'kepviselo@gmail.com',
            'password' => Hash::make('kepviselo'),
        ]);
    }
}
