<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotSasaranIndikator extends Model
{
    protected $table = 'pivot_sasaran_indikators';
    protected $guarded = 'id';

    public function sasaran()
    {
        return $this->belongsTo('App\Models\Sasaran', 'sasaran_id');
    }

    public function pivot_sasaran_indikator_program_rpjmd()
    {
        return $this->hasMany('App\Models\PivotSasaranIndikatorProgramRpjmd', 'pivot_sasaran_indikator_id');
    }
}
