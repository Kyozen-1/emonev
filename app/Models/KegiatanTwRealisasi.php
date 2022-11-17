<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanTwRealisasi extends Model
{
    protected $table = 'kegiatan_tw_realisasis';
    protected $guarded = 'id';

    public function tw()
    {
        return $this->belongsTo('App\Models\MasterTw', 'tw_id');
    }

    public function kegiatan_target_satuan_rp_realisasi()
    {
        return $this->belongsTo('App\Models\KegiatanTargetSatuanRpRealisasi', 'kegiatan_target_satuan_rp_realisasi_id');
    }
}
