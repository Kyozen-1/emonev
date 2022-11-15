<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanPdIndikatorKinerja extends Model
{
    protected $table = 'tujuan_pd_indikator_kinerjas';
    protected $guarded = 'id';

    public function tujuan_pd()
    {
        return $this->belongsTo('App\Models\TujuanPd', 'tujuan_pd_id');
    }

    public function tujuan_pd_target_satuan_rp_realisasi()
    {
        return $this->hasMany('App\Models\TujuanPdTargetSatuanRpRealisasi', 'tujuan_pd_indikator_kinerja_id');
    }
}
