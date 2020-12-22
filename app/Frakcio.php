<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Frakcio extends Model
{
    protected $table = 'frakciok';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
}
