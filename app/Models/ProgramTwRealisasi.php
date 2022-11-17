<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramTwRealisasi extends Model
{
    protected $table = 'program_tw_realisasis';
    protected $guarded = 'id';

    public function tw()
    {
        return $this->belongsTo('App\Models\MasterTw', 'tw_id');
    }

    public function program_target_satuan_rp_realisasi()
    {
        return $this->belongsTo('App\Models\ProgramTargetSatuanRpRealisasi', 'program_target_satuan_rp_realisasi_id');
    }
}
