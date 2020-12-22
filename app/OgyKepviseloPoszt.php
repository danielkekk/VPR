<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OgyKepviseloPoszt extends Model
{
    const CREATED_AT = 'datum';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ogykepviselo_poszt';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
}
