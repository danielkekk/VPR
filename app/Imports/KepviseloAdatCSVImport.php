<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class KepviseloAdatCSVImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

        return $collection;

        /*foreach ($collection as $row)
        {
            User::create([
                'name' => $row[0],
            ]);
        }*/
    }
}
