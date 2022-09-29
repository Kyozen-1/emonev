<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetRpPertahunTujuan extends Model
{
    protected $table = 'target_rp_pertahun_tujuans';
    protected $guarded = 'id';

    public function pivot_tujuan_indikator()
    {
        return $this->belongsTo('App\Moels\PivotTujuanIndikator', 'pivot_tujuan_indikator_id');
    }
}
