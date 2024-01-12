<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterOpd extends Model
{
    protected $table = 'master_opds';
    protected $guarded = 'id';

    public function jenis_opd()
    {
        return $this->belongsTo('App\Models\JenisOpd', 'jenis_opd_id');
    }

    public function opd()
    {
        return $this->hasMany('App\Models\Opd', 'opd_id');
    }

    public function opd_program_indikator_kinerja()
    {
        return $this->hasMany('App\Models\OpdProgramIndikatorKinerja', 'opd_id');
    }

    public function tujuan_pd()
    {
        return $this->hasMany('App\Models\TujuanPd', 'opd_id');
    }

    public function sasaran_pd()
    {
        return $this->hasMany('App\Models\SasaranPd', 'opd_id');
    }

    public function opd_kegiatan_indikator_kinerja()
    {
        return $this->hasMany('App\Models\OpdKegiatanIndikatorKinerja', 'opd_id');
    }

    public function rkpd_opd_tahun_pembangunan()
    {
        return $this->hasMany('App\Models\RkpdOpdTahunPembangunan', 'opd_id');
    }

    public function faktor_tindak_lanjut_e81()
    {
        return $this->hasMany('App\Models\FaktorTindakLanjutE81', 'opd_id');
    }
}
