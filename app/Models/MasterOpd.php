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

    public function program_rpjmd()
    {
        return $this->hasMany('App\Models\ProgramRpjmd', 'opd_id');
    }

    public function opd()
    {
        return $this->hasMany('App\Models\Opd', 'opd_id');
    }

    public function target_rp_pertahun_tujuan()
    {
        return $this->hasMany('App\Models\TargetRpPertahunTujuan', 'opd_id');
    }

    public function target_rp_pertahun_sasaran()
    {
        return $this->hasMany('App\Models\TargetRpPertahunSasaran', 'opd_id');
    }

    public function target_rp_pertahun_program()
    {
        return $this->hasMany('App\Models\TargetRpPertahunProgram', 'opd_id');
    }
}
