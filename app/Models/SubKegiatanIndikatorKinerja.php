<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKegiatanIndikatorKinerja extends Model
{
    protected $table = 'sub_kegiatan_indikator_kinerjas';
    protected $guarded = 'id';

    public function sub_kegiatan()
    {
        return $this->belongsTo('App\Models\SubKegiatan', 'sub_kegiatan_id');
    }

    public function opd_sub_kegiatan_indikator_kinerja()
    {
        return $this->hasMany('App\Models\OpdSubKegiatanIndikatorKinerja', 'sub_kegiatan_indikator_kinerja_id');
    }
}
