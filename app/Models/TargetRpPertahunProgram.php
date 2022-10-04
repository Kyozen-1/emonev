<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetRpPertahunProgram extends Model
{
    protected $table = 'target_rp_pertahun_programs';
    protected $guarded = 'id';

    public function pivot_program_indikator()
    {
        return $this->belongsTo('App\Models\PivotProgramIndikator', 'pivot_program_indikator_id');
    }
}
