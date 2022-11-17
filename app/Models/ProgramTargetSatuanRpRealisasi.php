<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramTargetSatuanRpRealisasi extends Model
{
    protected $table = 'program_target_satuan_rp_realisasis';
    protected $guarded = 'id';

    public function opd_program_indikator_kinerja()
    {
        return $this->belongsTo('App\Models\OpdProgramIndikatorKinerja', 'opd_program_indikator_kinerja_id');
    }

    public function program_tw_realisasi()
    {
        return $this->hasMany('App\Models\ProgramTwRealisasi', 'program_target_satuan_rp_realisasi_id');
    }
}
