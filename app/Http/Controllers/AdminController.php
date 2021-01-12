<?php

namespace App\Http\Controllers;

use App\Business\Statistics\PostStat;
use App\Frakcio;
use App\Imports\KepviseloAdatCSVImport;
use App\KepviseloPoszt;
use App\LocalMediaPoszt;
use App\Media;
use App\OgyKepviselok;
use App\OgyKepviseloPoszt;
use App\OrszMediaPoszt;
use App\PosztTipusok;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin');
    }

    public function ujKepviselo()
    {
        $frakciok = Frakcio::all();

        return view('admin.ujkepviselo', [
            'frakciok' => $frakciok,
        ]);
    }

    public function createKepviselo(Request $request)
    {
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'max' => 'Maximum 100 karakter.',
            'min' => 'A jelszó legalább 8 karakter hosszúságú kell legyen.',
            'integer' => 'A(z) :attribute mező csak számokat tartalmazhat.',
            'confirmed' => 'A két jelszó nem egyezik meg',
        ];

        $validator = Validator::make($request->all(), [
            'kepviselonev' => 'required|string|max:100',
            'kepviseloemail' => 'required|email',
            'password' => 'required|string|confirmed',
            'kepviselofrakcio' => 'required|integer',
            'kepviselorole' => 'required|integer',
        ], $messages);

        if ($validator->fails()) {
            return redirect('ujkepviselo')
                ->withErrors($validator)
                ->withInput();
        }
        //print_r($request->category); exit;
        /*$cat = explode(':' ,trim($request->category));*/

        $kepviselo = new User();
        $kepviselo->name = $request->kepviselonev;
        $kepviselo->email = $request->kepviseloemail;
        $kepviselo->password = Hash::make($request->password);
        $kepviselo->frakcio_id = $request->kepviselofrakcio;
        $kepviselo->role = $request->kepviselorole;
        $kepviselo->status_id = 1;
        $kepviselo->save();

        return redirect()->route('ujkepviselo');
    }

    public function ujKepviseloPoszt()
    {
        $kepviselok = User::whereIn('role', [3,4])->get();

        //lekérdezni az összes posztot
        //kirakni egy gomb szerkesztés adatok, egy gomb törlés,

        return view('admin.ujkepviseloposzt', [
            'kepviselok' => $kepviselok,
        ]);
    }

    public function createKepviseloPoszt(Request $request)
    {
        $posztDatum = new \DateTime(trim($request->poszt_datum));
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            //'max' => 'Maximum 100 karakter.',
            //'min' => 'A jelszó legalább 8 karakter hosszúságú kell legyen.',
            'integer' => 'A(z) :attribute mező csak számokat tartalmazhat.',
            'date' => 'Nem dátum.',
        ];

        $validator = Validator::make($request->all(), [
            'kepviselo' => 'required|integer',
            'poszt_datum' => 'required|date',
            'kovetok_szama' => 'required|integer|min:1',
        ], $messages);

        if ($validator->fails()) {
            return redirect('ujkepviseloposzt')
                ->withErrors($validator)
                ->withInput();
        }

        $letezikKepviseloPoszt = KepviseloPoszt::where('users_id','=',trim($request->kepviselo))
            ->where('ev','=',$posztDatum->format('Y'))
            ->where('honap','=',$posztDatum->format('m'))
            ->where('nap','=',$posztDatum->format('d'))
            ->first();

        if($letezikKepviseloPoszt) {
            $messageBag = new MessageBag();
            $messageBag->add('postexists', 'Ehhez a képviselőhöz erre a dátumra már készült bejegyzés.');
            return redirect('ujkepviseloposzt')
                ->withErrors($messageBag)
                ->withInput();
        }

        $posztok = [];

        $kepviseloPoszt = new KepviseloPoszt();
        $kepviseloPoszt->users_id = $request->kepviselo;
        $kepviseloPoszt->ev = $posztDatum->format('Y');
        $kepviseloPoszt->honap = $posztDatum->format('m');
        $kepviseloPoszt->nap = $posztDatum->format('d');
        $kepviseloPoszt->kovetok_szama = $request->kovetok_szama;
        $kepviseloPoszt->posztok = json_encode($posztok);
        $kepviseloPoszt->save();

        $posztTipusokSum = PostStat::getSumPosztTipusok($posztok);
        $kepviseloPoszt->stat_poszt_sum = PostStat::getSumPoszt($posztok);
        $kepviseloPoszt->stat_reakciok_sum = PostStat::getSumReakciok($posztok);
        $kepviseloPoszt->stat_sajat_sum = $posztTipusokSum['sajat'];
        $kepviseloPoszt->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
        $kepviseloPoszt->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
        $kepviseloPoszt->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
        $kepviseloPoszt->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
        $kepviseloPoszt->stat_media_sum = $posztTipusokSum['media'];
        $kepviseloPoszt->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
        $kepviseloPoszt->stat_egyeb_sum = $posztTipusokSum['egyeb'];
        $kepviseloPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);
        $kepviseloPoszt->save();

        /*return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);*/


        return redirect()->route('ujkepviseloposzt');
    }

    public function getKepviseloPosztok() {
        $kepviseloposztok = DB::select('select us.name,kp.id,kp.users_id,kp.kovetok_szama,kp.ev,kp.honap,kp.nap from kepviselo_poszt as kp 
                                     left join users as us on kp.users_id=us.id;', []);

        return view('admin.kepviseloposztok', [
            'kepviseloposztok' => $kepviseloposztok,
        ]);
    }

    public function deleteKepviseloPoszt($id)
    {
        $deleted = KepviseloPoszt::destroy(trim($id));
        if(($deleted === 0)) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'Törlés sikertelen.');
            return redirect('kepviseloposztok')
                ->withErrors($messageBag)
                ->withInput();
        }

        $kepviseloposztok = DB::select('select us.name,kp.id,kp.users_id,kp.ev,kp.honap,kp.nap from kepviselo_poszt as kp 
                                     left join users as us on kp.users_id=us.id;', []);

        return view('admin.kepviseloposztok', [
            'kepviseloposztok' => $kepviseloposztok,
        ]);
    }

    public function editKepviseloPosztView($id)
    {
        $poszttipusok = PosztTipusok::all();
        $napiposztok = KepviseloPoszt::where('id','=',trim($id))->first();

        return view('admin.editkepviseloposzt', [
            'kepviseloposztid' => trim($id),
            'poszttipusok' => $poszttipusok,
            'napiposztok' => json_decode($napiposztok->posztok, true)
        ]);
    }

    public function editKepviseloPoszt(Request $request)
    {
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'max' => 'Maximum 100 karakter.',
            'min' => 'A jelszó legalább 8 karakter hosszúságú kell legyen.',
            'integer' => 'A(z) :attribute mező csak számokat tartalmazhat.',
            'digits_between' => 'A(z) :attribute mező értéke 0-1000000 között lehet.',
        ];

        $validator = Validator::make($request->all(), [
            'azon' => 'required|integer|digits_between:0,10000000',
            'reakcio' => 'required|integer|digits_between:0,1000000',
            'poszttipus' => 'required|integer|min:1|max:1000',
            'url' => 'required|string|max:250',
        ], $messages);

        if ($validator->fails()) {
            if(isset($request->azon) && trim($request->azon)!=='') {
                return redirect('editkepviseloposzt/' . trim($request->azon))
                    ->withErrors($validator)
                    ->withInput();
            }

            return redirect('kepviseloposztok')
                ->withErrors($validator)
                ->withInput();
        }


        $napiKepviseloPoszt = KepviseloPoszt::where('id','=',trim($request->azon))->first();
        $posztTipus = PosztTipusok::where('id','=',trim($request->poszttipus))->first();
        if(!$napiKepviseloPoszt || !$posztTipus) {
            $messageBag = new MessageBag();
            $messageBag->add('postnotexist', 'Bejegyzés sikertelen. Nem létezik a megadott bejegyzés vagy poszttipus.');
            return redirect('kepviseloposztok')
                ->withErrors($messageBag)
                ->withInput();
        }


        $posztok = json_decode($napiKepviseloPoszt->posztok,true);
        $ujposzt = [
            'id' => count($posztok),
            'reakcio' => (int)$request->reakcio,
            'tipus' => trim($posztTipus->code),
            'link' => trim($posztTipus->url),
            'HM' => PostStat::getHM((int)$request->reakcio, intval($napiKepviseloPoszt->kovetok_szama)),
        ];
        $posztok[] = $ujposzt;

        $napiKepviseloPoszt->posztok = json_encode($posztok);
        $posztTipusokSum = PostStat::getSumPosztTipusok($posztok);
        $napiKepviseloPoszt->stat_poszt_sum = PostStat::getSumPoszt($posztok);
        $napiKepviseloPoszt->stat_reakciok_sum = PostStat::getSumReakciok($posztok);
        $napiKepviseloPoszt->stat_sajat_sum = $posztTipusokSum['sajat'];
        $napiKepviseloPoszt->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
        $napiKepviseloPoszt->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
        $napiKepviseloPoszt->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
        $napiKepviseloPoszt->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
        $napiKepviseloPoszt->stat_media_sum = $posztTipusokSum['media'];
        $napiKepviseloPoszt->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
        $napiKepviseloPoszt->stat_egyeb_sum = $posztTipusokSum['egyeb'];
        $napiKepviseloPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);

        if(!$napiKepviseloPoszt->save())
        {
            $messageBag = new MessageBag();
            $messageBag->add('postsavecrash', 'Mentés sikertelen.');
            return redirect('editkepviseloposzt/' . trim($request->azon))
                ->withErrors($messageBag)
                ->withInput();
        }

        return redirect()->route('editkepviseloposzt', ['id' => trim($request->azon)]);
    }

    public function deleteKepviseloPosztById($kpid,$pid)
    {
        /*törlés*/
        $kepviseloposzt = KepviseloPoszt::where('id','=',trim($kpid))->first();
        if(!$kepviseloposzt) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'A törlés sikertelen.');
            return redirect('editkepviseloposzt/' . trim($kpid))
                ->withErrors($messageBag)
                ->withInput();
        }
        $posztok = json_decode($kepviseloposzt->posztok,true);
        $ujposztok = [];
        foreach($posztok as $poszt) {
            if((int)$poszt['id'] != (int)$pid) {
                $ujposztok[] = $poszt;
            }
        }
        $kepviseloposzt->posztok = json_encode($ujposztok);
        $posztTipusokSum = PostStat::getSumPosztTipusok($ujposztok);
        $kepviseloposzt->stat_poszt_sum = PostStat::getSumPoszt($ujposztok);
        $kepviseloposzt->stat_reakciok_sum = PostStat::getSumReakciok($ujposztok);
        $kepviseloposzt->stat_sajat_sum = $posztTipusokSum['sajat'];
        $kepviseloposzt->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
        $kepviseloposzt->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
        $kepviseloposzt->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
        $kepviseloposzt->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
        $kepviseloposzt->stat_media_sum = $posztTipusokSum['media'];
        $kepviseloposzt->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
        $kepviseloposzt->stat_egyeb_sum = $posztTipusokSum['egyeb'];
        $kepviseloposzt->stat_atlag_hm = PostStat::getAtlagHM($ujposztok);
        $kepviseloposzt->save();

        return redirect()->route('editkepviseloposzt', ['id' => trim($kpid)]);
    }


    /*
     * OGY képviselők
    */
    public function ujOgyKepviselo()
    {
        return view('admin.ujogykepviselo', []);
    }

    public function createOgyKepviselo(Request $request)
    {
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'max' => 'Maximum 100 karakter.',
        ];

        $validator = Validator::make($request->all(), [
            'ogykepviselonev' => 'required|string|max:100',
        ], $messages);

        if ($validator->fails()) {
            return redirect('ujogykepviselo')
                ->withErrors($validator)
                ->withInput();
        }

        $ogykepviselo = new OgyKepviselok();
        $ogykepviselo->name = $request->ogykepviselonev;
        $ogykepviselo->status_id = 1;
        $ogykepviselo->save();

        return redirect()->route('ujogykepviselo');
    }

    public function ujOgyKepviseloPoszt()
    {
        $ogykepviselok = OgyKepviselok::all();

        return view('admin.ujogykepviseloposzt', [
            'ogykepviselok' => $ogykepviselok,
        ]);
    }

    public function createOgyKepviseloPoszt(Request $request)
    {
        $posztDatum = new \DateTime(trim($request->poszt_datum));
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'integer' => 'A(z) :attribute mező csak számokat tartalmazhat.',
            'date' => 'Nem dátum.',
        ];

        $validator = Validator::make($request->all(), [
            'ogykepviselo' => 'required|integer',
            'poszt_datum' => 'required|date',
            'kovetok_szama' => 'required|integer|min:1',
        ], $messages);

        if ($validator->fails()) {
            return redirect('ujogykepviseloposzt')
                ->withErrors($validator)
                ->withInput();
        }

        $letezikOgyKepviseloPoszt = OgyKepviseloPoszt::where('ogykepviselo_id','=',trim($request->ogykepviselo))
            ->where('ev','=',$posztDatum->format('Y'))
            ->where('honap','=',$posztDatum->format('m'))
            ->where('nap','=',$posztDatum->format('d'))
            ->first();

        if($letezikOgyKepviseloPoszt) {
            $messageBag = new MessageBag();
            $messageBag->add('postexists', 'Ehhez a képviselőhöz erre a dátumra már készült bejegyzés.');
            return redirect('ujogykepviseloposzt')
                ->withErrors($messageBag)
                ->withInput();
        }

        $posztok = [];

        $ogyKepviseloPoszt = new OgyKepviseloPoszt();
        $ogyKepviseloPoszt->ogykepviselo_id = $request->ogykepviselo;
        $ogyKepviseloPoszt->ev = $posztDatum->format('Y');
        $ogyKepviseloPoszt->honap = $posztDatum->format('m');
        $ogyKepviseloPoszt->nap = $posztDatum->format('d');
        $ogyKepviseloPoszt->kovetok_szama = $request->kovetok_szama;
        $ogyKepviseloPoszt->posztok = json_encode($posztok);
        $ogyKepviseloPoszt->save();

        $posztTipusokSum = PostStat::getSumPosztTipusok($posztok);
        $ogyKepviseloPoszt->stat_poszt_sum = PostStat::getSumPoszt($posztok);
        $ogyKepviseloPoszt->stat_reakciok_sum = PostStat::getSumReakciok($posztok);
        $ogyKepviseloPoszt->stat_sajat_sum = $posztTipusokSum['sajat'];
        $ogyKepviseloPoszt->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
        $ogyKepviseloPoszt->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
        $ogyKepviseloPoszt->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
        $ogyKepviseloPoszt->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
        $ogyKepviseloPoszt->stat_media_sum = $posztTipusokSum['media'];
        $ogyKepviseloPoszt->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
        $ogyKepviseloPoszt->stat_egyeb_sum = $posztTipusokSum['egyeb'];
        $ogyKepviseloPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);
        $ogyKepviseloPoszt->save();

        return redirect()->route('ujogykepviseloposzt');
    }

    public function getOgyKepviseloPosztok() {
        $ogykepviseloposztok = DB::select('select us.name,kp.id,kp.ogykepviselo_id,kp.kovetok_szama,kp.ev,kp.honap,kp.nap from ogykepviselo_poszt as kp 
                                     left join ogykepviselok as us on kp.ogykepviselo_id=us.id;', []);

        return view('admin.ogykepviseloposztok', [
            'ogykepviseloposztok' => $ogykepviseloposztok,
        ]);
    }

    public function deleteOgyKepviseloPoszt($id)
    {
        $deleted = OgyKepviseloPoszt::destroy(trim($id));
        if(($deleted === 0)) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'Törlés sikertelen.');
            return redirect('ogykepviseloposztok')
                ->withErrors($messageBag)
                ->withInput();
        }

        return redirect()->route('ogykepviseloposztok');
    }

    public function editOgyKepviseloPosztView($id)
    {
        //TODO ogynál más
        $poszttipusok = PosztTipusok::all();
        $napiposztok = OgyKepviseloPoszt::where('id','=',trim($id))->first();

        return view('admin.editogykepviseloposzt', [
            'ogykepviseloposztid' => trim($id),
            'poszttipusok' => $poszttipusok,
            'napiposztok' => json_decode($napiposztok->posztok, true)
        ]);
    }

    public function editOgyKepviseloPoszt(Request $request)
    {
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'max' => 'Maximum 100 karakter.',
            'min' => 'A jelszó legalább 8 karakter hosszúságú kell legyen.',
            'integer' => 'A(z) :attribute mező csak számokat tartalmazhat.',
            'digits_between' => 'A(z) :attribute mező értéke 0-1000000 között lehet.',
        ];

        $validator = Validator::make($request->all(), [
            'azon' => 'required|integer|digits_between:0,10000000',
            'reakcio' => 'required|integer|digits_between:0,1000000',
            'poszttipus' => 'required|integer|min:1|max:1000',
            'url' => 'required|string|max:250',
        ], $messages);

        if ($validator->fails()) {
            if(isset($request->azon) && trim($request->azon)!=='') {
                return redirect('editogykepviseloposzt/' . trim($request->azon))
                    ->withErrors($validator)
                    ->withInput();
            }

            return redirect('ogykepviseloposztok')
                ->withErrors($validator)
                ->withInput();
        }


        $napiOgyKepviseloPoszt = OgyKepviseloPoszt::where('id','=',trim($request->azon))->first();
        $posztTipus = PosztTipusok::where('id','=',trim($request->poszttipus))->first();
        if(!$napiOgyKepviseloPoszt || !$posztTipus) {
            $messageBag = new MessageBag();
            $messageBag->add('postnotexist', 'Bejegyzés sikertelen. Nem létezik a megadott bejegyzés vagy poszttipus.');
            return redirect('ogykepviseloposztok')
                ->withErrors($messageBag)
                ->withInput();
        }


        $posztok = json_decode($napiOgyKepviseloPoszt->posztok,true);
        $ujposzt = [
            'id' => count($posztok),
            'reakcio' => (int)$request->reakcio,
            'tipus' => trim($posztTipus->code),
            'link' => trim($posztTipus->url),
            'HM' => PostStat::getHM((int)$request->reakcio, intval($napiOgyKepviseloPoszt->kovetok_szama)),
        ];
        $posztok[] = $ujposzt;

        $napiOgyKepviseloPoszt->posztok = json_encode($posztok);
        $posztTipusokSum = PostStat::getSumPosztTipusok($posztok);
        $napiOgyKepviseloPoszt->stat_poszt_sum = PostStat::getSumPoszt($posztok);
        $napiOgyKepviseloPoszt->stat_reakciok_sum = PostStat::getSumReakciok($posztok);
        $napiOgyKepviseloPoszt->stat_sajat_sum = $posztTipusokSum['sajat'];
        $napiOgyKepviseloPoszt->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
        $napiOgyKepviseloPoszt->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
        $napiOgyKepviseloPoszt->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
        $napiOgyKepviseloPoszt->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
        $napiOgyKepviseloPoszt->stat_media_sum = $posztTipusokSum['media'];
        $napiOgyKepviseloPoszt->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
        $napiOgyKepviseloPoszt->stat_egyeb_sum = $posztTipusokSum['egyeb'];
        $napiOgyKepviseloPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);

        if(!$napiOgyKepviseloPoszt->save())
        {
            $messageBag = new MessageBag();
            $messageBag->add('postsavecrash', 'Mentés sikertelen.');
            return redirect('editogykepviseloposzt/' . trim($request->azon))
                ->withErrors($messageBag)
                ->withInput();
        }

        return redirect()->route('editogykepviseloposzt', ['id' => trim($request->azon)]);
    }

    public function deleteOgyKepviseloPosztById($kpid,$pid)
    {
        /*törlés*/
        $ogykepviseloposzt = OgyKepviseloPoszt::where('id','=',trim($kpid))->first();
        if(!$ogykepviseloposzt) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'A törlés sikertelen.');
            return redirect('editogykepviseloposzt/' . trim($kpid))
                ->withErrors($messageBag)
                ->withInput();
        }
        $posztok = json_decode($ogykepviseloposzt->posztok,true);
        $ujposztok = [];
        foreach($posztok as $poszt) {
            if((int)$poszt['id'] != (int)$pid) {
                $ujposztok[] = $poszt;
            }
        }
        $ogykepviseloposzt->posztok = json_encode($ujposztok);
        $posztTipusokSum = PostStat::getSumPosztTipusok($ujposztok);
        $ogykepviseloposzt->stat_poszt_sum = PostStat::getSumPoszt($ujposztok);
        $ogykepviseloposzt->stat_reakciok_sum = PostStat::getSumReakciok($ujposztok);
        $ogykepviseloposzt->stat_sajat_sum = $posztTipusokSum['sajat'];
        $ogykepviseloposzt->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
        $ogykepviseloposzt->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
        $ogykepviseloposzt->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
        $ogykepviseloposzt->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
        $ogykepviseloposzt->stat_media_sum = $posztTipusokSum['media'];
        $ogykepviseloposzt->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
        $ogykepviseloposzt->stat_egyeb_sum = $posztTipusokSum['egyeb'];
        $ogykepviseloposzt->stat_atlag_hm = PostStat::getAtlagHM($ujposztok);
        $ogykepviseloposzt->save();

        return redirect()->route('editogykepviseloposzt', ['id' => trim($kpid)]);
    }



    /*
     * Országos média
    */
    public function ujOrszMedia()
    {
        return view('admin.ujorszmedia', []);
    }

    public function createOrszMedia(Request $request)
    {
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'max' => 'Maximum 100 karakter.',
        ];

        $validator = Validator::make($request->all(), [
            'orszmedianev' => 'required|string|max:90',
        ], $messages);

        if ($validator->fails()) {
            return redirect('ujorszmedia')
                ->withErrors($validator)
                ->withInput();
        }

        $orszmedia = new Media();
        $orszmedia->name = $request->orszmedianev;
        $orszmedia->tipus = 1;
        $orszmedia->status_id = 1;
        $orszmedia->save();

        return redirect()->route('ujorszmedia');
    }

    public function ujOrszMediaPoszt()
    {
        $orszmedia = Media::where('tipus','=',Media::ORSZAGOS)->get();

        return view('admin.ujorszmediaposzt', [
            'orszmediak' => $orszmedia,
        ]);
    }

    public function createOrszMediaPoszt(Request $request)
    {
        $posztDatum = new \DateTime(trim($request->poszt_datum));
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'integer' => 'A(z) :attribute mező csak számokat tartalmazhat.',
            'date' => 'Nem dátum.',
        ];

        $validator = Validator::make($request->all(), [
            'orszmedia' => 'required|integer',
            'poszt_datum' => 'required|date',
            'kovetok_szama' => 'required|integer|min:1',
        ], $messages);

        if ($validator->fails()) {
            return redirect('ujorszmediaposzt')
                ->withErrors($validator)
                ->withInput();
        }

        $letezikOrszMediaPoszt = OrszMediaPoszt::where('media_id','=',trim($request->orszmedia))
            ->where('ev','=',$posztDatum->format('Y'))
            ->where('honap','=',$posztDatum->format('m'))
            ->where('nap','=',$posztDatum->format('d'))
            ->first();

        if($letezikOrszMediaPoszt) {
            $messageBag = new MessageBag();
            $messageBag->add('postexists', 'Ehhez a médiához erre a dátumra már készült bejegyzés.');
            return redirect('ujorszmediaposzt')
                ->withErrors($messageBag)
                ->withInput();
        }

        $posztok = [];

        $orszMediaPoszt = new OrszMediaPoszt();
        $orszMediaPoszt->media_id = $request->orszmedia;
        $orszMediaPoszt->ev = $posztDatum->format('Y');
        $orszMediaPoszt->honap = $posztDatum->format('m');
        $orszMediaPoszt->nap = $posztDatum->format('d');
        $orszMediaPoszt->kovetok_szama = $request->kovetok_szama;
        $orszMediaPoszt->posztok = json_encode($posztok);
        $orszMediaPoszt->save();

        $posztTipusokSum = PostStat::getSumPosztTipusok($posztok);
        $orszMediaPoszt->stat_poszt_sum = PostStat::getSumPoszt($posztok);
        $orszMediaPoszt->stat_reakciok_sum = PostStat::getSumReakciok($posztok);
        $orszMediaPoszt->stat_sajat_sum = $posztTipusokSum['sajat'];
        $orszMediaPoszt->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
        $orszMediaPoszt->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
        $orszMediaPoszt->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
        $orszMediaPoszt->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
        $orszMediaPoszt->stat_media_sum = $posztTipusokSum['media'];
        $orszMediaPoszt->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
        $orszMediaPoszt->stat_egyeb_sum = $posztTipusokSum['egyeb'];
        $orszMediaPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);
        $orszMediaPoszt->save();

        return redirect()->route('ujorszmediaposzt');
    }

    public function getOrszMediaPosztok() {
        $orszmediaposztok = DB::select('select m.name,kp.id,kp.media_id,kp.kovetok_szama,kp.ev,kp.honap,kp.nap from orszmedia_poszt as kp 
                                     left join media as m on kp.media_id=m.id;', []);

        return view('admin.orszmediaposztok', [
            'orszmediaposztok' => $orszmediaposztok,
        ]);
    }

    public function deleteOrszMediaPoszt($id)
    {
        $deleted = OrszMediaPoszt::destroy(trim($id));
        if(($deleted === 0)) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'Törlés sikertelen.');
            return redirect('orszmediaposztok')
                ->withErrors($messageBag)
                ->withInput();
        }

        return redirect()->route('orszmediaposztok');
    }

    public function editOrszMediaPosztView($id)
    {
        //TODO orszmedianál más
        $poszttipusok = PosztTipusok::all();
        $napiposztok = OrszMediaPoszt::where('id','=',trim($id))->first();

        return view('admin.editorszmediaposzt', [
            'orszmediaposztid' => trim($id),
            'poszttipusok' => $poszttipusok,
            'napiposztok' => json_decode($napiposztok->posztok, true)
        ]);
    }

    public function editOrszMediaPoszt(Request $request)
    {
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'max' => 'Maximum 100 karakter.',
            'min' => 'A jelszó legalább 8 karakter hosszúságú kell legyen.',
            'integer' => 'A(z) :attribute mező csak számokat tartalmazhat.',
            'digits_between' => 'A(z) :attribute mező értéke 0-1000000 között lehet.',
        ];

        $validator = Validator::make($request->all(), [
            'azon' => 'required|integer|digits_between:0,10000000',
            'reakcio' => 'required|integer|digits_between:0,1000000',
            'poszttipus' => 'required|integer|min:1|max:1000',
            'url' => 'required|string|max:250',
        ], $messages);

        if ($validator->fails()) {
            if(isset($request->azon) && trim($request->azon)!=='') {
                return redirect('editorszmediaposzt/' . trim($request->azon))
                    ->withErrors($validator)
                    ->withInput();
            }

            return redirect('orszmediaposztok')
                ->withErrors($validator)
                ->withInput();
        }


        $napiOrszMediaPoszt = OrszMediaPoszt::where('id','=',trim($request->azon))->first();
        $posztTipus = PosztTipusok::where('id','=',trim($request->poszttipus))->first();
        if(!$napiOrszMediaPoszt || !$posztTipus) {
            $messageBag = new MessageBag();
            $messageBag->add('postnotexist', 'Bejegyzés sikertelen. Nem létezik a megadott bejegyzés vagy poszttipus.');
            return redirect('orszmediaposztok')
                ->withErrors($messageBag)
                ->withInput();
        }


        $posztok = json_decode($napiOrszMediaPoszt->posztok,true);
        $ujposzt = [
            'id' => count($posztok),
            'reakcio' => (int)$request->reakcio,
            'tipus' => trim($posztTipus->code),
            'link' => trim($posztTipus->url),
            'HM' => PostStat::getHM((int)$request->reakcio, intval($napiOrszMediaPoszt->kovetok_szama)),
        ];
        $posztok[] = $ujposzt;

        $napiOrszMediaPoszt->posztok = json_encode($posztok);
        $posztTipusokSum = PostStat::getSumPosztTipusok($posztok);
        $napiOrszMediaPoszt->stat_poszt_sum = PostStat::getSumPoszt($posztok);
        $napiOrszMediaPoszt->stat_reakciok_sum = PostStat::getSumReakciok($posztok);
        $napiOrszMediaPoszt->stat_sajat_sum = $posztTipusokSum['sajat'];
        $napiOrszMediaPoszt->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
        $napiOrszMediaPoszt->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
        $napiOrszMediaPoszt->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
        $napiOrszMediaPoszt->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
        $napiOrszMediaPoszt->stat_media_sum = $posztTipusokSum['media'];
        $napiOrszMediaPoszt->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
        $napiOrszMediaPoszt->stat_egyeb_sum = $posztTipusokSum['egyeb'];
        $napiOrszMediaPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);

        if(!$napiOrszMediaPoszt->save())
        {
            $messageBag = new MessageBag();
            $messageBag->add('postsavecrash', 'Mentés sikertelen.');
            return redirect('editorszmediaposzt/' . trim($request->azon))
                ->withErrors($messageBag)
                ->withInput();
        }

        return redirect()->route('editorszmediaposzt', ['id' => trim($request->azon)]);
    }

    public function deleteOrszMediaPosztById($kpid,$pid)
    {
        /*törlés*/
        $orszmediaposzt = OrszMediaPoszt::where('id','=',trim($kpid))->first();
        if(!$orszmediaposzt) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'A törlés sikertelen.');
            return redirect('editorszmediaposzt/' . trim($kpid))
                ->withErrors($messageBag)
                ->withInput();
        }
        $posztok = json_decode($orszmediaposzt->posztok,true);
        $ujposztok = [];
        foreach($posztok as $poszt) {
            if((int)$poszt['id'] != (int)$pid) {
                $ujposztok[] = $poszt;
            }
        }
        $orszmediaposzt->posztok = json_encode($ujposztok);
        $posztTipusokSum = PostStat::getSumPosztTipusok($ujposztok);
        $orszmediaposzt->stat_poszt_sum = PostStat::getSumPoszt($ujposztok);
        $orszmediaposzt->stat_reakciok_sum = PostStat::getSumReakciok($ujposztok);
        $orszmediaposzt->stat_sajat_sum = $posztTipusokSum['sajat'];
        $orszmediaposzt->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
        $orszmediaposzt->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
        $orszmediaposzt->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
        $orszmediaposzt->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
        $orszmediaposzt->stat_media_sum = $posztTipusokSum['media'];
        $orszmediaposzt->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
        $orszmediaposzt->stat_egyeb_sum = $posztTipusokSum['egyeb'];
        $orszmediaposzt->stat_atlag_hm = PostStat::getAtlagHM($ujposztok);
        $orszmediaposzt->save();

        return redirect()->route('editorszmediaposzt', ['id' => trim($kpid)]);
    }



    /*
     * EGYÉB
     */
    public function ujFrakcio()
    {
        $frakciok = Frakcio::all();

        return view('admin.ujfrakcio', [
            'frakciok' => $frakciok
        ]);
    }

    public function createFrakcio(Request $request)
    {
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'max' => 'Maximum 100 karakter.',
            'min' => 'A jelszó legalább 8 karakter hosszúságú kell legyen.',
            'integer' => 'A(z) :attribute mező csak számokat tartalmazhat.',
            'confirmed' => 'A két jelszó nem egyezik meg',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100',
            'varos' => 'required|string|max:100',
        ], $messages);

        if ($validator->fails()) {
            return redirect('ujfrakcio')
                ->withErrors($validator)
                ->withInput();
        }

        $frakcio = new Frakcio();
        $frakcio->name = $request->name;
        $frakcio->code = $request->code;
        $frakcio->varos = $request->varos;
        $frakcio->save();

        return redirect()->route('ujfrakcio');
    }

    public function deleteFrakcio($id)
    {
        $deleted = Frakcio::destroy(trim($id));
        if(($deleted === 0)) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'Törlés sikertelen.');
            return redirect('ujfrakcio')
                ->withErrors($messageBag)
                ->withInput();
        }

        return redirect()->route('ujfrakcio');
    }

    public function loadKepviseloAdatokView()
    {
        $kepviselok = User::whereIn('role', [3,4])->get();
        $ogykepviselok = OgyKepviselok::all();
        $helyimediak = Media::where('tipus', 2)->get();
        $orszagosmediak = Media::where('tipus', 1)->get();
        $tipusok = [
            1 => 'Képviselő poszt',
            2 => 'Ogy. Képviselő poszt',
            3 => 'Helyi média poszt',
            4 => 'Országos média poszt',
        ];

        return view('admin.loadkepviseloadatok', [
            'kepviselok' => $kepviselok,
            'ogykepviselok' => $ogykepviselok,
            'helyimediak' => $helyimediak,
            'orszagosmediak' => $orszagosmediak,
            'tipusok' => $tipusok,
        ]);
    }

    public function loadKepviseloAdatok(Request $request)
    {
        $poszttipusmap = [
            1 => 'sajat',
            2 => 'szemelyes',
            3 => 'polgarmesteri',
            4 => 'alpolgarmesteri',
            5 => 'csoportoldal',
            6 => 'media',
            7 => 'kepviselotars',
            8 => 'egyeb',
        ];

        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'mimes' => 'Csak .csv formátum',
            'max' => 'Max 2MB',
            'integer' => 'Csak számok',
        ];

        $validator = Validator::make($request->all(), [
            'kepviselo' => 'required|integer',
            'ogykepviselo' => 'required|integer',
            'helyimedia' => 'required|integer',
            'orszmedia' => 'required|integer',
            'poszttype' => 'required|integer',
            'file' => 'max:2048',
            //'file' => 'required|mimetypes:csv,txt,text/csv,text/plain,application/csv,text/comma-separated-values,text/anytext,application/octet-stream,application/txt|max:2048',
        ], $messages);

        if ($validator->fails()) {
            return redirect('loadkepviseloadatokview')
                ->withErrors($validator)
                ->withInput();
        }

        if (!$request->hasFile('filecsv')) {
            echo 'Hiba';
        }

        $file = $request->file('filecsv');

        //Display File Name
        echo 'File Name: '.$file->getClientOriginalName();
        echo '<br>';

        //Display File Extension
        echo 'File Extension: '.$file->getClientOriginalExtension();
        echo '<br>';

        //Display File Real Path
        echo 'File Real Path: '.$file->getRealPath();
        echo '<br>';

        //Display File Size
        echo 'File Size: '.$file->getSize();
        echo '<br>';

        //Display File Mime Type
        echo 'File Mime Type: '.$file->getMimeType();


        $path = $file->storeAs('uploadedcsvs', 'kepviseloadat.csv');


        //beolvasni
        //parseolni
        //$excel = new Excel(\Maatwebsite\Excel\Excel::CSV,\Maatwebsite\Excel\Excel::CSV,null,null);
        $array = Excel::toArray(new KepviseloAdatCSVImport, storage_path('app/uploadedcsvs/kepviseloadat.csv'),null, \Maatwebsite\Excel\Excel::CSV);

        //dd($array);

        //megnézzük h az adott userid és időponthoz van e bejegyezség
        //ha nincs insert
        //ha van lekérdezzük és hozzáadjuk a poszt adatot a json listához

        foreach($array as $a) {
            foreach($a as $b) {
                foreach($b as $c) {
                    $arr = explode(";", $c);
                    $datum = preg_replace('/\D/', '', trim($arr[0]));
                    $posztDatum = new \DateTime($datum);

                    if(mb_strlen($datum) != 8) {
                        //continue;
                    }

                    /*
                     * 1 => 'Képviselő poszt',
                        2 => 'Ogy. Képviselő poszt',
                        3 => 'Helyi média poszt',
                        4 => 'Országos média poszt',
                     */
                    $posztModel = null;
                    $ujPoszt = null;
                    if((int)$request->poszttype == 1) {
                        $posztModel = KepviseloPoszt::where('users_id','=',trim($request->kepviselo))
                            ->where('ev','=',$posztDatum->format('Y'))
                            ->where('honap','=',$posztDatum->format('m'))
                            ->where('nap','=',$posztDatum->format('d'))
                            ->first();
                        $ujPoszt = new KepviseloPoszt();
                        $ujPoszt->users_id = trim($request->kepviselo);
                    } else if((int)$request->poszttype == 2) {
                        $posztModel = OgyKepviseloPoszt::where('ogykepviselo_id','=',trim($request->ogykepviselo))
                            ->where('ev','=',$posztDatum->format('Y'))
                            ->where('honap','=',$posztDatum->format('m'))
                            ->where('nap','=',$posztDatum->format('d'))
                            ->first();
                        $ujPoszt = new OgyKepviseloPoszt();
                        $ujPoszt->ogykepviselo_id = trim($request->ogykepviselo);
                    } else if((int)$request->poszttype == 3) {
                        $posztModel = LocalMediaPoszt::where('media_id','=',trim($request->helyimedia))
                            ->where('ev','=',$posztDatum->format('Y'))
                            ->where('honap','=',$posztDatum->format('m'))
                            ->where('nap','=',$posztDatum->format('d'))
                            ->first();
                        $ujPoszt = new LocalMediaPoszt();
                        $ujPoszt->media_id = trim($request->helyimedia);
                    } else if((int)$request->poszttype == 4) {
                        $posztModel = OrszMediaPoszt::where('media_id','=',trim($request->orszmedia))
                            ->where('ev','=',$posztDatum->format('Y'))
                            ->where('honap','=',$posztDatum->format('m'))
                            ->where('nap','=',$posztDatum->format('d'))
                            ->first();
                        $ujPoszt = new OrszMediaPoszt();
                        $ujPoszt->media_id = trim($request->orszmedia);
                    }

                    $posztok = [];
                    if($posztModel && ((int)$arr[2]!=0 && (int)$arr[3]!=0)) {
                        $posztok = json_decode($posztModel->posztok,true);
                        $ujposzt = [
                            'id' => count($posztok)+1,
                            'reakcio' => (int)$arr[2],
                            'tipus' => $poszttipusmap[(int)$arr[3]],
                            'link' => trim($arr[4]),
                            'HM' => PostStat::getHM((int)$arr[2], intval($arr[1])),
                        ];
                        $posztok[] = $ujposzt;

                        $posztModel->posztok = json_encode($posztok);
                        $posztTipusokSum = PostStat::getSumPosztTipusok($posztok);
                        $posztModel->stat_poszt_sum = PostStat::getSumPoszt($posztok);
                        $posztModel->stat_reakciok_sum = PostStat::getSumReakciok($posztok);
                        $posztModel->stat_sajat_sum = $posztTipusokSum['sajat'];
                        $posztModel->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
                        $posztModel->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
                        $posztModel->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
                        $posztModel->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
                        $posztModel->stat_media_sum = $posztTipusokSum['media'];
                        $posztModel->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
                        $posztModel->stat_egyeb_sum = $posztTipusokSum['egyeb'];
                        $posztModel->stat_atlag_hm = PostStat::getAtlagHM($posztok);
                        $posztModel->save();
                    } else if(!$posztModel) {
                        $posztok[] = [
                            'id' => 1,
                            'reakcio' => (int)$arr[2],
                            'tipus' => $poszttipusmap[(int)$arr[3]],
                            'link' => trim($arr[4]),
                            'HM' => PostStat::getHM((int)$arr[2], intval($arr[1])),
                        ];

                        $ujPoszt->ev = $posztDatum->format('Y');
                        $ujPoszt->honap = $posztDatum->format('m');
                        $ujPoszt->nap = $posztDatum->format('d');
                        $ujPoszt->kovetok_szama = (int)$arr[1];
                        $ujPoszt->posztok = json_encode($posztok);
                        $ujPoszt->save();

                        $posztTipusokSum = PostStat::getSumPosztTipusok($posztok);
                        $ujPoszt->stat_poszt_sum = PostStat::getSumPoszt($posztok);
                        $ujPoszt->stat_reakciok_sum = PostStat::getSumReakciok($posztok);
                        $ujPoszt->stat_sajat_sum = $posztTipusokSum['sajat'];
                        $ujPoszt->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
                        $ujPoszt->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
                        $ujPoszt->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
                        $ujPoszt->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
                        $ujPoszt->stat_media_sum = $posztTipusokSum['media'];
                        $ujPoszt->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
                        $ujPoszt->stat_egyeb_sum = $posztTipusokSum['egyeb'];
                        $ujPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);
                        $ujPoszt->save();
                    } else if((int)$arr[2]==0 && (int)$arr[3]==0) {
                        $ujPoszt->ev = $posztDatum->format('Y');
                        $ujPoszt->honap = $posztDatum->format('m');
                        $ujPoszt->nap = $posztDatum->format('d');
                        $ujPoszt->kovetok_szama = (int)$arr[1];
                        $ujPoszt->posztok = json_encode($posztok);
                        $ujPoszt->save();

                        $posztTipusokSum = PostStat::getSumPosztTipusok($posztok);
                        $ujPoszt->stat_poszt_sum = PostStat::getSumPoszt($posztok);
                        $ujPoszt->stat_reakciok_sum = PostStat::getSumReakciok($posztok);
                        $ujPoszt->stat_sajat_sum = $posztTipusokSum['sajat'];
                        $ujPoszt->stat_szemelyes_sum = $posztTipusokSum['szemelyes'];
                        $ujPoszt->stat_polgarmesteri_sum = $posztTipusokSum['polgarmesteri'];
                        $ujPoszt->stat_alpolgarmesteri_sum = $posztTipusokSum['alpolgarmesteri'];
                        $ujPoszt->stat_csoportoldal_sum = $posztTipusokSum['csoportoldal'];
                        $ujPoszt->stat_media_sum = $posztTipusokSum['media'];
                        $ujPoszt->stat_kepviselotars_sum = $posztTipusokSum['kepviselotars'];
                        $ujPoszt->stat_egyeb_sum = $posztTipusokSum['egyeb'];
                        $ujPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);
                        $ujPoszt->save();
                    }

                    //echo json_encode($arr) . ", ";
                }
                //echo "<br>";
            }
            //secho "<br>";
        }

        exit;
        //Move Uploaded File
        /*$destinationPath = 'uploads';
        $file->move($destinationPath,$file->getClientOriginalName());*/
    }
}
