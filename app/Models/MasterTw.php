<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTw extends Model
{
    protected $table = 'master_tws';
    protected $fillable = ['nama'];
    protected $guarded = 'id';

    public function program_tw_realisasi()
    {
        return $this->hasMany('App\Models\ProgramTwRealisasi', 'tw_id');
    }

    public function kegiatan_tw_realisasi()
    {
        return $this->hasMany('App\Models\KegiatanTwRealisasi', 'tw_id');
    }

    public function sub_kegiatan_tw_realisas()
    {
        return $this->hasMany('App\Models\SubKegiatanTwRealisasi', 'tw_id');
    }
}
