<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanIndikatorKinerja extends Model
{
    protected $table = 'kegiatan_indikator_kinerjas';
    protected $guarded = 'id';

    public function kegiatan()
    {
        return $this->belongsTo('App\Models\Kegiatan', 'kegiatan_id');
    }
}
