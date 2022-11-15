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

    public function sasaran_indikator_kinerja()
    {
        return $this->hasMany('App\Models\SasaranIndikatorKinerja', 'sasaran_id');
    }

    public function sasaran_pd()
    {
        return $this->hasMany('App\Models\SasaranPd', 'sasaran_id');
    }
}
