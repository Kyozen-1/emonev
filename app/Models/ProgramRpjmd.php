<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramRpjmd extends Model
{
    protected $table = 'program_rpjmds';
    protected $guarded = 'id';

    public function program()
    {
        return $this->belongsTo('App\Models\Program', 'program_id');
    }

    public function sasaran()
    {
        return $this->belongsTo('App\Models\Sasaran', 'sasaran_id');
    }

    public function master_opd()
    {
        return $this->belongsTo('App\Models\MasterOpd', 'opd_id');
    }
}
