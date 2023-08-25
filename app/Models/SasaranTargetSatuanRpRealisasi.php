<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SasaranTargetSatuanRpRealisasi extends Model
{
    protected $table = 'sasaran_target_satuan_rp_realisasis';
    protected $guarded = 'id';

    public function sasaran_indikator_kinerja()
    {
        return $this->belongsTo('App\Models\SasaranIndikatorKinerja', 'sasaran_indikator_kinerja_id');
    }

    public function sasaran_tw_realisasi()
    {
        return $this->hasMany('App\Models\SasaranTwRealisasi', 'sasaran_tw_realisasi_renja_id');
    }
}
