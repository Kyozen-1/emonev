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

    public function pivot_sasaran_indikator()
    {
        return $this->belongsTo('App\Models\PivotSasaranIndikator', 'sasaran_indikator_id');
    }
}
