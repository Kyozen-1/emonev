<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpdKegiatanIndikatorKinerja extends Model
{
    protected $table = 'opd_kegiatan_indikator_kinerjas';
    protected $guarded = 'id';

    public function kegiatan_indikator_kinerja()
    {
        return $this->belongsTo('App\Models\KegiatanIndikatorKinerja', 'kegiatan_indikator_kinerja_id');
    }

    public function opd()
    {
        return $this->belongsTo('App\Models\MasterOpd', 'opd_id');
    }
}
