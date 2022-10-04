<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renstra extends Model
{
    protected $table = 'renstras';
    protected $guarded = 'id';

    public function misi()
    {
        return $this->belongsTo('App\models\Misi', 'misi_id');
    }

    public function tujuan()
    {
        return $this->belongsTo('App\Models\Tujuan', 'tujuan_id');
    }

    public function sasaran()
    {
        return $this->belongsTo('App\Models\Sasaran', 'sasaran_id');
    }

    public function program()
    {
        return $this->belongsTo('App\Models\Program', 'program_id');
    }

    public function opd()
    {
        return $this->belongsTo('App\Models\Opd', 'opd_id');
    }

    public function target_rp_pertahun_program()
    {
        return $this->hasMany('App\Models\TargetRpPertahunProgram','renstra_id');
    }

    public function target_rp_pertahun_tujuan()
    {
        return $this->hasMany('App\Models\TargetRpPertahunTujuan', 'renstra_id');
    }

    public function target_rp_pertahun_sasaran()
    {
        return $this->hasMany('App\Models\TargetRpPertahunSasaran', 'renstra_id');
    }
}
