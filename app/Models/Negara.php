<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negara extends Model
{
    protected $table = 'negaras';
    protected $guarded = 'id';

    public function provinsi()
    {
        return $this->hasMany('App\Models\Provinsi', 'negara_id');
    }

    public function opd()
    {
        return $this->hasMany('App\Models\Opd', 'negara_id');
    }
}
