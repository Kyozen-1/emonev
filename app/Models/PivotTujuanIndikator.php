<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotTujuanIndikator extends Model
{
    protected $table = 'pivot_tujuan_indikators';
    protected $guarded = 'id';

    public function tujuan()
    {
        return $this->belongsTo('App\Models\Tujuan', 'tujuan_id');
    }

    public function target_rp_pertahun_tujuan()
    {
        return $this->hasMany('App\Models\TargetRpPertahunTujuan', 'pivot_tujuan_indikator_id');
    }
}
