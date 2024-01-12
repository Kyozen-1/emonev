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

    public function faktor_tindak_lanjut_e81()
    {
        return $this->hasMany('App\Models\FaktorTindakLanjutE81', 'tahun_periode_id');
    }
}
