<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramIndikatorKinerja extends Model
{
    protected $table = 'program_indikator_kinerjas';
    protected $guarded = 'id';

    public function program()
    {
        return $this->belongsTo('App\Models\Program', 'program_id');
    }
}
