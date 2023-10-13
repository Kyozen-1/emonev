<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\AkunOpd;
use App\Models\MasterOpd;
use App\Models\TahunPeriode;
use App\Models\Urusan;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Tujuan;
use App\Models\Sasaran;
use App\Models\TujuanPd;
use App\Models\SasaranPd;
use App\Models\SubKegiatanTargetSatuanRpRealisasi;
use App\Models\SubKegiatanTwRealisasi;

class DashboardController extends Controller
{
    public function index()
    {
        $getTahunPeriode = TahunPeriode::where('status', 'Aktif')->first();
        $countUrusan = Urusan::where('tahun_periode_id', $getTahunPeriode->id)->whereHas('program', function($q){
                            $q->whereHas('program_indikator_kinerja', function($q){
                                $q->whereHas('opd_program_indikator_kinerja', function($q){
                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                });
                            });
                        })->count();
        $countProgram = Program::where('tahun_periode_id', $getTahunPeriode->id)->whereHas('program_indikator_kinerja', function($q){
                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                $q->where('opd_id', Auth::user()->opd->opd_id);
                            });
                        })->count();
        $countKegiatan = Kegiatan::where('tahun_periode_id', $getTahunPeriode->id)->whereHas('kegiatan_indikator_kinerja', function($q){
                            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                $q->where('opd_id', Auth::user()->opd->opd_id);
                            });
                        })->count();

        $countSubKegiatan = SubKegiatan::where('tahun_periode_id', $getTahunPeriode->id)->whereHas('sub_kegiatan_indikator_kinerja', function($q){
                                $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q){
                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                });
                            })->count();

        $countTujuan = Tujuan::whereHas('tujuan_pd', function($q){
                            $q->where('opd_id', Auth::user()->opd->opd_id);
                        })->count();

        $countSasaran = Sasaran::whereHas('sasaran_pd', function($q){
                            $q->where('opd_id', Auth::user()->opd->opd_id);
                        })->count();

        $countTujuanPd = TujuanPd::where('opd_id', Auth::user()->opd->opd_id)->count();
        $countSasaranPd = SasaranPd::where('opd_id', Auth::user()->opd->opd_id)->count();

        $targetAnggaran = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
            $q->where('opd_id', Auth::user()->opd->opd_id);
            $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                    $q->where('tahun_periode_id', $getTahunPeriode->id);
                });
            });
        })->sum('target_anggaran_awal');

        $targetAnggaranPerubahan = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
            $q->where('opd_id', Auth::user()->opd->opd_id);
            $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                    $q->where('tahun_periode_id', $getTahunPeriode->id);
                });
            });
        })->sum('target_anggaran_perubahan');


        $targetRealisasi = SubKegiatanTwRealisasi::whereHas('sub_kegiatan_target_satuan_rp_realisasi', function($q) use ($getTahunPeriode){
            $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->where('opd_id', Auth::user()->opd->opd_id);
                $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                    $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                        $q->where('tahun_periode_id', $getTahunPeriode->id);
                    });
                });
            });
        })->sum('realisasi_rp');
        return view('opd.dashboard.index',[
            'getTahunPeriode' => $getTahunPeriode,
            'countUrusan' => $countUrusan,
            'countProgram' => $countProgram,
            'countKegiatan' => $countKegiatan,
            'countSubKegiatan' => $countSubKegiatan,
            'countTujuan' => $countTujuan,
            'countSasaran' => $countSasaran,
            'countTujuanPd' => $countTujuanPd,
            'countSasaranPd' => $countSasaranPd,
            'targetAnggaran' => $targetAnggaran,
            'targetAnggaranPerubahan' => $targetAnggaranPerubahan,
            'targetRealisasi' => $targetRealisasi
        ]);
    }

    public function change(Request $request)
    {
        $user = AkunOpd::find(Auth::user()->id);
        $user->color_layout = $request->color_layout;
        $user->nav_color = $request->nav_color;
        $user->behaviour = $request->behaviour;
        $user->layout = $request->layout;
        $user->radius = $request->radius;
        $user->placement = $request->placement;
        $user->save();
    }
}
