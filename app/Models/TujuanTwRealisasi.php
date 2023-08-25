<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanTwRealisasi extends Model
{
    protected $table = 'tujuan_tw_realisasis';
    protected $guarded = 'id';

    public function tujuan_target_satuan_rp_realisasi()
    {
        return $this->belongsTo('App\Models\TujuanTargetSatuanRpRealisasi', 'tujuan_target_satuan_rp_realisasi_id');
    }
}
