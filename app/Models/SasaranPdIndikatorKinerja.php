<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SasaranPdIndikatorKinerja extends Model
{
    protected $table = 'sasaran_pd_indikator_kinerjas';
    protected $guarded = 'id';

    public function sasaran_pd()
    {
        return $this->belongsTo('App\Models\SasaranPd', 'sasaran_pd_id');
    }

    public function sasaran_pd_target_satuan_rp_realisasi()
    {
        return $this->hasMany('App\Models\SasaranPdTargetSatuanRpRealisasi', 'sasaran_pd_indikator_kinerja_id');
    }
}
