<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RkpdTahunPembangunan extends Model
{
    protected $table = 'rkpd_tahun_pembangunans';
    protected $guarded = 'id';

    public function kabupaten()
    {
        return $this->belongsTo('App\Models\Kabupaten', 'kabupaten_id');
    }

    public function rkpd_opd_tahun_pembangunan()
    {
        return $this->hasMany('App\Models\RkpdOpdTahunPembangunan', 'rkpd_tahun_pembangunan_id');
    }
}
