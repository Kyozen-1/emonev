<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanPd extends Model
{
    protected $table = 'tujuan_pds';
    protected $guarded = 'id';

    public function tujuan()
    {
        return $this->belongsTo('App\Models\Tujuan', 'tujuan_id');
    }
    public function opd()
    {
        return $this->belongsTo('App\Models\MasterOpd', 'opd_id');
    }

    public function pivot_perubahan_tujuan_pd()
    {
        return $this->hasMany('App\Models\PivotPerubahanTujuanPd', 'tujuan_pd_id');
    }

    public function tujuan_pd_indikator_kinerja()
    {
        return $this->hasMany('App\Models\TujuanPdIndikatorKinerja', 'tujuan_pd_id');
    }
}
