<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetRpPertahunProgramRpjmd extends Model
{
    protected $table = 'target_rp_pertahun_program_rpjmds';
    protected $guarded = 'id';

    public function pivot_program_rpjmd_indikator()
    {
        return $this->belongsTo('App\Models\PivotProgramRpjmdIndikator', 'pivot_program_rpjmd_indikator_id');
    }
}
