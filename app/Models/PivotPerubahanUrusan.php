<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotPerubahanUrusan extends Model
{
    protected $table = 'pivot_perubahan_urusans';
    protected $guarded = 'id';

    public function urusan()
    {
        return $this->belongsTo('App\Models\Urusan', 'urusan_id');
    }
}
