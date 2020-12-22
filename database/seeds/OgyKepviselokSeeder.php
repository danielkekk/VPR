<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as DB;

class OgyKepviselokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ogykepviselok')->insert([
            'id' => 1,
            'name' => 'Szijjártó Péter',
            'status_id' => 1,
        ]);

        DB::table('ogykepviselok')->insert([
            'id' => 2,
            'name' => 'Simon Robi',
            'status_id' => 1,
        ]);
    }
}
