<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PivotPerubahanKegiatan extends Model
{
    protected $table = 'pivot_perubahan_kegiatans';
    protected $guarded = 'id';

    public function kegiatan()
    {
        return $this->belongsTo('App\Models\Kegiatan', 'kegiatan_id');
    }
}
