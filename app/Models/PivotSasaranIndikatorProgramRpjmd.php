<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotSasaranIndikatorProgramRpjmd extends Model
{
    protected $table = 'pivot_sasaran_indikator_program_rpjmds';
    protected $guarded = 'id';

    public function program_rpjmd()
    {
        return $this->belongsTo('App\Models\ProgramRpjmd', 'program_rpjmd_id');
    }

    public function sasaran_indikator_kinerja()
    {
        return $this->belongsTo('App\Models\SasaranIndikatorKinerja', 'sasaran_indikator_kinerja_id');
    }
}
