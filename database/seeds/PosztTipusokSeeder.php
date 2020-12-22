<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as DB;

class PosztTipusokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('poszt_tipusok')->insert([
            'id' => 1,
            'code' => 'polgarmesteri',
            'web_nev' => 'Polgármesteri',
        ]);

        DB::table('poszt_tipusok')->insert([
            'id' => 2,
            'code' => 'alpolgarmesteri',
            'web_nev' => 'Alpolgármesteri',
        ]);

        DB::table('poszt_tipusok')->insert([
            'id' => 3,
            'code' => 'altalanos',
            'web_nev' => 'Általános',
        ]);

        DB::table('poszt_tipusok')->insert([
            'id' => 4,
            'code' => 'szemelyes',
            'web_nev' => 'Személyes',
        ]);

        DB::table('poszt_tipusok')->insert([
            'id' => 5,
            'code' => 'ogykepviselo',
            'web_nev' => 'Országgyűlési képviselő',
        ]);
    }
}
