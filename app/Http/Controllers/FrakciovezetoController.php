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

        $evek = DB::table('kepviselo_poszt')
            ->select('ev')
            ->distinct()
            ->get();

        $honapok = [
            1 => 'Január',
            2 => 'Február',
            3 => 'Március',
            4 => 'Április',
            5 => 'Május',
            6 => 'Június',
            7 => 'Július',
            8 => 'Augusztus',
            9 => 'Szeptember',
            10 => 'Október',
            11 => 'November',
            12 => 'December',
        ];

        return view('frakciovezeto.statisztika', [
            'kepviselok' => $kepviselok,
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
