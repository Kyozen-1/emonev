<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Urusan;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Visi;
use App\Models\Misi;
use App\Models\Tujuan;
use App\Models\Sasaran;
use App\Models\TahunPeriode;

class NormalisasiController extends Controller
{
    public function memberikan_id_tahun_periode()
    {
        $tahun_periode = TahunPeriode::where('status', 'Aktif')->first();
        $urusans = Urusan::all();
        foreach ($urusans as $urusan) {
            $urusan = Urusan::find($urusan->id);
            $urusan->tahun_periode_id = $tahun_periode->id;
            $urusan->save();
        }

        $programs = Program::all();
        foreach ($programs as $program) {
            $program = Program::find($program->id);
            $program->tahun_periode_id = $tahun_periode->id;
            $program->save();
        }

        $kegiatans = Kegiatan::all();
        foreach ($kegiatans as $kegiatan) {
            $kegiatan = Kegiatan::find($kegiatan->id);
            $kegiatan->tahun_periode_id = $tahun_periode->id;
            $kegiatan->save();
        }

        $sub_kegiatans = SubKegiatan::all();
        foreach ($sub_kegiatans as $sub_kegiatan) {
            $sub_kegiatan = SubKegiatan::find($sub_kegiatan->id);
            $sub_kegiatan->tahun_periode_id = $tahun_periode->id;
            $sub_kegiatan->save();
        }

        $visis = Visi::all();
        foreach ($visis as $visi) {
            $visi = Visi::find($visi->id);
            $visi->tahun_periode_id = $tahun_periode->id;
            $visi->save();
        }

        $misis = Misi::all();
        foreach ($misis as $misi) {
            $misi = Misi::find($misi->id);
            $misi->tahun_periode_id = $tahun_periode->id;
            $misi->save();
        }

        $tujuans = Tujuan::all();
        foreach ($tujuans as $tujuan) {
            $tujuan = Tujuan::find($tujuan->id);
            $tujuan->tahun_periode_id = $tahun_periode->id;
            $tujuan->save();
        }

        $sasarans = Sasaran::all();
        foreach ($sasarans as $sasaran) {
            $sasaran = Sasaran::find($sasaran->id);
            $sasaran->tahun_periode_id = $tahun_periode->id;
            $sasaran->save();
        }

        return 'Berhasil normalisasi memberikan periode id';
    }
}
