<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    const ORSZAGOS = 1;
    const HELYI = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    public $incrementing = true;
}
