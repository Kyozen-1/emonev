<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetRpPertahunSasaran extends Model
{
    protected $table = 'target_rp_pertahun_sasarans';
    protected $guarded = 'id';

    public function pivot_sasaran_indikator()
    {
        return $this->belongsTo('App\Models\PivotSasaranIndikator', 'pivot_sasaran_indikator_id');
    }

    public function opd()
    {
        return $this->belongsTo('App\Models\MasterOpd', 'opd_id');
    }
}
