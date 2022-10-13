<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotOpdProgramRpjmd extends Model
{
    protected $table = 'pivot_opd_program_rpjmds';
    protected $guarded = 'id';

    public function program_rpjmd()
    {
        return $this->belongsTo('App\Models\ProgramRpjmd', 'program_rpjmd_id');
    }

    public function opd()
    {
        return $this->belongsTo('App\Models\MasterOpd', 'opd_id');
    }
}
