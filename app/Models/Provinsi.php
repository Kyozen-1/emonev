<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $table = 'provinsis';
    protected $guarded = 'id';

    public function negara()
    {
        return $this->belongsTo('App\Models\Negara', 'negara_id');
    }

    public function kabupaten()
    {
        return $this->hasMany('App\Models\Kabupaten', 'kabupaten_id');
    }

    public function opd()
    {
        return $this->hasMany('App\Models\Opd', 'provinsi_id');
    }
}
