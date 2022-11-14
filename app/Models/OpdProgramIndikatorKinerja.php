<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpdProgramIndikatorKinerja extends Model
{
    protected $table = 'opd_program_indikator_kinerjas';
    protected $guarded = 'id';

    public function program_indikator_kinerja()
    {
        return $this->belongsTo('App\Models\ProgramIndikatorKinerja', 'program_indikator_kinerja_id');
    }

    public function opd()
    {
        return $this->belongsTo('App\Models\MasterOpd', 'opd_id');
    }

    public function program_target_satuan_rp_realisasi()
    {
        return $this->hasMany('App\Models\ProgramTargetSatuanRpRealisasi', 'opd_program_indikator_kinerja_id');
    }
}
