<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $table = 'kelurahans';
    protected $guarded = 'id';

    public function kecamatan()
    {
        return $this->belongsTo('App\Models\Kecamatan', 'kecamatan_id');
    }

    public function opd()
    {
        return $this->hasMany('App\Models\Opd', 'kelurahan_id');
    }
}
