<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanPdTargetSatuanRpRealisasi extends Model
{
    protected $table = 'tujuan_pd_target_satuan_rp_realisasis';
    protected $guarded = 'id';

    public function tujuan_pd_indikator_kinerja()
    {
        return $this->belongsTo('App\Models\TujuanPdIndikatorKinerja', 'tujuan_pd_indikator_kinerja_id');
    }

    public function tujuan_pd_realisasi_renja()
    {
        return $this->hasOne('App\Models\TujuanPdRealisasiRenja', 'tujuan_pd_target_satuan_rp_realisasi_id');
    }
}
