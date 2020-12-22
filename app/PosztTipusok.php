<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PosztTipusok extends Model
{
    protected $table = 'poszt_tipusok';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
}
