<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SasaranPd extends Model
{
    protected $table = 'sasaran_pds';
    protected $guarded = 'id';

    public function sasaran()
    {
        return $this->belongsTo('App\Models\Sasaran', 'sasaran_id');
    }

    public function sasaran_pd_indikator_kinerja()
    {
        return $this->hasMany('App\Models\SasaranPdIndikatorKinerja', 'sasaran_pd_id');
    }

    public function pivot_perubahan_sasaran_pd()
    {
        return $this->hasMany('App\Models\PivotPerubahanSasaranPd', 'sasaran_pd_id');
    }

    public function opd()
    {
        return $this->belongsTo('App\Models\MasterOpd', 'opd_id');
    }
}
