<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotPerubahanSubKegiatan extends Model
{
    protected $table = 'pivot_perubahan_sub_kegiatans';
    protected $guarded = 'id';

    public function sub_kegiatan()
    {
        return $this->belongsTo('App\Models\SubKegiatan', 'sub_kegiatan_id');
    }
}
