<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisztikaController extends Controller
{
    //ogykepviseloposzt
    public function ogyKepviseloPoszt($kepviselo)
    {
        $result = DB::table('ogykepviselo_poszt')
            ->where([
                ['ogykepviselo_id','=',intval($kepviselo)],
                ['ev','=','2020'],
                ['honap','=','12'],
            ])
            ->orderBy('nap', 'ASC')
            ->get();

        return response()->json($result);
    }

    //orszmedia posztok
    public function orszMediaPoszt($orszmedia)
    {
        $result = DB::table('orszmedia_poszt')
            ->where([
                ['media_id','=',intval($orszmedia)],
                ['ev','=','2020'],
                ['honap','=','12'],
            ])
            ->orderBy('nap', 'ASC')
            ->get();

        return response()->json($result);
    }

    //helyimédia posztok
    public function localMediaPoszt($localmedia)
    {
        $result = DB::table('localmedia_poszt')
            ->where([
                ['media_id','=',intval($localmedia)],
                ['ev','=','2020'],
                ['honap','=','12'],
            ])
            ->orderBy('nap', 'ASC')
            ->get();

        return response()->json($result);
    }

    public function kepviseloPoszt($kepviselo)
    {
        $result = DB::table('kepviselo_poszt')
            ->where([
                ['users_id','=',intval($kepviselo)],
                ['ev','=','2020'],
                ['honap','=','12'],
            ])
            ->orderBy('nap', 'ASC')
            ->get();

        return response()->json($result);
    }

    //saját posztok
    public function frakciovezetoPoszt($kepviselo)
    {
        $result = DB::table('kepviselo_poszt')
            ->where([
                ['users_id','=',intval($kepviselo)],
                ['ev','=','2020'],
                ['honap','=','12'],
            ])
            ->orderBy('nap', 'ASC')
            ->get();

        return response()->json($result);
    }
}
