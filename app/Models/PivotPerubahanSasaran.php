<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotPerubahanSasaran extends Model
{
    protected $table = 'pivot_perubahan_sasarans';
    protected $guarded = 'id';

    public function sasaran()
    {
        return $this->belongsTo('App\Models\Sasaran', 'sasaran_id');
    }
}
