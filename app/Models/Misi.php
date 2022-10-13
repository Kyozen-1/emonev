<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Misi extends Model
{
    protected $table = 'misis';
    protected $guarded = 'id';

    public function visi()
    {
        return $this->belongsTo('App\Models\Visi', 'visi_id');
    }

    public function pivot_perubahan_misi()
    {
        return $this->hasMany('App\Models\PivotPerubahanMisi', 'misi_id');
    }

    public function tujuan()
    {
        return $this->hasMany('App\Models\Tujuan', 'misi_id');
    }
}
