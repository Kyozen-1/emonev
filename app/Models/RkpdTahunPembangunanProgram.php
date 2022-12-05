<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RkpdTahunPembangunanProgram extends Model
{
    protected $table = 'rkpd_tahun_pembangunan_programs';
    protected $guarded = 'id';

    public function rkpd_tahun_pembangunan_urusan()
    {
        return $this->belongsTo('App\Models\RkpdTahunPembangunanUrusan', 'rkpd_tahun_pembangunan_urusan_id');
    }

    public function program()
    {
        return $this->belongsTo('App\Models\Program', 'program_id');
    }

    public function rkpd_tahun_pembangunan_kegiatan()
    {
        return $this->hasMany('App\Models\RkpdTahunPembangunanKegiatan', 'rkpd_tahun_pembangunan_program_id');
    }
}
