<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as DB;

class FrakciokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('frakciok')->insert([
            'id' => 1,
            'name' => 'Győri frakció',
            'code' => 'gyor_1',
            'varos' => 'Győr',
        ]);

        DB::table('frakciok')->insert([
            'id' => 2,
            'name' => 'Soproni frakció',
            'code' => 'sopron_1',
            'varos' => 'Sopron',
        ]);
    }
}
