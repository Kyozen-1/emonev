<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotPerubahanSasaranPd extends Model
{
    protected $table = 'pivot_perubahan_sasaran_pds';
    protected $guarded = 'id';

    public function sasaran_pd()
    {
        return $this->belongsTo('App\Models\SasaranPd', 'sasaran_pd_id');
    }
}
