<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visi extends Model
{
    protected $table = 'visis';
    protected $guarded = 'id';

    public function pivot_perubahan_visi()
    {
        return $this->hasMany('App\Models\PivotPerubahanVisi', 'visi_id');
    }

    public function misi()
    {
        return $this->hasMany('App\Models\Misi', 'visi_id');
    }
}
