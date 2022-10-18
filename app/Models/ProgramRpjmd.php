<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramRpjmd extends Model
{
    protected $table = 'program_rpjmds';
    protected $guarded = 'id';

    public function pivot_sasaran_indikator_program_rpjmd()
    {
        return $this->hasMany('App\Models\PivotSasaranIndikatorProgramRpjmd', 'program_rpjmd_id');
    }

    public function pivot_opd_program_rpjmd()
    {
        return $this->hasMany('App\Models\PivotOpdProgramRpjmd', 'program_rpjmd_id');
    }

    public function pivot_program_kegiatan_renstra()
    {
        return $this->hasMany('App\Models\PivotProgramKegiatanRenstra', 'program_rpjmd_id');
    }
}
