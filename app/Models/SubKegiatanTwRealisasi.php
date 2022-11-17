<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKegiatanTwRealisasi extends Model
{
    protected $table = 'sub_kegiatan_tw_realisasis';
    protected $guarded = 'id';

    public function sub_kegiatan_target_satuan_rp_realisasi()
    {
        return $this->belongsTo('App\Models\SubKegiatanTargetSatuanRpRealisasi', 'sub_kegiatan_target_satuan_rp_realisasi_id');
    }

    public function tw()
    {
        return $this->belongsTo('App\Models\MasterTw', 'tw_id');
    }
}
