<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterOpd extends Model
{
    protected $table = 'master_opds';
    protected $guarded = 'id';

    public function jenis_opd()
    {
        return $this->belongsTo('App\Models\JenisOpd', 'jenis_opd_id');
    }

    public function opd()
    {
        return $this->hasMany('App\Models\Opd', 'opd_id');
    }

    public function opd_program_indikator_kinerja()
    {
        return $this->hasMany('App\Models\OpdProgramIndikatorKinerja', 'opd_id');
    }
}
