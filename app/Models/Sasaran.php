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
}
