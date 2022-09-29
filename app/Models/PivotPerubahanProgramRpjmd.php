<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotPerubahanProgramRpjmd extends Model
{
    protected $table = 'pivot_perubahan_program_rpjmds';
    protected $guarded = 'id';

    public function program_rpjmd()
    {
        return $this->belongsTo('App\Models\ProgramRpjmd', 'program_rpjmd_id');
    }
}
