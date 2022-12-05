<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RkpdOpdTahunPembangunan extends Model
{
    protected $table = 'rkpd_opd_tahun_pembangunans';
    protected $guarded = 'id';

    public function rkpd_tahun_pembangunan()
    {
        return $this->belongsTo('App\Models\RkpdTahunPembangunan', 'rkpd_tahun_pembangunan_id');
    }

    public function opd()
    {
        return $this->belongsTo('App\Models\MasterOpd','opd_id');
    }

    public function rkpd_tahun_pembangunan_urusan()
    {
        return $this->hasMany('App\Models\RkpdTahunPembangunanUrusan', 'rkpd_opd_tahun_pembangunans');
    }
}
