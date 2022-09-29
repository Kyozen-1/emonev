<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'kecamatans';
    protected $guarded = 'id';

    public function kabupaten()
    {
        return $this->belongsTo('App\Models\Kabupaten', 'kabupaten_id');
    }

    public function kelurahan()
    {
        return $this->hasMany('App\Models\Kelurahan', 'kelurahan_id');
    }

    public function opd()
    {
        return $this->hasMany('App\Models\Opd', 'kecamatan_id');
    }
}
