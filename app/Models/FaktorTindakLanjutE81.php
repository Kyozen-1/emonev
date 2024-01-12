<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaktorTindakLanjutE81 extends Model
{
    public function opd()
    {
        return $this->belongsTo('App\Models\MasterOpd', 'opd_id');
    }

    public function tahun_periode()
    {
        return $this->belongsTo('App\Models\TahunPeriode', 'tahun_periode_id');
    }
}
