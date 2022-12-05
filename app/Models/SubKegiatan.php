<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKegiatan extends Model
{
    protected $table = 'sub_kegiatans';
    protected $guarded = 'id';

    public function kegiatan()
    {
        return $this->belongsTo('App\Models\Kegiatan', 'kegiatan_id');
    }

    public function pivot_perubahan_sub_kegiatan()
    {
        return $this->hasMany('App\Models\PivotPerubahanSubKegiatan', 'sub_kegiatan_id');
    }

    public function sub_kegiatan_indikator_kinerja()
    {
        return $this->hasMany('App\Models\SubKegiatanIndikatorKinerja', 'sub_kegiatan_id');
    }

    public function rkpd_tahun_pembangunan_sub_kegiatan()
    {
        return $this->hasMany('App\Models\RkpdTahunPembangunanSubKegiatan', 'sub_kegiatan_id');
    }
}
