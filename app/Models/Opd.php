<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opd extends Model
{
    protected $table = 'opds';
    protected $guarded = 'id';

    public function akun_opd()
    {
        return $this->hasOne('App\AkunOpd', 'opd_id');
    }

    public function negara()
    {
        return $this->belongsTo('App\Models\Negara', 'negara_id');
    }

    public function provinsi()
    {
        return $this->belongsTo('App\Models\Provinsi', 'provinsi_id');
    }

    public function kabupaten()
    {
        return $this->belongsTo('App\Models\Kabupaten', 'kabupaten_id');
    }

    public function kecamatan()
    {
        return $this->belongsTo('App\Models\Kecamatan', 'kecamatan_id');
    }

    public function kelurahan()
    {
        return $this->belongsTo('App\Models\Kelurahan', 'kelurahan_id');
    }

    public function master_opd()
    {
        return $this->belongsTo('App\Models\MasterOpd', 'opd_id');
    }

    public function renstra()
    {
        return $this->hasMany('App\Models\Renstra', 'opd_id');
    }
}
