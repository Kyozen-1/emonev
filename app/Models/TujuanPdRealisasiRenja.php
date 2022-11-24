<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanPdRealisasiRenja extends Model
{
    protected $table = 'tujuan_pd_realisasi_renjas';
    protected $guarded = 'id';

    public function tujuan_pd_target_satuan_rp_realisasi()
    {
        return $this->belongsTo('App\Models\TujuanPdTargetSatuanRpRealisasi', 'tujuan_pd_target_satuan_rp_realisasi_id');
    }
}
