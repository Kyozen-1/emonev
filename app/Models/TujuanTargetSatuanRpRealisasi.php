<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanTargetSatuanRpRealisasi extends Model
{
    protected $table = 'tujuan_target_satuan_rp_realisasis';
    protected $guarded = 'id';

    public function tujuan_indikator_kinerja()
    {
        return $this->belongsTo('App\Models\TujuanIndikatorKinerja', 'tujuan_indikator_kinerja_id');
    }

    public function tujuan_tw_realisasi()
    {
        return $this->hasMany('App\Models\TujuanTwRealisasi', 'tujuan_target_satuan_rp_realisasi_id');
    }
}
