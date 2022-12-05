<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RkpdTahunPembangunanKegiatan extends Model
{
    protected $table = 'rkpd_tahun_pembangunan_kegiatans';
    protected $guarded = 'id';

    public function rkpd_tahun_pembangunan_program()
    {
        return $this->belongsTo('App\Models\RkpdTahunPembangunanProgram', 'rkpd_tahun_pembangunan_program_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo('App\Models\Kegiatan', 'kegiatan_id');
    }

    public function rkpd_tahun_pembangunan_sub_kegiatan()
    {
        return $this->hasMany('App\Models\RkpdTahunPembangunanSubKegiatan', 'rkpd_tahun_pembangunan_kegiatan_id');
    }
}
