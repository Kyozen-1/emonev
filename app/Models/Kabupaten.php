<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 'kabupatens';
    protected $guarded = 'id';

    public function provinsi()
    {
        return $this->belongsTo('App\Models\Provinsi', 'provinsi_id');
    }

    public function kecamatan()
    {
        return $this->hasMany('App\Models\Kecamatan', 'kabupaten_id');
    }

    public function opd()
    {
        return $this->hasMany('App\Models\Opd', 'kabupaten_id');
    }

    public function user()
    {
        return $this->hasMany('App\User', 'kabupaten_id');
    }

    public function rkpd_tahun_pembangunan()
    {
        return $this->hasMany('App\Models\RkpdTahunPembangunan', 'kabupaten_id');
    }
}
