<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotTahunMasterSkalaNilaiPeringkatKinerja extends Model
{
    protected $table = 'pivot_tahun_master_skala_nilai_peringkat_kinerjas';

    public function master_skala_nilai_perangkat_kinerja()
    {
        return $this->belongsTo('App\Models\MasterSkalaNilaiPeringkatKinerja', 'master_skala_nilai_peringkat_kinerja_id');
    }
}
