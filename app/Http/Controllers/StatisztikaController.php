<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    public function kepviseloPoszt(Request $request)
    {
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'integer' => 'A(z) :attribute mező csak számokat tartalmazhat.',
        ];

        $validator = Validator::make($request->all(), [
            'ev' => 'required|integer',
            'honap' => 'required|integer',
            'kepviselo' => 'required|integer',
        ], $messages);

        if ($validator->fails()) {
            $error_msg = [];
            foreach($validator->errors()->all() as $err) {
                $error_msg[] = (string)$err;
            }
            $result = ['errors' => $error_msg];
            return response()->json($result,400);
        }

        //kiadjuk az adatokat egy csomagban amit a képviselőhöz kell megjeleníteni

        //a havi összes poszt számát

        $result = DB::table('kepviselo_poszt')
            ->select('ev','honap','nap','kovetok_szama')
            ->where([
                ['users_id','=',intval($request->kepviselo)],
                ['ev','=',$request->ev],
                ['honap','=',$request->honap],
            ])
            ->orderBy('nap', 'ASC')
            ->get();



        $response = [
            'post_datas' => $result,
        ];

        return response()->json($response);
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
