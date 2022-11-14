<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanIndikatorKinerja extends Model
{
    protected $table = 'tujuan_indikator_kinerjas';
    protected $guarded = 'id';

    public function tujuan()
    {
        return $this->belongsTo('App\Models\Tujuan', 'tujuan_id');
    }

    public function tujuan_target_satuan_rp_realisasi()
    {
        return $this->hasMany('App\Models\TujuanTargetSatuanRpRealisasi', 'tujuan_indikator_kinerja_id');
    }
}
