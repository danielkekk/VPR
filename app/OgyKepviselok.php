<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OgyKepviselok extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ogykepviselok';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    public $incrementing = true;
}
