<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatans';
    protected $guarded = 'id';

    public function program()
    {
        return $this->belongsTo('App\Models\Program', 'program_id');
    }

    public function pivot_perubahan_kegiatan()
    {
        return $this->hasMany('App\Models\PivotPerubahanKegiatan', 'program_id');
    }

    public function sub_kegiatan()
    {
        return $this->hasMany('App\Models\SubKegiatan', 'sub_kegiatan_id');
    }

    public function kegiatan_indikator_kinerja()
    {
        return $this->hasMany('App\Models\KegiatanIndikatorKinerja', 'kegiatan_id');
    }

    public function rkpd_tahun_pembangunan_kegiatan()
    {
        return $this->hasMany('App\Models\RkpdTahunPembangunanKegiatan', 'kegiatan_id');
    }
}
