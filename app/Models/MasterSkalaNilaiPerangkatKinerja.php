<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSkalaNilaiPerangkatKinerja extends Model
{
    public function pivot_tahun_master_skala_nilai_peringkat_kinerja()
    {
        return $this->hasMany('App\Models\PivotTahunMasterSkalaNilaiPeringkatKinerja', 'master_skala_nilai_peringkat_kinerja_id');
    }
}
