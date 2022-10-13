<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramRpjmd extends Model
{
    protected $table = 'program_rpjmds';
    protected $guarded = 'id';

    public function pivot_sasaran_indikator()
    {
        return $this->belongsTo('App\Models\PivotSasaranIndikator', 'pivot_sasaran_indikator_id');
    }
}
