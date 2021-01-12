<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('media')->insert([
            'id' => 1,
            'name' => 'Kisalföld',
            'frakcio_id' => 1,
            'tipus' => 2,
            'status_id' => 1,
        ]);

        DB::table('media')->insert([
            'id' => 2,
            'name' => 'Győr+',
            'frakcio_id' => 1,
            'tipus' => 2,
            'status_id' => 1,
        ]);

        DB::table('media')->insert([
            'id' => 3,
            'name' => 'Origo',
            'frakcio_id' => null,
            'tipus' => 1,
            'status_id' => 1,
        ]);
    }
}
