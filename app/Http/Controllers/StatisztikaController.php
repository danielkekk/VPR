<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'honap' => 'required',
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

        //összes poszt, lekérdezzük a hónapban a kepviselohoz tartozo sorokat, összeszámoljuk
        $haviPosztok = DB::table('kepviselo_poszt')
            ->select('nap','stat_poszt_sum','stat_reakciok_sum','stat_sajat_sum','stat_szemelyes_sum',
                'stat_polgarmesteri_sum','stat_alpolgarmesteri_sum','stat_csoportoldal_sum','stat_media_sum',
                'stat_kepviselotars_sum','stat_egyeb_sum','stat_atlag_hm')
            ->where([
                ['users_id','=',intval($request->kepviselo)],
                ['ev','=',$request->ev],
                ['honap','=',$request->honap],
            ])
            ->orderBy('nap', 'ASC')
            ->get();
        $napokSzama = cal_days_in_month(CAL_GREGORIAN,(int)substr($request->honap, -1),(int)$request->ev);

        $sum_poszt = 0;
        $sum_reakciok = 0;
        $sum_sajat = 0;
        $sum_szemelyes = 0;
        $sum_alpolg = 0;
        $sum_polg = 0;
        $sum_csoportoldal = 0;
        $sum_kepviselotars = 0;
        $sum_media = 0;
        $sum_egyeb = 0;
        $sum_atlag_hm = 0;
        $inaktiv_napok = $napokSzama - count($haviPosztok);
        $atlag_napi_poszt = 0;
        //végigmegyünk a lekérdezésen, összeadjuk a smokat
        foreach($haviPosztok as $post) {
            $sum_poszt += (int)$post->stat_poszt_sum;
            $sum_reakciok += (int)$post->stat_reakciok_sum;
            $sum_sajat += (int)$post->stat_sajat_sum;
            $sum_szemelyes += (int)$post->stat_szemelyes_sum;
            $sum_alpolg += (int)$post->stat_alpolgarmesteri_sum;
            $sum_polg += (int)$post->stat_polgarmesteri_sum;
            $sum_csoportoldal += (int)$post->stat_csoportoldal_sum;
            $sum_media += (int)$post->stat_media_sum;
            $sum_kepviselotars += (int)$post->stat_kepviselotars_sum;
            $sum_egyeb += (int)$post->stat_egyeb_sum;
            $sum_atlag_hm += round($post->stat_atlag_hm, 2);
        }
        $atlag_napi_poszt = round(($sum_poszt / $napokSzama), 2);
        $sum_atlag_hm = round(($sum_atlag_hm / $napokSzama), 2);
        $kepviseloDatas = [
            'sum_poszt' => $sum_poszt,
            'sum_reakciok' => $sum_reakciok,
            'sum_sajat' => $sum_sajat,
            'sum_szemelyes' => $sum_szemelyes,
            'sum_alpolg' => $sum_alpolg,
            'sum_polg' => $sum_polg,
            'sum_csoportoldal' => $sum_csoportoldal,
            'sum_media' => $sum_media,
            'sum_kepviselotars' => $sum_kepviselotars,
            'sum_egyeb' => $sum_egyeb,
            'sum_atlag_hm' => $sum_atlag_hm,
            'atlag_napi_poszt' => $atlag_napi_poszt,
            'inaktiv_napok' => $inaktiv_napok
        ];


        //a havi összes poszt számát
        $result = [];
        $kovetokSzama = DB::table('kepviselo_poszt')
            ->select('ev','honap','nap','kovetok_szama')
            ->where([
                ['users_id','=',intval($request->kepviselo)],
                ['ev','=',$request->ev],
                ['honap','=',$request->honap],
            ])
            ->orderBy('nap', 'ASC')
            ->get();

        if($kovetokSzama->count() != 0) {
            $kepviseloDatas['kovetok_szama'] = ($kovetokSzama->count() != 0) ? ($kovetokSzama->last())->kovetok_szama : 0;
            $kepviseloDatas['uj_kovetok'] = ($kovetokSzama->count() != 0) ? (($kovetokSzama->last())->kovetok_szama - ($kovetokSzama->first())->kovetok_szama) : 0;

            $aktualisKovSzama = $kovetokSzama[0]->kovetok_szama;

            foreach($kovetokSzama as $row) {
                $key = 'nap_'.str_pad($row->nap, 2, "0", STR_PAD_LEFT);
                $result[$key] = [
                    $row->honap . '. ' . str_pad($row->nap, 2, "0", STR_PAD_LEFT) . '.',
                    $row->kovetok_szama
                ];
            }

            for($i=0; $i<$napokSzama; $i++) {
                $key = 'nap_'.str_pad(($i+1), 2, "0", STR_PAD_LEFT);
                if(isset($result[$key])) {
                    $aktualisKovSzama = $result[$key][1];
                } else {
                    $result[$key] = [
                        $request->honap . '. ' . str_pad(($i+1), 2, "0", STR_PAD_LEFT) . '.',
                        $aktualisKovSzama
                    ];
                }
            }
        }


        //lekérdezzük az összes posztot
        //amelyik nap van poszt ott két érték kell
        //nap label, posztok száma, összes reakció/követők száma %
        $resultMegosztasiHatekonysag = [];
        $megosztasiHatekonysag = DB::table('kepviselo_poszt')
            ->select('ev','honap','nap','kovetok_szama','stat_reakciok_sum')
            ->where([
                ['users_id','=',intval($request->kepviselo)],
                ['ev','=',$request->ev],
                ['honap','=',$request->honap],
            ])
            ->orderBy('nap', 'ASC')
            ->get();

        //for ciklus végigmegyünk kitöltjük a tömböt



        $response = [
            'post_datas' => $result,
            'kepviselo_datas' => $kepviseloDatas,
        ];

        return response()->json($response);
    }

    public function haviKimutatas(Request $request)
    {
        $napokSzama = 0;
        $response = [
            'inaktivNapok' => [],
            'posztokSzama' => [],
            'reakciokSzama' => [],
            'kovetokSzama' => [],
            'ujKovetokSzama' => []
        ];

        $authenticatedUser = Auth::user();
        if(!$authenticatedUser || !isset($authenticatedUser->frakcio_id)) {
            //TODO hiba
        }

        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'integer' => 'A(z) :attribute mező csak számokat tartalmazhat.',
        ];

        $validator = Validator::make($request->all(), [
            'ev' => 'required|integer',
            'honap' => 'required',
        ], $messages);

        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->errors()->all() as $err) {
                $error_msg[] = (string)$err;
            }
            $result = ['errors' => $error_msg];
            return response()->json($result, 400);
        }

        $napokSzama = cal_days_in_month(CAL_GREGORIAN,(int)substr($request->honap, -1),(int)$request->ev);


        //inaktiv napok szama
        $sql = 'SELECT us.name, COUNT(kp.users_id) AS neminaktiv_napok_szama
                FROM kepviselo_poszt AS kp LEFT JOIN users AS us ON us.id=kp.users_id
                WHERE users_id IN (SELECT id FROM users WHERE frakcio_id=? AND role IN (3,4)) AND kp.ev=? AND kp.honap=?
                GROUP BY kp.users_id, us.name
                ORDER BY COUNT(kp.users_id) ASC;';
        $inaktivResult = DB::select($sql, [trim($authenticatedUser->frakcio_id),(int)$request->ev,$request->honap]);
        foreach($inaktivResult as $res) {
            $response['inaktivNapok'][] = [
                trim($res->name),
                ($napokSzama - $res->neminaktiv_napok_szama)
            ];
        }


        //posztok szama
        //lekérdezzük
        $sql = 'SELECT us.name, SUM(kp.stat_poszt_sum) AS posztok_szama
                FROM kepviselo_poszt AS kp LEFT JOIN users AS us ON us.id=kp.users_id
                WHERE kp.users_id IN (SELECT id FROM users WHERE frakcio_id=? AND role IN (3,4)) AND kp.ev=? AND kp.honap=?
                GROUP BY kp.users_id, us.name
                ORDER BY SUM(kp.stat_poszt_sum) DESC;';
        $inaktivResult = DB::select($sql, [trim($authenticatedUser->frakcio_id),(int)$request->ev,$request->honap]);
        foreach($inaktivResult as $res) {
            $response['posztokSzama'][] = [
                trim($res->name),
                $res->posztok_szama
            ];
        }

        $sql = 'SELECT us.name, SUM(kp.stat_reakciok_sum) AS reakciok_szama
                FROM kepviselo_poszt AS kp LEFT JOIN users AS us ON us.id=kp.users_id
                WHERE kp.users_id IN (SELECT id FROM users WHERE frakcio_id=? AND role IN (3,4)) AND kp.ev=? AND kp.honap=?
                GROUP BY kp.users_id, us.name
                ORDER BY SUM(kp.stat_reakciok_sum) DESC;';
        $inaktivResult = DB::select($sql, [trim($authenticatedUser->frakcio_id),$request->ev,$request->honap]);
        foreach($inaktivResult as $res) {
            $response['reakciokSzama'][] = [
                trim($res->name),
                $res->reakciok_szama
            ];
        }

        //print_r($inaktivResult); exit;

        //követők száma
        //hónap utolsó napján
        $sql = 'SELECT us.name, kp.kovetok_szama
                FROM kepviselo_poszt AS kp LEFT JOIN users AS us ON us.id=kp.users_id
                WHERE kp.users_id IN (SELECT id FROM users WHERE frakcio_id=? AND role IN (3,4)) AND kp.ev=? 
                AND kp.honap=?  AND kp.nap=?
                GROUP BY kp.users_id, us.name, kp.kovetok_szama
                ORDER BY kp.kovetok_szama DESC;';
        $inaktivResult = DB::select($sql, [
            trim($authenticatedUser->frakcio_id),
            (int)$request->ev,
            $request->honap,
            $napokSzama]);
        foreach($inaktivResult as $res) {
            $response['kovetokSzama'][] = [
                trim($res->name),
                $res->kovetok_szama
            ];
        }

        //új követők száma
        //ujKovetokSzama
        $kepviselok = User::where('frakcio_id', '=', trim($authenticatedUser->frakcio_id))->
        whereIn('role', array(3,4))->get();

        foreach($kepviselok as $kpv) {
            $kovetokSzama = DB::table('kepviselo_poszt')
                ->select('ev','honap','nap','kovetok_szama')
                ->where([
                    ['users_id','=',intval($kpv->id)],
                    ['ev','=',$request->ev],
                    ['honap','=',$request->honap],
                ])
                ->orderBy('nap', 'ASC')
                ->get();

            if($kovetokSzama->count() != 0) {
                $response['ujKovetokSzama'][] = [
                    trim($kpv->name),
                    ($kovetokSzama->last())->kovetok_szama - ($kovetokSzama->first())->kovetok_szama
                ];
            }
        }


        return response()->json(['response' => $response], 200);
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

    public function topKimutatas(Request $request)
    {
        $today = new \DateTime(date('Ymd'));
        $ev = '2020';//$today->format('Y');
        $honap = '12';//$today->format('m');

        $response_kepviselo_poszt = $this->getTopArray('kepviselo_poszt', $ev, $honap);
        $response_ogykepviselo_poszt = $this->getTopArray('ogykepviselo_poszt', $ev, $honap);
        $response_helyimedia_poszt = $this->getTopArray('localmedia_poszt', $ev, $honap);
        $response_orszmedia_poszt = $this->getTopArray('orszmedia_poszt', $ev, $honap);

        return response()->json([
            'response_kepviselo_poszt' => array_slice($response_kepviselo_poszt, 0, 3),
            'response_ogykepviselo_poszt' => array_slice($response_ogykepviselo_poszt, 0, 3),
            'response_helyimedia_poszt' => array_slice($response_helyimedia_poszt, 0, 3),
            'response_orszmedia_poszt' => array_slice($response_orszmedia_poszt, 0, 3),
        ], 200);
    }

    private function getTopArray($table='kepviselo_poszt', $ev, $honap) {
        $posztok = [];
        $hms = [];

        $result = DB::table($table)
            ->where([
                ['ev','=',$ev],
                ['honap','=',$honap],
            ])
            ->get();

        foreach($result as $row) {
            $rowPosztok = json_decode($row->posztok, true);
            foreach($rowPosztok as $rowPoszt) {
                $posztok[] = [
                    'datum' => trim($row->ev) . trim($row->honap) . trim($row->nap),
                    'reakcio' => $rowPoszt['reakcio'],
                    'tipus' => $rowPoszt['tipus'],
                    'link' => $rowPoszt['link'],
                    'HM' => (float)$rowPoszt['HM'],
                ];
            }
        }

        //rendezés hm szerint
        foreach ($posztok as $key => $row)
        {
            $hms[$key] = $row['HM'];
        }
        array_multisort($hms, SORT_DESC, $posztok);

        return $posztok;
    }
}
