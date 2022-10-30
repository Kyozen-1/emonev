<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetRpPertahunRenstraKegiatan extends Model
{
    protected $table = 'target_rp_pertahun_renstra_kegiatans';
    protected $guarded = 'id';

    public function renstra_kegiatan()
    {
        return $this->belongsTo('App\Models\RenstraKegiatan', 'renstra_kegiatan_id');
    }

    public function opd()
    {
        return $this->belongsTo('App\Models\MasterOpd', 'opd_id');
    }
}
