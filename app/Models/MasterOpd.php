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

    public function target_rp_pertahun_program()
    {
        return $this->hasMany('App\Models\TargetRpPertahunProgram', 'opd_id');
    }

    public function pivot_opd_renstra_kegiatan()
    {
        return $this->belongsTo('App\Models\PivotOpdRenstraKegiatan', 'opd_id');
    }

    public function target_rp_pertahun_renstra_kegiatan()
    {
        return $this->hasMany('App\Models\TargetRpPertahunRenstraKegiatan', 'opd_id');
    }
}
