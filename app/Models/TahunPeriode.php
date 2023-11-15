<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunPeriode extends Model
{
    protected $table = 'tahun_periodes';
    protected $guarded = 'id';

    public function urusan()
    {
        return $this->hasMany('App\Models\Urusan', 'tahun_periode_id');
    }
}
