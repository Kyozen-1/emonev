<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RenstraKegiatan extends Model
{
    protected $table = 'renstra_kegiatans';
    protected $guarded = 'id';

    public function program_rpjmd()
    {
        return $this->belongsTo('App\Models\ProgramRpjmd', 'program_rpjmd_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo('App\Models\Kegiatan', 'kegiatan_id');
    }

    public function pivot_opd_renstra_kegiatan()
    {
        return $this->belongsTo('App\Models\PivotOpdRenstraKegiatan', 'renstra_kegiatan_id');
    }
}
