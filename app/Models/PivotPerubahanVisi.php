<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotPerubahanVisi extends Model
{
    protected $table = 'pivot_perubahan_visis';
    protected $guarded = 'id';

    public function visi()
    {
        return $this->belongsTo('App\Models\Visi', 'visi_id');
    }
}
