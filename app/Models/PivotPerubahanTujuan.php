<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotPerubahanTujuan extends Model
{
    protected $table = 'pivot_perubahan_tujuans';
    protected $guarded = 'id';

    public function tujuan()
    {
        return $this->belongsTo('App\Models\Tujuan', 'tujuan_id');
    }
}
