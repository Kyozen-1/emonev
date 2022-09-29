<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotKegiatanIndikator extends Model
{
    protected $table = 'pivot_kegiatan_indikators';
    protected $guarded = 'id';

    public function kegiatan()
    {
        return $this->belongsTo('App\Models\Kegiatan', 'kegiatan_id');
    }
}
