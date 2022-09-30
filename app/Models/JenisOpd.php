<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisOpd extends Model
{
    protected $table = 'jenis_opds';
    protected $guarded = 'id';

    public function master_opd()
    {
        return $this->hasMany('App\Models\MasterOpd', 'jenis_opd_id');
    }
}
