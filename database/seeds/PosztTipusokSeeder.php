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
            'code' => 'sajat',
            'web_nev' => 'Saját',
        ]);

        DB::table('poszt_tipusok')->insert([
            'id' => 2,
            'code' => 'szemelyes',
            'web_nev' => 'Személyes',
        ]);

        DB::table('poszt_tipusok')->insert([
            'id' => 3,
            'code' => 'polgarmesteri',
            'web_nev' => 'Polgármesteri',
        ]);

        DB::table('poszt_tipusok')->insert([
            'id' => 4,
            'code' => 'alpolgarmesteri',
            'web_nev' => 'Alpolgármesteri',
        ]);

        DB::table('poszt_tipusok')->insert([
            'id' => 5,
            'code' => 'csoportoldal',
            'web_nev' => 'Csoport oldal',
        ]);

        DB::table('poszt_tipusok')->insert([
            'id' => 6,
            'code' => 'media',
            'web_nev' => 'Média',
        ]);

        DB::table('poszt_tipusok')->insert([
            'id' => 7,
            'code' => 'kepviselotars',
            'web_nev' => 'Képviselőtárs',
        ]);

        DB::table('poszt_tipusok')->insert([
            'id' => 8,
            'code' => 'egyeb',
            'web_nev' => 'Egyéb',
        ]);
    }
}
