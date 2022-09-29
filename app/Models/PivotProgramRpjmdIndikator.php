<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotProgramRpjmdIndikator extends Model
{
    protected $table = 'pivot_program_rpjmd_indikators';
    protected $guarded = 'id';

    public function program_rpjmd()
    {
        return $this->belongsTo('App\Models\ProgramRpjmd', 'program_rpjmd_id');
    }

    public function target_rp_pertahun_program_rpjmd()
    {
        return $this->hasMany('App\Models\TargetRpPertahunRpjmd', 'pivot_program_rpjmd_indikator_id');
    }
}
