<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanTargetSatuanRpRealisasi extends Model
{
    protected $table = 'kegiatan_target_satuan_rp_realisasis';
    protected $guarded = 'id';

    public function opd_kegiatan_indikator_kinerja()
    {
        return $this->belongsTo('App\Models\OpdKegiatanIndikatorKinerja', 'opd_kegiatan_indikator_kinerja_id');
    }
}
