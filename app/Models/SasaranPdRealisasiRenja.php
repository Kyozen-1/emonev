<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SasaranPdRealisasiRenja extends Model
{
    protected $table = 'sasaran_pd_realisasi_renjas';
    protected $guarded = 'id';

    public function sasaran_pd_target_satuan_rp_realisasi()
    {
        return $this->belongsTo('App\Models\SasaranPdTargetSatuanRpRealisasi', 'sasaran_pd_target_satuan_rp_realisasi_id');
    }
}
