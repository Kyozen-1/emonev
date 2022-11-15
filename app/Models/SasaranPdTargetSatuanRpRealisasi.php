<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SasaranPdTargetSatuanRpRealisasi extends Model
{
    protected $table = 'sasaran_pd_target_satuan_rp_realisasis';
    protected $guarded = 'id';

    public function sasaran_pd_indikator_kinerja()
    {
        return $this->belongsTo('App\Models\SasaranPdIndikatorKinerja', 'sasaran_pd_indikator_kinerja_id');
    }
}
