<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotSubKegiatanIndikator extends Model
{
    protected $table = 'pivot_sub_kegiatan_indikators';
    protected $guarded = 'id';

    public function sub_kegiatan()
    {
        return $this->belongsTo('App\Models\SubKegiatan', 'sub_kegiatan_id');
    }
}
