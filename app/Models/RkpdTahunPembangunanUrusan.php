<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RkpdTahunPembangunanUrusan extends Model
{
    protected $table = 'rkpd_tahun_pembangunan_urusans';
    protected $guarded = 'id';

    public function rkpd_opd_tahun_pembangunan()
    {
        return $this->belongsTo('App\Models\RkpdOpdTahunPembangunan', 'rkpd_opd_tahun_pembangunan_id');
    }

    public function urusan()
    {
        return $this->belongsTo('App\Models\Urusan', 'urusan_id');
    }

    public function rkpd_tahun_pembangunan_program()
    {
        return $this->hasMany('App\Models\RkpdTahunPembangunanProgam', 'rkpd_tahun_pembangunan_urusan_id');
    }
}
