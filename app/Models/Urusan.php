<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Urusan extends Model
{
    protected $table = 'urusans';
    protected $guarded = 'id';

    public function pivot_perubahan_urusan()
    {
        return $this->hasMany('App\Models\Urusan', 'urusan_id');
    }

    public function program()
    {
        return $this->hasMany('App\Models\Program', 'urusan_id');
    }

    public function rkpd_tahun_pembangunan_urusan()
    {
        return $this->hasMany('App\Models\RkpdTahunPembangunanUrusan', 'urusan_id');
    }
}
