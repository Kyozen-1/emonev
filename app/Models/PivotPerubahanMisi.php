<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotPerubahanMisi extends Model
{
    protected $table = 'pivot_perubahan_misis';
    protected $guarded = 'id';

    public function misi()
    {
        return $this->belongsTo('App\Models\Misi', 'misi_id');
    }
}
