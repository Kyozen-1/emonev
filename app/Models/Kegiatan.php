<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatans';
    protected $guarded = 'id';

    public function program()
    {
        return $this->belongsTo('App\Models\Program', 'program_id');
    }

    public function pivot_perubahan_kegiatan()
    {
        return $this->hasMany('App\Models\PivotPerubahanKegiatan', 'program_id');
    }

    public function sub_kegiatan()
    {
        return $this->hasMany('App\Models\SubKegiatan', 'sub_kegiatan_id');
    }

    public function pivot_program_kegiatan_renstra()
    {
        return $this->hasMany('App\Models\PivotProgramKegiatanRenstra', 'kegiatan_id');
    }

    public function renstra_kegiatan()
    {
        return $this->hasMany('App\Models\RenstraKegiatan', 'kegiatan_id');
    }
}
