<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotProgramKegiatanRenstra extends Model
{
    protected $table = 'pivot_program_kegiatan_renstras';
    protected $guarded = 'id';

    public function program()
    {
        return $this->belongsTo('App\Models\Program', 'program_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo('App\Models\Kegiatan', 'kegiatan_id');
    }

    public function program_rpjmd()
    {
        return $this->belongsTo('App\Models\ProgramRpjmd', 'program_rpjmd_id');
    }
}
