<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tujuan extends Model
{
    protected $table = 'tujuans';
    protected $guarded = 'id';

    public function misi()
    {
        return $this->belongsTo('App\Models\Misi', 'misi_id');
    }

    public function pivot_perubahan_tujuan()
    {
        return $this->hasMany('App\Models\PivotPerubahanTujuan', 'tujuan_id');
    }

    public function sasaran()
    {
        return $this->hasMany('App\Models\Sasaran', 'tujuan_id');
    }

    public function tujuan_indikator_kinerja()
    {
        return $this->hasMany('App\Models\TujuanIndikatorKinerja', 'tujuan_id');
    }

    public function tujuan_pd()
    {
        return $this->hasMany('App\Models\TujuanPd', 'tujuan_id');
    }
}
