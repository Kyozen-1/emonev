<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotPerubahanTujuanPd extends Model
{
    protected $table = 'pivot_perubahan_tujuan_pd';
    protected $guarded = 'id';

    public function tujuan_pd()
    {
        return $this->belongsTo('App\Models\TujuanPd', 'tujuan_pd_id');
    }
}
