<?php

namespace App\Http\Controllers;

use App\Media;
use App\OgyKepviselok;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrakciovezetoController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('frakciovezeto');
    }

    public function getOgyKepviselokStatisztika()
    {
        $kepviselok = OgyKepviselok::all();

        return view('frakciovezeto.ogykepviselok_statisztika', [
            'kepviselok' => $kepviselok,
        ]);
    }

    public function getOrszMediakStatisztika()
    {
        $orszmediak = Media::where('tipus', '=', Media::ORSZAGOS)->get();

        return view('frakciovezeto.orszmedia_statisztika', [
            'orszmediak' => $orszmediak,
        ]);
    }

    public function getLocalMediakStatisztika()
    {
        //TODO lecsekkolni a frakciójához tartozó helyi médiákat ???
        $localmediak = Media::where('tipus', '=', Media::HELYI)->get();

        return view('frakciovezeto.localmedia_statisztika', [
            'localmediak' => $localmediak,
        ]);
    }

    public function getKepviselokStatisztika()
    {
        $authenticatedUser = Auth::user();
        if(!$authenticatedUser || !isset($authenticatedUser->frakcio_id)) {
            //TODO hiba
        }

        $kepviselok = User::where('frakcio_id', '=', trim($authenticatedUser->frakcio_id))->
        whereIn('role', array(4))->get();

        //TODO csak a frakcióba tartozó képviselőkhöz tartozó évek
        $evek = DB::table('kepviselo_poszt')
            ->select('ev')
            ->distinct()
            ->get();

        $honapok = [
            '01' => 'Január',
            '02' => 'Február',
            '03' => 'Március',
            '04' => 'Április',
            '05' => 'Május',
            '06' => 'Június',
            '07' => 'Július',
            '08' => 'Augusztus',
            '09' => 'Szeptember',
            '10' => 'Október',
            '11' => 'November',
            '12' => 'December',
        ];

        return view('frakciovezeto.statisztika', [
            'kepviselok' => $kepviselok,
            'evek' => $evek,
            'honapok' => $honapok,
        ]);
    }

    public function getHaviStatisztika()
    {
        $authenticatedUser = Auth::user();
        if(!$authenticatedUser || !isset($authenticatedUser->frakcio_id)) {
            //TODO hiba
        }

        $evek = DB::table('kepviselo_poszt')
            ->select('ev')
            ->distinct()
            ->get();

        $honapok = [
            '01' => 'Január',
            '02' => 'Február',
            '03' => 'Március',
            '04' => 'Április',
            '05' => 'Május',
            '06' => 'Június',
            '07' => 'Július',
            '08' => 'Augusztus',
            '09' => 'Szeptember',
            '10' => 'Október',
            '11' => 'November',
            '12' => 'December',
        ];

        return view('frakciovezeto.statisztika_havi', [
            'evek' => $evek,
            'honapok' => $honapok,
        ]);
    }

    public function getFrakciovezetoStatisztika()
    {
        $authenticatedUser = Auth::user();
        if(!$authenticatedUser || !isset($authenticatedUser->frakcio_id)) {
            //TODO hiba
        }

        return view('frakciovezeto.frakciovezeto_statisztika', [
            'frakciovezetoId' => $authenticatedUser->id,
        ]);
    }
}
