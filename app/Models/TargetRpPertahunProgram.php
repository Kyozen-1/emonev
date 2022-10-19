<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetRpPertahunProgram extends Model
{
    protected $table = 'target_rp_pertahun_programs';
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
