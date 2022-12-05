<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RkpdTahunPembangunanSubKegiatan extends Model
{
    protected $table = 'rkpd_tahun_pembangunan_sub_kegiatans';
    protected $guarded = 'id';

    public function rkpd_tahun_pembangunan_kegiatan()
    {
        return $this->belongsTo('App\Models\RkpdTahunPembangunanKegiatan', 'rkpd_tahun_pembangunan_kegiatan_id');
    }

    public function sub_kegiatan()
    {
        return $this->belongsTo('App\Models\SubKegiatan', 'sub_kegiatan_id');
    }
}
