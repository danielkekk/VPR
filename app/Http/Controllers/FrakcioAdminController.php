<?php

namespace App\Http\Controllers;

use App\Business\Statistics\PostStat;
use App\Frakcio;
use App\KepviseloPoszt;
use App\LocalMediaPoszt;
use App\Media;
use App\PosztTipusok;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;


class FrakcioAdminController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('frakcioadmin');
    }

    public function ujKepviselo()
    {
        return view('frakcioadmin.fra-ujkepviselo', []);
    }

    public function createKepviselo(Request $request)
    {
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'max' => 'Maximum 100 karakter.',
            'min' => 'A jelszó legalább 8 karakter hosszúságú kell legyen.',
            'digits' => 'A(z) :attribute mező értéke nem megfelelő.',
            'confirmed' => 'A két jelszó nem egyezik meg',
        ];

        $validator = Validator::make($request->all(), [
            'kepviselonev' => 'required|string|max:100',
            'kepviseloemail' => 'required|email',
            'password' => 'required|string|confirmed',
            'kepviselorole' => 'required|digits:1',
        ], $messages);

        if ($validator->fails()) {
            return redirect('fra-ujkepviselo')
                ->withErrors($validator)
                ->withInput();
        }

        $authenticatedUser = Auth::user();
        if(!$authenticatedUser || !isset($authenticatedUser->frakcio_id)) {
            $messageBag = new MessageBag();
            $messageBag->add('menteshiba', 'Mentés sikertelen. Nem sikerült meghatározni a frakciót.');
            return redirect('fra-ujkepviselo')
                ->withErrors($messageBag)
                ->withInput();
        }

        $kepviselo = new User();
        $kepviselo->name = $request->kepviselonev;
        $kepviselo->email = $request->kepviseloemail;
        $kepviselo->password = Hash::make($request->password);
        $kepviselo->frakcio_id = $authenticatedUser->frakcio_id;
        $kepviselo->role = $request->kepviselorole;
        $kepviselo->status_id = 1;
        $kepviselo->save();

        return redirect()->route('fra-ujkepviselo');
    }

    public function ujKepviseloPoszt()
    {
        $authenticatedUser = Auth::user();
        $kepviselok = User::where('frakcio_id','=',trim($authenticatedUser->frakcio_id))->get();

        return view('frakcioadmin.fra-ujkepviseloposzt', [
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
            return redirect('fra-ujkepviseloposzt')
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
            return redirect('fra-ujkepviseloposzt')
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
        $kepviseloPoszt->stat_altalanos_sum = $posztTipusokSum['altalanos'];
        $kepviseloPoszt->stat_alpolg_sum = $posztTipusokSum['alpolgarmesteri'];
        $kepviseloPoszt->stat_polg_sum = $posztTipusokSum['polgarmesteri'];
        $kepviseloPoszt->stat_privat_sum = $posztTipusokSum['szemelyes'];
        $kepviseloPoszt->stat_ogykepviselo_sum = $posztTipusokSum['ogykepviselo'];
        $kepviseloPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);
        $kepviseloPoszt->save();

        return redirect()->route('fra-ujkepviseloposzt');
    }

    public function getKepviseloPosztok() {
        $authenticatedUser = Auth::user();
        $sql = 'SELECT us.name,kp.id,kp.users_id,kp.kovetok_szama,kp.ev,kp.honap,kp.nap 
                FROM kepviselo_poszt AS kp 
                LEFT JOIN users AS us ON kp.users_id=us.id
                WHERE us.frakcio_id=:frid;';
        $kepviseloposztok = DB::select($sql, ['frid' => $authenticatedUser->frakcio_id]);

        return view('frakcioadmin.fra-kepviseloposztok', [
            'kepviseloposztok' => $kepviseloposztok,
        ]);
    }

    //a frakcioid ugyanaz-e mint a poszthoz tartozó képviselő frakció idja?
    private function isSameFrakcioId($kpid) {
        $authenticatedUser = Auth::user();
        $sql = 'SELECT us.id, us.frakcio_id FROM kepviselo_poszt AS kp INNER JOIN users AS us ON kp.users_id=us.id
                WHERE kp.id=:kpid;';
        $result = DB::select($sql, ['kpid' => $kpid]);
        if(empty($result) || (int)$result[0]->frakcio_id !== (int)$authenticatedUser->frakcio_id) {
            return false;
        }

        return true;
    }

    public function deleteKepviseloPoszt($id)
    {
        if(!$this->isSameFrakcioId($id)) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'Törlés sikertelen.');
            return redirect('fra-kepviseloposztok')
                ->withErrors($messageBag)
                ->withInput();
        }

        $deleted = KepviseloPoszt::destroy(trim($id));
        if(($deleted === 0)) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'Törlés sikertelen.');
            return redirect('fra-kepviseloposztok')
                ->withErrors($messageBag)
                ->withInput();
        }

        return redirect()->route('fra-kepviseloposztok', []);
    }

    public function editKepviseloPosztView($id)
    {
        if(!$this->isSameFrakcioId($id)) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'Törlés sikertelen.');
            return redirect('fra-kepviseloposztok')
                ->withErrors($messageBag)
                ->withInput();
        }

        $poszttipusok = PosztTipusok::all();
        $napiposztok = KepviseloPoszt::where('id','=',trim($id))->first();

        return view('frakcioadmin.fra-editkepviseloposzt', [
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
                return redirect('fra-editkepviseloposzt/' . trim($request->azon))
                    ->withErrors($validator)
                    ->withInput();
            }

            return redirect('fra-kepviseloposztok')
                ->withErrors($validator)
                ->withInput();
        }

        if(!$this->isSameFrakcioId($request->azon)) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'Módosítás sikertelen.');
            return redirect('fra-kepviseloposztok')
                ->withErrors($messageBag)
                ->withInput();
        }


        $napiKepviseloPoszt = KepviseloPoszt::where('id','=',trim($request->azon))->first();
        $posztTipus = PosztTipusok::where('id','=',trim($request->poszttipus))->first();
        if(!$napiKepviseloPoszt || !$posztTipus) {
            $messageBag = new MessageBag();
            $messageBag->add('postnotexist', 'Bejegyzés sikertelen. Nem létezik a megadott bejegyzés vagy poszttipus.');
            return redirect('fra-kepviseloposztok')
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
        $napiKepviseloPoszt->stat_altalanos_sum = $posztTipusokSum['altalanos'];
        $napiKepviseloPoszt->stat_alpolg_sum = $posztTipusokSum['alpolgarmesteri'];
        $napiKepviseloPoszt->stat_polg_sum = $posztTipusokSum['polgarmesteri'];
        $napiKepviseloPoszt->stat_privat_sum = $posztTipusokSum['szemelyes'];
        $napiKepviseloPoszt->stat_ogykepviselo_sum = $posztTipusokSum['ogykepviselo'];
        $napiKepviseloPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);

        if(!$napiKepviseloPoszt->save())
        {
            $messageBag = new MessageBag();
            $messageBag->add('postsavecrash', 'Mentés sikertelen.');
            return redirect('fra-editkepviseloposzt/' . trim($request->azon))
                ->withErrors($messageBag)
                ->withInput();
        }

        return redirect()->route('fra-editkepviseloposzt', ['id' => trim($request->azon)]);
    }

    public function deleteKepviseloPosztById($kpid,$pid)
    {
        if(!$this->isSameFrakcioId($kpid)) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'Törlés sikertelen.');
            return redirect('fra-kepviseloposztok')
                ->withErrors($messageBag)
                ->withInput();
        }

        /*törlés*/
        $kepviseloposzt = KepviseloPoszt::where('id','=',trim($kpid))->first();
        if(!$kepviseloposzt) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'A törlés sikertelen.');
            return redirect('fra-editkepviseloposzt/' . trim($kpid))
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
        $kepviseloposzt->stat_altalanos_sum = $posztTipusokSum['altalanos'];
        $kepviseloposzt->stat_alpolg_sum = $posztTipusokSum['alpolgarmesteri'];
        $kepviseloposzt->stat_polg_sum = $posztTipusokSum['polgarmesteri'];
        $kepviseloposzt->stat_privat_sum = $posztTipusokSum['szemelyes'];
        $kepviseloposzt->stat_ogykepviselo_sum = $posztTipusokSum['ogykepviselo'];
        $kepviseloposzt->stat_atlag_hm = PostStat::getAtlagHM($ujposztok);
        $kepviseloposzt->save();

        return redirect()->route('fra-editkepviseloposzt', ['id' => trim($kpid)]);
    }





    /*
     * Helyi média
    */
    public function ujLocalMedia()
    {
        return view('frakcioadmin.fra-ujlocalmedia', []);
    }

    public function createLocalMedia(Request $request)
    {
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'max' => 'Maximum 100 karakter.',
        ];

        $validator = Validator::make($request->all(), [
            'localmedianev' => 'required|string|max:100',
        ], $messages);

        if ($validator->fails()) {
            return redirect('fra-ujlocalmedia')
                ->withErrors($validator)
                ->withInput();
        }

        $localmedia = new Media();
        $localmedia->name = $request->localmedianev;
        $localmedia->tipus = 2;
        $localmedia->status_id = 1;
        $localmedia->save();

        return redirect()->route('fra-ujlocalmedia');
    }

    public function ujLocalMediaPoszt()
    {
        $localmedia = Media::where('tipus','=',Media::HELYI)->get();

        return view('frakcioadmin.fra-ujlocalmediaposzt', [
            'localmediak' => $localmedia,
        ]);
    }

    public function createLocalMediaPoszt(Request $request)
    {
        $posztDatum = new \DateTime(trim($request->poszt_datum));
        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'integer' => 'A(z) :attribute mező csak számokat tartalmazhat.',
            'date' => 'Nem dátum.',
        ];

        $validator = Validator::make($request->all(), [
            'localmedia' => 'required|integer',
            'poszt_datum' => 'required|date',
            'kovetok_szama' => 'required|integer|min:1',
        ], $messages);

        if ($validator->fails()) {
            return redirect('fra-ujlocalmediaposzt')
                ->withErrors($validator)
                ->withInput();
        }

        $letezikLocalMediaPoszt = LocalMediaPoszt::where('media_id','=',trim($request->localmedia))
            ->where('ev','=',$posztDatum->format('Y'))
            ->where('honap','=',$posztDatum->format('m'))
            ->where('nap','=',$posztDatum->format('d'))
            ->first();

        if($letezikLocalMediaPoszt) {
            $messageBag = new MessageBag();
            $messageBag->add('postexists', 'Ehhez a médiához erre a dátumra már készült bejegyzés.');
            return redirect('fra-ujlocalmediaposzt')
                ->withErrors($messageBag)
                ->withInput();
        }

        $posztok = [];

        $localMediaPoszt = new LocalMediaPoszt();
        $localMediaPoszt->media_id = $request->localmedia;
        $localMediaPoszt->ev = $posztDatum->format('Y');
        $localMediaPoszt->honap = $posztDatum->format('m');
        $localMediaPoszt->nap = $posztDatum->format('d');
        $localMediaPoszt->kovetok_szama = $request->kovetok_szama;
        $localMediaPoszt->posztok = json_encode($posztok);
        $localMediaPoszt->save();

        $posztTipusokSum = PostStat::getSumPosztTipusok($posztok);
        $localMediaPoszt->stat_poszt_sum = PostStat::getSumPoszt($posztok);
        $localMediaPoszt->stat_reakciok_sum = PostStat::getSumReakciok($posztok);
        $localMediaPoszt->stat_altalanos_sum = $posztTipusokSum['altalanos'];
        $localMediaPoszt->stat_alpolg_sum = $posztTipusokSum['alpolgarmesteri'];
        $localMediaPoszt->stat_polg_sum = $posztTipusokSum['polgarmesteri'];
        $localMediaPoszt->stat_privat_sum = $posztTipusokSum['szemelyes'];
        $localMediaPoszt->stat_ogykepviselo_sum = $posztTipusokSum['ogykepviselo'];
        $localMediaPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);
        $localMediaPoszt->save();

        return redirect()->route('fra-ujlocalmediaposzt');
    }

    public function getLocalMediaPosztok() {
        $localmediaposztok = DB::select('select m.name,kp.id,kp.media_id,kp.kovetok_szama,kp.ev,kp.honap,kp.nap from localmedia_poszt as kp 
                                     left join media as m on kp.media_id=m.id;', []);

        return view('frakcioadmin.fra-localmediaposztok', [
            'localmediaposztok' => $localmediaposztok,
        ]);
    }

    public function deleteLocalMediaPoszt($id)
    {
        $deleted = LocalMediaPoszt::destroy(trim($id));
        if(($deleted === 0)) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'Törlés sikertelen.');
            return redirect('fra-localmediaposztok')
                ->withErrors($messageBag)
                ->withInput();
        }

        return redirect()->route('fra-localmediaposztok');
    }

    public function editLocalMediaPosztView($id)
    {
        //TODO localmedianál más
        $poszttipusok = PosztTipusok::all();
        $napiposztok = LocalMediaPoszt::where('id','=',trim($id))->first();

        return view('frakcioadmin.fra-editlocalmediaposzt', [
            'localmediaposztid' => trim($id),
            'poszttipusok' => $poszttipusok,
            'napiposztok' => json_decode($napiposztok->posztok, true)
        ]);
    }

    public function editLocalMediaPoszt(Request $request)
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
                return redirect('fra-editlocalmediaposzt/' . trim($request->azon))
                    ->withErrors($validator)
                    ->withInput();
            }

            return redirect('fra-localmediaposztok')
                ->withErrors($validator)
                ->withInput();
        }


        $napiLocalMediaPoszt = LocalMediaPoszt::where('id','=',trim($request->azon))->first();
        $posztTipus = PosztTipusok::where('id','=',trim($request->poszttipus))->first();
        if(!$napiLocalMediaPoszt || !$posztTipus) {
            $messageBag = new MessageBag();
            $messageBag->add('postnotexist', 'Bejegyzés sikertelen. Nem létezik a megadott bejegyzés vagy poszttipus.');
            return redirect('fra-localmediaposztok')
                ->withErrors($messageBag)
                ->withInput();
        }


        $posztok = json_decode($napiLocalMediaPoszt->posztok,true);
        $ujposzt = [
            'id' => count($posztok),
            'reakcio' => (int)$request->reakcio,
            'tipus' => trim($posztTipus->code),
            'link' => trim($posztTipus->url),
            'HM' => PostStat::getHM((int)$request->reakcio, intval($napiLocalMediaPoszt->kovetok_szama)),
        ];
        $posztok[] = $ujposzt;

        $napiLocalMediaPoszt->posztok = json_encode($posztok);
        $posztTipusokSum = PostStat::getSumPosztTipusok($posztok);
        $napiLocalMediaPoszt->stat_poszt_sum = PostStat::getSumPoszt($posztok);
        $napiLocalMediaPoszt->stat_reakciok_sum = PostStat::getSumReakciok($posztok);
        $napiLocalMediaPoszt->stat_altalanos_sum = $posztTipusokSum['altalanos'];
        $napiLocalMediaPoszt->stat_alpolg_sum = $posztTipusokSum['alpolgarmesteri'];
        $napiLocalMediaPoszt->stat_polg_sum = $posztTipusokSum['polgarmesteri'];
        $napiLocalMediaPoszt->stat_privat_sum = $posztTipusokSum['szemelyes'];
        $napiLocalMediaPoszt->stat_ogykepviselo_sum = $posztTipusokSum['ogykepviselo'];
        $napiLocalMediaPoszt->stat_atlag_hm = PostStat::getAtlagHM($posztok);

        if(!$napiLocalMediaPoszt->save())
        {
            $messageBag = new MessageBag();
            $messageBag->add('postsavecrash', 'Mentés sikertelen.');
            return redirect('fra-editlocalmediaposzt/' . trim($request->azon))
                ->withErrors($messageBag)
                ->withInput();
        }

        return redirect()->route('fra-editlocalmediaposzt', ['id' => trim($request->azon)]);
    }

    public function deleteLocalMediaPosztById($kpid,$pid)
    {
        /*törlés*/
        $localmediaposzt = LocalMediaPoszt::where('id','=',trim($kpid))->first();
        if(!$localmediaposzt) {
            $messageBag = new MessageBag();
            $messageBag->add('deletecrash', 'A törlés sikertelen.');
            return redirect('fra-editlocalmediaposzt/' . trim($kpid))
                ->withErrors($messageBag)
                ->withInput();
        }
        $posztok = json_decode($localmediaposzt->posztok,true);
        $ujposztok = [];
        foreach($posztok as $poszt) {
            if((int)$poszt['id'] != (int)$pid) {
                $ujposztok[] = $poszt;
            }
        }
        $localmediaposzt->posztok = json_encode($ujposztok);
        $posztTipusokSum = PostStat::getSumPosztTipusok($ujposztok);
        $localmediaposzt->stat_poszt_sum = PostStat::getSumPoszt($ujposztok);
        $localmediaposzt->stat_reakciok_sum = PostStat::getSumReakciok($ujposztok);
        $localmediaposzt->stat_altalanos_sum = $posztTipusokSum['altalanos'];
        $localmediaposzt->stat_alpolg_sum = $posztTipusokSum['alpolgarmesteri'];
        $localmediaposzt->stat_polg_sum = $posztTipusokSum['polgarmesteri'];
        $localmediaposzt->stat_privat_sum = $posztTipusokSum['szemelyes'];
        $localmediaposzt->stat_ogykepviselo_sum = $posztTipusokSum['ogykepviselo'];
        $localmediaposzt->stat_atlag_hm = PostStat::getAtlagHM($ujposztok);
        $localmediaposzt->save();

        return redirect()->route('fra-editlocalmediaposzt', ['id' => trim($kpid)]);
    }
}
