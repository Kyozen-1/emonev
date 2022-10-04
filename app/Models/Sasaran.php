<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sasaran extends Model
{
    protected $table = 'sasarans';
    protected $guarded = 'id';

    public function tujuan()
    {
        return $this->belongsTo('App\Models\Tujuan', 'tujuan_id');
    }

    public function pivot_perubahan_sasaran()
    {
        return $this->hasMany('App\Models\PivotPerubahanSasaran', 'sasaran_id');
    }

    public function pivot_sasaran_indikator()
    {
        return $this->hasMany('App\Models\PivotSasaranIndikator', 'sasaran_id');
    }

    public function program_rpjmd()
    {
        return $this->hasMany('App\Models\ProgramRpjmd', 'sasaran_id');
    }

    public function renstra()
    {
        return $this->hasMany('App\Models\Renstra', 'sasaran_id');
    }

    public function target_rp_pertahun_sasaran()
    {
        return $this->hasMany('App\Models\TargetRpPertahunSasaran', 'sasaran_id');
    }
}
