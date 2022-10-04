<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotProgramIndikator extends Model
{
    protected $table = 'pivot_program_indikators';
    protected $guarded = 'id';

    public function program()
    {
        return $this->belongsTo('App\Models\Program', 'program_id');
    }

    public function target_rp_pertahun_program()
    {
        return $this->hasMany('App\Models\TargetRpPertahunProgram', 'pivot_program_indikator_id');
    }
}
