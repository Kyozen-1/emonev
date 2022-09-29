<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotPerubahanProgram extends Model
{
    protected $table = 'pivot_perubahan_programs';
    protected $guarded = 'id';

    public function program()
    {
        return $this->belongsTo('App\Models\Program', 'program_id');
    }
}
