<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SasaranTwRealisasi extends Model
{
    protected $table = 'sasaran_tw_realisasis';
    protected $guarded = 'id';

    public function sasaran_target_satuan_rp_realisasi()
    {
        return $this->belongsTo('App\Models\SasaranTargetSatuanRpRealisasi', 'sasaran_target_satuan_rp_realisasi_id');
    }
}
