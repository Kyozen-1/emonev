<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKegiatanTargetSatuanRpRealisasi extends Model
{
    protected $table = 'sub_kegiatan_target_satuan_rp_realisasis';
    protected $guarded = 'id';

    public function opd_sub_kegiatan_indikator_kinerja()
    {
        return $this->belongsTo('App\Models\OpdSubKegiatanIndikatorKinerja', 'opd_sub_kegiatan_indikator_kinerja_id');
    }

    public function sub_kegiatan_tw_realisasi()
    {
        return $this->hasMany('App\Models\SubKegiatanTwRealisasi', 'sub_kegiatan_target_satuan_rp_realisasi_id');
    }
}
