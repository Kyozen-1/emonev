<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SasaranPdProgramRpjmd extends Model
{
    protected $table = 'sasaran_pd_program_rpjmds';
    protected $guarded = 'id';

    public function sasaran_pd()
    {
        return $this->belongsTo('App\Models\SasaranPd', 'sasaran_pd_id');
    }

    public function program_rpjmd()
    {
        return $this->belongsTo('App\Models\ProgramRpjmd', 'program_rpjmd_id');
    }
}
