<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs';
    protected $guarded = 'id';

    public function pivot_perubahan_program()
    {
        return $this->hasMany('App\Models\PivotPerubahanProgram', 'program_id');
    }

    public function pivot_program_indikator()
    {
        return $this->hasMany('App\Models\PivotProgramIndikator', 'program_id');
    }

    public function urusan()
    {
        return $this->belongsTo('App\Models\Urusan', 'urusan_id');
    }

    public function kegiatan()
    {
        return $this->hasMany('App\Models\Kegiatan', 'program_id');
    }

    public function renstra()
    {
        return $this->hasMany('App\Models\Renstra', 'program_id');
    }

    public function target_rp_pertahun_program()
    {
        return $this->hasMany('App\Models\TargetRpPertahunProgram', 'program_id');
    }
}
