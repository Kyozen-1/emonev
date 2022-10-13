<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotSasaranIndikator extends Model
{
    protected $table = 'pivot_sasaran_indikators';
    protected $guarded = 'id';

    public function sasaran()
    {
        return $this->belongsTo('App\Models\Sasaran', 'sasaran_id');
    }

    public function target_rp_pertahun_sasaran()
    {
        return $this->hasMany('App\Models\TargetRpPertahunSasaran', 'pivot_sasaran_indikator_id');
    }

    public function program_rpjmd()
    {
        return $this->hasMany('App\Models\ProgramRpjmd', 'pivot_sasaran_indikator_id');
    }
}
