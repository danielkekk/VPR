<?php
namespace App\Business\Statistics;

class PostStat
{
    /*
     * return HM%
     */
    public static function getHM($reakciokSzama=0, $kovetokSzama=0)
    {
        if(empty($kovetokSzama)) {
            return 0;
        }

        $hm = ($reakciokSzama / $kovetokSzama) * 100;

        return round($hm,2);
    }

    /*
     * return atlag HM%
     */
    public static function getAtlagHM(array $posztok)
    {
        if(empty($posztok)) {
            return 0;
        }

        $hmDb = 0;
        $hmSum = 0;
        foreach($posztok as $poszt) {
            $hmSum += $poszt['HM'];
            $hmDb++;
        }

        return round(($hmSum / $hmDb),2);
    }

    /*
     * összes napi poszt száma
     */
    public static function getSumPoszt(array $posztok)
    {
        return count($posztok);
    }

    /*
     * összes napi reakciók
     */
    public static function getSumReakciok(array $posztok)
    {
        if(empty($posztok)) {
            return 0;
        }

        $sum = 0;
        foreach($posztok as $poszt) {
            $sum += $poszt['reakcio'];
        }

        return $sum;
    }

    /*
     * összes poszttipus darabszama
     */
    public static function getSumPosztTipusok(array $posztok)
    {
        if(empty($posztok)) {
            return [
                'sajat' => 0,
                'szemelyes' => 0,
                'polgarmesteri' => 0,
                'alpolgarmesteri' => 0,
                'csoportoldal' => 0,
                'media' => 0,
                'kepviselotars' => 0,
                'egyeb' => 0,
            ];
        }

        $sumSajat = 0;
        $sumSzemelyes = 0;
        $sumPolgarmesteri = 0;
        $sumAlpolgarmesteri = 0;
        $sumCsoportoldal = 0;
        $sumMedia = 0;
        $sumKepviselotars = 0;
        $sumEgyeb = 0;
        foreach($posztok as $poszt) {
            if($poszt['tipus'] === 'sajat') {
                $sumSajat++;
            } else if($poszt['tipus'] === 'szemelyes') {
                $sumSzemelyes++;
            } else if($poszt['tipus'] === 'alpolgarmesteri') {
                $sumAlpolgarmesteri++;
            } else if($poszt['tipus'] === 'polgarmesteri') {
                $sumPolgarmesteri++;
            } else if($poszt['tipus'] === 'csoportoldal') {
                $sumCsoportoldal++;
            } else if($poszt['tipus'] === 'media') {
                $sumMedia++;
            } else if($poszt['tipus'] === 'kepviselotars') {
                $sumKepviselotars++;
            } else if($poszt['tipus'] === 'egyeb') {
                $sumEgyeb++;
            }
        }

        return [
            'sajat' => $sumSajat,
            'szemelyes' => $sumSzemelyes,
            'alpolgarmesteri' => $sumAlpolgarmesteri,
            'polgarmesteri' => $sumPolgarmesteri,
            'csoportoldal' => $sumCsoportoldal,
            'media' => $sumMedia,
            'kepviselotars' => $sumKepviselotars,
            'egyeb' => $sumEgyeb,
        ];
    }
}