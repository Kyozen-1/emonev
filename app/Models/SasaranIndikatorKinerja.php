<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SasaranIndikatorKinerja extends Model
{
    protected $table = 'sasaran_indikator_kinerjas';
    protected $guarded = 'id';

    public function sasaran()
    {
        return $this->belongsTo('App\Models\Sasaran', 'sasaran_id');
    }

    public function sasaran_target_satuan_rp_realisasi()
    {
        return $this->hasMany('App\Models\SasaranTargetSatuanRpRealisasi', 'sasaran_indikator_kinerja_id');
    }

    public function pivot_sasaran_indikator_program_rpjmd()
    {
        return $this->hasMany('App\Models\PivotSasaranIndikatorProgramRpjmd', 'sasaran_indikator_kinerja_id');
    }
}
