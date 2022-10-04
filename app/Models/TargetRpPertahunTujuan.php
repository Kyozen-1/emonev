<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetRpPertahunTujuan extends Model
{
    protected $table = 'target_rp_pertahun_tujuans';
    protected $guarded = 'id';

    public function renstra()
    {
        return $this->belongsTo('App\Models\Renstra', 'renstra_id');
    }

    public function tujuan()
    {
        return $this->belongsTo('App\Models\Tujuan', 'tujuan_id');
    }

    public function pivot_tujuan_indikator()
    {
        return $this->belongsTo('App\Models\PivotTujuanIndikator', 'pivot_tujuan_indikator_id');
    }
}
