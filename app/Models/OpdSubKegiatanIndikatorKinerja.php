<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpdSubKegiatanIndikatorKinerja extends Model
{
    protected $table = 'opd_sub_kegiatan_indikator_kinerjas';
    protected $guarded = 'id';

    public function sub_kegiatan_indikator_kinerja()
    {
        return $this->belongsTo('App\Models\SubKegiatanIndikatorKinerja', 'sub_kegiatan_indikator_kinerja_id');
    }

    public function opd()
    {
        return $this->belongsTo('App\Models\MasterOpd', 'opd_id');
    }

    public function sub_kegiatan_target_satuan_rp_realisasi()
    {
        return $this->hasMany('App\Models\SubKegiatanTargetSatuanRpRealisasi', 'opd_sub_kegiatan_indikator_kinerja_id');
    }
}
