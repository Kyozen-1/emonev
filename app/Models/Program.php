<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs';
    protected $guarded = 'id';

    public function urusan()
    {
        return $this->belongsTo('App\Models\Urusan', 'urusan_id');
    }

    public function pivot_perubahan_program()
    {
        return $this->hasMany('App\Models\PivotPerubahanProgram', 'program_id');
    }

    public function kegiatan()
    {
        return $this->hasMany('App\Models\Kegiatan', 'program_id');
    }

    public function pivot_program_kegiatan_renstra()
    {
        return $this->hasMany('App\Models\PivotProgramKegiatanRenstra', 'program_id');
    }

    public function program_rpjmd()
    {
        return $this->hasMany('App\Models\ProgramRpjmd', 'program_id');
    }
}
