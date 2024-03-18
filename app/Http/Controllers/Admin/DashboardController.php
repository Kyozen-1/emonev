<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use DB;
use Validator;
use Carbon\Carbon;
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
use App\Models\MasterTw;
use App\Models\ProgramIndikatorKinerja;
use App\Models\OpdProgramIndikatorKinerja;
use App\Models\ProgramTargetSatuanRpRealisasi;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanIndikatorKinerja;
use App\Models\OpdKegiatanIndikatorKinerja;
use App\Models\KegiatanTargetSatuanRpRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\SubKegiatanIndikatorKinerja;
use App\Models\OpdSubKegiatanIndikatorKinerja;
use App\Models\PivotPerubahanTujuanPd;
use App\Models\TujuanPdIndikatorKinerja;
use App\Models\TujuanPdTargetSatuanRpRealisasi;
use App\Models\TujuanPdRealisasiRenja;
use App\Models\PivotPerubahanSasaranPd;
use App\Models\SasaranPdIndikatorKinerja;
use App\Models\SasaranPdTargetSatuanRpRealisasi;
use App\Models\SasaranPdRealisasiRenja;
use App\Models\SasaranPdProgramRpjmd;
use App\Models\ProgramRpjmd;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotPerubahanKegiatan;
use App\Models\PivotPerubahanSubKegiatan;

class DashboardController extends Controller
{
    public function index()
    {
        $getTahunPeriode = TahunPeriode::where('status', 'Aktif')->first();
        $countUrusan = Urusan::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countProgram = Program::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countKegiatan = Kegiatan::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countSubKegiatan = SubKegiatan::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countOpd = MasterOpd::count();
        $countTujuan = Tujuan::count();
        $countSasaran = Sasaran::count();
        $targetAnggaran = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
            $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                    $q->where('tahun_periode_id', $getTahunPeriode->id);
                });
            });
        })->sum('target_anggaran_awal');

        $targetAnggaranPerubahan = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
            $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                    $q->where('tahun_periode_id', $getTahunPeriode->id);
                });
            });
        })->sum('target_anggaran_perubahan');


        $targetRealisasi = SubKegiatanTwRealisasi::whereHas('sub_kegiatan_target_satuan_rp_realisasi', function($q) use ($getTahunPeriode){
            $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                    $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                        $q->where('tahun_periode_id', $getTahunPeriode->id);
                    });
                });
            });
        })->sum('realisasi_rp');

        $opds = MasterOpd::paginate(10);
        $tws = MasterTw::select('id')->get();

        $opdsTransform = $opds
            ->getCollection()
            ->map(function($data) use ($getTahunPeriode, $tws){

                $tahun_awal = $getTahunPeriode->tahun_awal;
                $jarak_tahun = $getTahunPeriode->tahun_akhir - $tahun_awal;
                $tahuns = [];
                for ($i=0; $i < $jarak_tahun + 1; $i++) {
                    $tahuns[] = $tahun_awal + $i;
                }
                // Tujuan Pd Start
                    $getIndikatorTujuanPds = TujuanPdIndikatorKinerja::whereHas('tujuan_pd', function($q) use($data){
                        $q->where('opd_id', $data->id);
                    })->get();
                    $jumlah_indikator_tujuan_pd = TujuanPdIndikatorKinerja::whereHas('tujuan_pd', function($q) use($data){
                        $q->where('opd_id', $data->id);
                    })->count();

                    $countTargetTujuanPd = 0;
                    foreach ($getIndikatorTujuanPds as $getIndikatorTujuanPd) {
                        foreach ($tahuns as $tahun) {
                            $cekTujuanPdTarget = TujuanPdTargetSatuanRpRealisasi::where('tahun', $tahun)->whereHas('tujuan_pd_indikator_kinerja', function($q) use ($data, $getIndikatorTujuanPd){
                                $q->where('id', $getIndikatorTujuanPd->id);
                                $q->whereHas('tujuan_pd', function($q) use ($data){
                                    $q->where('opd_id', $data->id);
                                });
                            })->first();
                            if($cekTujuanPdTarget)
                            {
                                $countTargetTujuanPd++;
                            }
                        }
                    }

                    $countRealisasiTujuanPd = 0;
                    foreach ($getIndikatorTujuanPds as $getIndikatorTujuanPd) {
                        foreach ($tahuns as $tahun) {
                            $cekTujuanPdTarget = TujuanPdTargetSatuanRpRealisasi::where('tahun', $tahun)->whereHas('tujuan_pd_indikator_kinerja', function($q) use ($data, $getIndikatorTujuanPd){
                                $q->where('id', $getIndikatorTujuanPd->id);
                                $q->whereHas('tujuan_pd', function($q) use ($data){
                                    $q->where('opd_id', $data->id);
                                });
                            })->first();
                            if($cekTujuanPdTarget)
                            {
                                $cekRealisasiTujuanPd = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cekTujuanPdTarget->id)->first();
                                if($cekRealisasiTujuanPd)
                                {
                                    $countRealisasiTujuanPd++;
                                }
                            }
                        }
                    }

                    if($jumlah_indikator_tujuan_pd)
                    {
                        $target_tujuan_pd = ($countTargetTujuanPd / ($jumlah_indikator_tujuan_pd * count($tahuns))) * 100;
                        $realisasi_tujuan_pd = ($countRealisasiTujuanPd / ($jumlah_indikator_tujuan_pd * count($tahuns))) * 100;
                    } else {
                        $target_tujuan_pd = 0;
                        $realisasi_tujuan_pd = 0;
                    }
                // Tujuan Pd End

                // Sasaran Pd Start
                    $getIndikatorSasaranPds = SasaranPdIndikatorKinerja::whereHas('sasaran_pd', function($q) use($data){
                        $q->where('opd_id', $data->id);
                    })->get();
                    $jumlah_indikator_sasaran_pd = SasaranPdIndikatorKinerja::whereHas('sasaran_pd', function($q) use($data){
                        $q->where('opd_id', $data->id);
                    })->count();

                    $countTargetSasaranPd = 0;
                    foreach ($getIndikatorSasaranPds as $getIndikatorSasaranPd) {
                        foreach ($tahuns as $tahun) {
                            $cekSasaranPdTarget = SasaranPdTargetSatuanRpRealisasi::where('tahun', $tahun)->whereHas('sasaran_pd_indikator_kinerja', function($q) use ($data, $getIndikatorSasaranPd){
                                $q->where('id', $getIndikatorSasaranPd->id);
                                $q->whereHas('sasaran_pd', function($q) use ($data){
                                    $q->where('opd_id', $data->id);
                                });
                            })->first();
                            if($cekSasaranPdTarget)
                            {
                                $countTargetSasaranPd++;
                            }
                        }
                    }

                    $countRealisasiSasaranPd = 0;
                    foreach ($getIndikatorSasaranPds as $getIndikatorSasaranPd) {
                        foreach ($tahuns as $tahun) {
                            $cekSasaranPdTarget = SasaranPdTargetSatuanRpRealisasi::where('tahun', $tahun)->whereHas('sasaran_pd_indikator_kinerja', function($q) use ($data, $getIndikatorSasaranPd){
                                $q->where('id', $getIndikatorSasaranPd->id);
                                $q->whereHas('sasaran_pd', function($q) use ($data){
                                    $q->where('opd_id', $data->id);
                                });
                            })->first();
                            if($cekSasaranPdTarget)
                            {
                                $cekRealisasiSasaranPd = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cekSasaranPdTarget->id)->first();
                                if($cekRealisasiSasaranPd)
                                {
                                    $countRealisasiSasaranPd++;
                                }
                            }
                        }
                    }

                    if($jumlah_indikator_sasaran_pd)
                    {
                        $target_sasaran_pd = ($countTargetSasaranPd / ($jumlah_indikator_sasaran_pd * count($tahuns))) * 100;
                        $realisasi_sasaran_pd = ($countRealisasiSasaranPd / ($jumlah_indikator_sasaran_pd * count($tahuns))) * 100;
                    } else {
                        $target_sasaran_pd = 0;
                        $realisasi_sasaran_pd = 0;
                    }
                // Sasaran Pd End

                // Program Start
                    $getIndikatorPrograms = ProgramIndikatorKinerja::whereHas('opd_program_indikator_kinerja',function($q) use ($data){
                                                $q->where('opd_id', $data->id);
                                            })->whereHas('program', function($q) use ($data) {
                                                $q->whereHas('program_rpjmd', function($q) use ($data){
                                                    $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                                        $q->whereHas('sasaran_pd', function($q) use ($data){
                                                            $q->where('opd_id', $data->id);
                                                        });
                                                    });
                                                });
                                            })->get();
                    $jumlah_indikator_program = ProgramIndikatorKinerja::whereHas('opd_program_indikator_kinerja',function($q) use ($data){
                                                        $q->where('opd_id', $data->id);
                                                    })->whereHas('program', function($q) use ($data) {
                                                        $q->whereHas('program_rpjmd', function($q) use ($data){
                                                            $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                                                $q->whereHas('sasaran_pd', function($q) use ($data){
                                                                    $q->where('opd_id', $data->id);
                                                                });
                                                            });
                                                        });
                                                    })->count();

                    $countTargetProgram = 0;
                    foreach ($getIndikatorPrograms as $getIndikatorProgram) {
                        $cekOpdProgramIndikatorKinerja = OpdProgramIndikatorKinerja::where('opd_id', $data->id)->where('program_indikator_kinerja_id', $getIndikatorProgram->id)->first();
                        if($cekOpdProgramIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekProgramTarget = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_program_indikator_kinerja_id', $cekOpdProgramIndikatorKinerja->id)->first();
                                if($cekProgramTarget)
                                {
                                    $countTargetProgram++;
                                }
                            }
                        }
                    }

                    $countRealisasiProgram = 0;
                    foreach ($getIndikatorPrograms as $getIndikatorProgram) {
                        $cekOpdProgramIndikatorKinerja = OpdProgramIndikatorKinerja::where('opd_id', $data->id)->where('program_indikator_kinerja_id', $getIndikatorProgram->id)->first();
                        if($cekOpdProgramIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekProgramTarget = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_program_indikator_kinerja_id', $cekOpdProgramIndikatorKinerja->id)->first();
                                if($cekProgramTarget)
                                {
                                    foreach ($tws as $tw) {
                                        $cekProgramRealisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cekProgramTarget->id)
                                                                ->where('tw_id', $tw->id)
                                                                ->first();
                                        if($cekProgramRealisasi)
                                        {
                                            $countRealisasiProgram++;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if($jumlah_indikator_program)
                    {
                        $target_program = ($countTargetProgram / ($jumlah_indikator_program * count($tahuns))) * 100;
                        $realisasi_program = (($countRealisasiProgram/4) / ($jumlah_indikator_program * count($tahuns))) * 100;
                    } else {
                        $target_program = 0;
                        $realisasi_program = 0;
                    }
                // Program End

                // Kegiatan Start
                    $getIndikatorKegiatans = KegiatanIndikatorKinerja::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($data){
                        $q->where('opd_id', $data->id);
                    })->whereHas('kegiatan', function($q) use ($data){
                        $q->whereHas('program', function($q) use ($data){
                            $q->whereHas('program_rpjmd', function($q) use ($data){
                                $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                    $q->whereHas('sasaran_pd', function($q) use ($data){
                                        $q->where('opd_id', $data->id);
                                    });
                                });
                            });
                        });
                    })->get();

                    $jumlah_indikator_kegiatan = KegiatanIndikatorKinerja::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($data){
                                                    $q->where('opd_id', $data->id);
                                                })->whereHas('kegiatan', function($q) use ($data){
                                                    $q->whereHas('program', function($q) use ($data){
                                                        $q->whereHas('program_rpjmd', function($q) use ($data){
                                                            $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                                                $q->whereHas('sasaran_pd', function($q) use ($data){
                                                                    $q->where('opd_id', $data->id);
                                                                });
                                                            });
                                                        });
                                                    });
                                                })->count();
                    $countTargetKegiatan = 0;
                    foreach ($getIndikatorKegiatans as $getIndikatorKegiatan) {
                        $cekOpdKegiatanIndikatorKinerja = OpdKegiatanIndikatorKinerja::where('opd_id', $data->id)->where('kegiatan_indikator_kinerja_id', $getIndikatorKegiatan->id)->first();
                        if($cekOpdKegiatanIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekKegiatanTarget = KegiatanTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_kegiatan_indikator_kinerja_id', $cekOpdKegiatanIndikatorKinerja->id)->first();
                                if($cekKegiatanTarget)
                                {
                                    $countTargetKegiatan++;
                                }
                            }
                        }
                    }

                    $countRealisasiKegiatan = 0;
                    foreach ($getIndikatorKegiatans as $getIndikatorKegiatan) {
                        $cekOpdKegiatanIndikatorKinerja = OpdKegiatanIndikatorKinerja::where('opd_id', $data->id)->where('kegiatan_indikator_kinerja_id', $getIndikatorKegiatan->id)->first();
                        if($cekOpdKegiatanIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekKegiatanTarget = KegiatanTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_kegiatan_indikator_kinerja_id', $cekOpdKegiatanIndikatorKinerja->id)->first();
                                if($cekKegiatanTarget)
                                {
                                    foreach ($tws as $tw) {
                                        $cekKegiatanRealisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cekKegiatanTarget->id)
                                                                ->where('tw_id', $tw->id)
                                                                ->first();
                                        if($cekKegiatanRealisasi)
                                        {
                                            $countRealisasiKegiatan++;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if($jumlah_indikator_kegiatan)
                    {
                        $target_kegiatan = ($countTargetKegiatan / ($jumlah_indikator_kegiatan * count($tahuns))) * 100;
                        $realisasi_kegiatan = (($countRealisasiKegiatan/4) / ($jumlah_indikator_kegiatan * count($tahuns))) * 100;
                    } else {
                        $target_kegiatan = 0;
                        $realisasi_kegiatan = 0;
                    }
                // Kegiatan End

                // Sub Kegiatan Start
                    $getIndikatorSubKegiatans = SubKegiatanIndikatorKinerja::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($data){
                                                    $q->where('opd_id', $data->id);
                                                })->whereHas('sub_kegiatan', function($q) use ($data){
                                                    $q->whereHas('kegiatan', function($q) use ($data){
                                                        $q->whereHas('program', function($q) use ($data){
                                                            $q->whereHas('program_rpjmd', function($q) use ($data){
                                                                $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                                                    $q->whereHas('sasaran_pd', function($q) use ($data){
                                                                        $q->where('opd_id', $data->id);
                                                                    });
                                                                });
                                                            });
                                                        });
                                                    });
                                                })->get();

                    $jumlah_indikator_sub_kegiatan = SubKegiatanIndikatorKinerja::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($data){
                                                    $q->where('opd_id', $data->id);
                                                })->whereHas('sub_kegiatan', function($q) use ($data){
                                                    $q->whereHas('kegiatan', function($q) use ($data){
                                                        $q->whereHas('program', function($q) use ($data){
                                                            $q->whereHas('program_rpjmd', function($q) use ($data){
                                                                $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                                                    $q->whereHas('sasaran_pd', function($q) use ($data){
                                                                        $q->where('opd_id', $data->id);
                                                                    });
                                                                });
                                                            });
                                                        });
                                                    });
                                                })->count();
                    $countTargetSubKegiatan = 0;
                    foreach ($getIndikatorSubKegiatans as $getIndikatorSubKegiatan) {
                        $cekOpdSubKegiatanIndikatorKinerja = OpdSubKegiatanIndikatorKinerja::where('opd_id', $data->id)->where('sub_kegiatan_indikator_kinerja_id', $getIndikatorSubKegiatan->id)->first();
                        if($cekOpdSubKegiatanIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekSubKegiatanTarget = SubKegiatanTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_sub_kegiatan_indikator_kinerja_id', $cekOpdSubKegiatanIndikatorKinerja->id)->first();
                                if($cekSubKegiatanTarget)
                                {
                                    $countTargetSubKegiatan++;
                                }
                            }
                        }
                    }

                    $countRealisasiSubKegiatan = 0;
                    foreach ($getIndikatorSubKegiatans as $getIndikatorSubKegiatan) {
                        $cekOpdSubKegiatanIndikatorKinerja = OpdSubKegiatanIndikatorKinerja::where('opd_id', $data->id)->where('sub_kegiatan_indikator_kinerja_id', $getIndikatorSubKegiatan->id)->first();
                        if($cekOpdSubKegiatanIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekSubKegiatanTarget = SubKegiatanTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_sub_kegiatan_indikator_kinerja_id', $cekOpdSubKegiatanIndikatorKinerja->id)->first();
                                if($cekSubKegiatanTarget)
                                {
                                    foreach ($tws as $tw) {
                                        $cekSubKegiatanRealisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cekSubKegiatanTarget->id)
                                                                ->where('tw_id', $tw->id)
                                                                ->first();
                                        if($cekSubKegiatanRealisasi)
                                        {
                                            $countRealisasiSubKegiatan++;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if($jumlah_indikator_sub_kegiatan)
                    {
                        $target_sub_kegiatan = ($countTargetSubKegiatan / ($jumlah_indikator_sub_kegiatan * count($tahuns))) * 100;
                        $realisasi_sub_kegiatan = (($countRealisasiSubKegiatan/4) / ($jumlah_indikator_sub_kegiatan * count($tahuns))) * 100;
                    } else {
                        $target_sub_kegiatan = 0;
                        $realisasi_sub_kegiatan = 0;
                    }
                // Sub Kegiatan End

                return[
                    'id' => Crypt::encryptString($data->id),
                    'nama' => $data->nama,
                    'jumlah_indikator_tujuan_pd' => $jumlah_indikator_tujuan_pd,
                    'target_tujuan_pd' => number_format($target_tujuan_pd, 2),
                    'realisasi_tujuan_pd' => number_format($realisasi_tujuan_pd, 2),
                    'jumlah_indikator_sasaran_pd' => $jumlah_indikator_sasaran_pd,
                    'target_sasaran_pd' => number_format($target_sasaran_pd, 2),
                    'realisasi_sasaran_pd' => number_format($realisasi_sasaran_pd, 2),
                    'jumlah_indikator_program' => $jumlah_indikator_program,
                    'target_program' => number_format($target_program, 2),
                    'realisasi_program' => number_format($realisasi_program, 2),
                    'jumlah_indikator_kegiatan' => $jumlah_indikator_kegiatan,
                    'target_kegiatan' => number_format($target_kegiatan, 2),
                    'realisasi_kegiatan' => number_format($realisasi_kegiatan, 2),
                    'jumlah_indikator_sub_kegiatan' => $jumlah_indikator_sub_kegiatan,
                    'target_sub_kegiatan' => number_format($target_sub_kegiatan, 2),
                    'realisasi_sub_kegiatan' => number_format($realisasi_sub_kegiatan, 2),
                ];
            });
        $opdsTransformAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $opdsTransform,
            $opds->total(),
            $opds->perPage(),
            $opds->currentPage(), [
                'path' => \Request::url(),
                'query' => [
                    'page' => $opds->currentPage()
                ]
            ]
        );

        return view('admin.dashboard.index', [
            'getTahunPeriode' => $getTahunPeriode,
            'countUrusan' => $countUrusan,
            'countProgram' => $countProgram,
            'countKegiatan' => $countKegiatan,
            'countSubKegiatan' => $countSubKegiatan,
            'countOpd' => $countOpd,
            'countTujuan' => $countTujuan,
            'countSasaran' => $countSasaran,
            'targetAnggaran' => $targetAnggaran,
            'targetAnggaranPerubahan' => $targetAnggaranPerubahan,
            'targetRealisasi' => $targetRealisasi,
            'opds' => $opdsTransformAndPaginated
        ]);
    }

    public function opd_get_data_search(Request $request)
    {
        $getTahunPeriode = TahunPeriode::where('status', 'Aktif')->first();
        $countUrusan = Urusan::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countProgram = Program::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countKegiatan = Kegiatan::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countSubKegiatan = SubKegiatan::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countOpd = MasterOpd::count();
        $countTujuan = Tujuan::count();
        $countSasaran = Sasaran::count();
        $targetAnggaran = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
            $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                    $q->where('tahun_periode_id', $getTahunPeriode->id);
                });
            });
        })->sum('target_anggaran_awal');

        $targetAnggaranPerubahan = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
            $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                    $q->where('tahun_periode_id', $getTahunPeriode->id);
                });
            });
        })->sum('target_anggaran_perubahan');


        $targetRealisasi = SubKegiatanTwRealisasi::whereHas('sub_kegiatan_target_satuan_rp_realisasi', function($q) use ($getTahunPeriode){
            $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                    $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                        $q->where('tahun_periode_id', $getTahunPeriode->id);
                    });
                });
            });
        })->sum('realisasi_rp');

        $opds = new MasterOpd;
        if($request->q)
        {
            $opds = $opds->where('nama', 'like', '%'.$request->q.'%');
        }
        $opds = $opds->paginate(10);
        $tws = MasterTw::select('id')->get();

        $opdsTransform = $opds
            ->getCollection()
            ->map(function($data) use ($getTahunPeriode, $tws){

                $tahun_awal = $getTahunPeriode->tahun_awal;
                $jarak_tahun = $getTahunPeriode->tahun_akhir - $tahun_awal;
                $tahuns = [];
                for ($i=0; $i < $jarak_tahun + 1; $i++) {
                    $tahuns[] = $tahun_awal + $i;
                }
                // Tujuan Pd Start
                    $getIndikatorTujuanPds = TujuanPdIndikatorKinerja::whereHas('tujuan_pd', function($q) use($data){
                        $q->where('opd_id', $data->id);
                    })->get();
                    $jumlah_indikator_tujuan_pd = TujuanPdIndikatorKinerja::whereHas('tujuan_pd', function($q) use($data){
                        $q->where('opd_id', $data->id);
                    })->count();

                    $countTargetTujuanPd = 0;
                    foreach ($getIndikatorTujuanPds as $getIndikatorTujuanPd) {
                        foreach ($tahuns as $tahun) {
                            $cekTujuanPdTarget = TujuanPdTargetSatuanRpRealisasi::where('tahun', $tahun)->whereHas('tujuan_pd_indikator_kinerja', function($q) use ($data, $getIndikatorTujuanPd){
                                $q->where('id', $getIndikatorTujuanPd->id);
                                $q->whereHas('tujuan_pd', function($q) use ($data){
                                    $q->where('opd_id', $data->id);
                                });
                            })->first();
                            if($cekTujuanPdTarget)
                            {
                                $countTargetTujuanPd++;
                            }
                        }
                    }

                    $countRealisasiTujuanPd = 0;
                    foreach ($getIndikatorTujuanPds as $getIndikatorTujuanPd) {
                        foreach ($tahuns as $tahun) {
                            $cekTujuanPdTarget = TujuanPdTargetSatuanRpRealisasi::where('tahun', $tahun)->whereHas('tujuan_pd_indikator_kinerja', function($q) use ($data, $getIndikatorTujuanPd){
                                $q->where('id', $getIndikatorTujuanPd->id);
                                $q->whereHas('tujuan_pd', function($q) use ($data){
                                    $q->where('opd_id', $data->id);
                                });
                            })->first();
                            if($cekTujuanPdTarget)
                            {
                                $cekRealisasiTujuanPd = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cekTujuanPdTarget->id)->first();
                                if($cekRealisasiTujuanPd)
                                {
                                    $countRealisasiTujuanPd++;
                                }
                            }
                        }
                    }

                    if($jumlah_indikator_tujuan_pd)
                    {
                        $target_tujuan_pd = ($countTargetTujuanPd / ($jumlah_indikator_tujuan_pd * count($tahuns))) * 100;
                        $realisasi_tujuan_pd = ($countRealisasiTujuanPd / ($jumlah_indikator_tujuan_pd * count($tahuns))) * 100;
                    } else {
                        $target_tujuan_pd = 0;
                        $realisasi_tujuan_pd = 0;
                    }
                // Tujuan Pd End

                // Sasaran Pd Start
                    $getIndikatorSasaranPds = SasaranPdIndikatorKinerja::whereHas('sasaran_pd', function($q) use($data){
                        $q->where('opd_id', $data->id);
                    })->get();
                    $jumlah_indikator_sasaran_pd = SasaranPdIndikatorKinerja::whereHas('sasaran_pd', function($q) use($data){
                        $q->where('opd_id', $data->id);
                    })->count();

                    $countTargetSasaranPd = 0;
                    foreach ($getIndikatorSasaranPds as $getIndikatorSasaranPd) {
                        foreach ($tahuns as $tahun) {
                            $cekSasaranPdTarget = SasaranPdTargetSatuanRpRealisasi::where('tahun', $tahun)->whereHas('sasaran_pd_indikator_kinerja', function($q) use ($data, $getIndikatorSasaranPd){
                                $q->where('id', $getIndikatorSasaranPd->id);
                                $q->whereHas('sasaran_pd', function($q) use ($data){
                                    $q->where('opd_id', $data->id);
                                });
                            })->first();
                            if($cekSasaranPdTarget)
                            {
                                $countTargetSasaranPd++;
                            }
                        }
                    }

                    $countRealisasiSasaranPd = 0;
                    foreach ($getIndikatorSasaranPds as $getIndikatorSasaranPd) {
                        foreach ($tahuns as $tahun) {
                            $cekSasaranPdTarget = SasaranPdTargetSatuanRpRealisasi::where('tahun', $tahun)->whereHas('sasaran_pd_indikator_kinerja', function($q) use ($data, $getIndikatorSasaranPd){
                                $q->where('id', $getIndikatorSasaranPd->id);
                                $q->whereHas('sasaran_pd', function($q) use ($data){
                                    $q->where('opd_id', $data->id);
                                });
                            })->first();
                            if($cekSasaranPdTarget)
                            {
                                $cekRealisasiSasaranPd = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cekSasaranPdTarget->id)->first();
                                if($cekRealisasiSasaranPd)
                                {
                                    $countRealisasiSasaranPd++;
                                }
                            }
                        }
                    }

                    if($jumlah_indikator_sasaran_pd)
                    {
                        $target_sasaran_pd = ($countTargetSasaranPd / ($jumlah_indikator_sasaran_pd * count($tahuns))) * 100;
                        $realisasi_sasaran_pd = ($countRealisasiSasaranPd / ($jumlah_indikator_sasaran_pd * count($tahuns))) * 100;
                    } else {
                        $target_sasaran_pd = 0;
                        $realisasi_sasaran_pd = 0;
                    }
                // Sasaran Pd End

                // Program Start
                    $getIndikatorPrograms = ProgramIndikatorKinerja::whereHas('opd_program_indikator_kinerja',function($q) use ($data){
                                                $q->where('opd_id', $data->id);
                                            })->whereHas('program', function($q) use ($data) {
                                                $q->whereHas('program_rpjmd', function($q) use ($data){
                                                    $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                                        $q->whereHas('sasaran_pd', function($q) use ($data){
                                                            $q->where('opd_id', $data->id);
                                                        });
                                                    });
                                                });
                                            })->get();
                    $jumlah_indikator_program = ProgramIndikatorKinerja::whereHas('opd_program_indikator_kinerja',function($q) use ($data){
                                                        $q->where('opd_id', $data->id);
                                                    })->whereHas('program', function($q) use ($data) {
                                                        $q->whereHas('program_rpjmd', function($q) use ($data){
                                                            $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                                                $q->whereHas('sasaran_pd', function($q) use ($data){
                                                                    $q->where('opd_id', $data->id);
                                                                });
                                                            });
                                                        });
                                                    })->count();

                    $countTargetProgram = 0;
                    foreach ($getIndikatorPrograms as $getIndikatorProgram) {
                        $cekOpdProgramIndikatorKinerja = OpdProgramIndikatorKinerja::where('opd_id', $data->id)->where('program_indikator_kinerja_id', $getIndikatorProgram->id)->first();
                        if($cekOpdProgramIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekProgramTarget = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_program_indikator_kinerja_id', $cekOpdProgramIndikatorKinerja->id)->first();
                                if($cekProgramTarget)
                                {
                                    $countTargetProgram++;
                                }
                            }
                        }
                    }

                    $countRealisasiProgram = 0;
                    foreach ($getIndikatorPrograms as $getIndikatorProgram) {
                        $cekOpdProgramIndikatorKinerja = OpdProgramIndikatorKinerja::where('opd_id', $data->id)->where('program_indikator_kinerja_id', $getIndikatorProgram->id)->first();
                        if($cekOpdProgramIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekProgramTarget = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_program_indikator_kinerja_id', $cekOpdProgramIndikatorKinerja->id)->first();
                                if($cekProgramTarget)
                                {
                                    foreach ($tws as $tw) {
                                        $cekProgramRealisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cekProgramTarget->id)
                                                                ->where('tw_id', $tw->id)
                                                                ->first();
                                        if($cekProgramRealisasi)
                                        {
                                            $countRealisasiProgram++;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if($jumlah_indikator_program)
                    {
                        $target_program = ($countTargetProgram / ($jumlah_indikator_program * count($tahuns))) * 100;
                        $realisasi_program = (($countRealisasiProgram/4) / ($jumlah_indikator_program * count($tahuns))) * 100;
                    } else {
                        $target_program = 0;
                        $realisasi_program = 0;
                    }
                // Program End

                // Kegiatan Start
                    $getIndikatorKegiatans = KegiatanIndikatorKinerja::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($data){
                        $q->where('opd_id', $data->id);
                    })->whereHas('kegiatan', function($q) use ($data){
                        $q->whereHas('program', function($q) use ($data){
                            $q->whereHas('program_rpjmd', function($q) use ($data){
                                $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                    $q->whereHas('sasaran_pd', function($q) use ($data){
                                        $q->where('opd_id', $data->id);
                                    });
                                });
                            });
                        });
                    })->get();

                    $jumlah_indikator_kegiatan = KegiatanIndikatorKinerja::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($data){
                                                    $q->where('opd_id', $data->id);
                                                })->whereHas('kegiatan', function($q) use ($data){
                                                    $q->whereHas('program', function($q) use ($data){
                                                        $q->whereHas('program_rpjmd', function($q) use ($data){
                                                            $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                                                $q->whereHas('sasaran_pd', function($q) use ($data){
                                                                    $q->where('opd_id', $data->id);
                                                                });
                                                            });
                                                        });
                                                    });
                                                })->count();
                    $countTargetKegiatan = 0;
                    foreach ($getIndikatorKegiatans as $getIndikatorKegiatan) {
                        $cekOpdKegiatanIndikatorKinerja = OpdKegiatanIndikatorKinerja::where('opd_id', $data->id)->where('kegiatan_indikator_kinerja_id', $getIndikatorKegiatan->id)->first();
                        if($cekOpdKegiatanIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekKegiatanTarget = KegiatanTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_kegiatan_indikator_kinerja_id', $cekOpdKegiatanIndikatorKinerja->id)->first();
                                if($cekKegiatanTarget)
                                {
                                    $countTargetKegiatan++;
                                }
                            }
                        }
                    }

                    $countRealisasiKegiatan = 0;
                    foreach ($getIndikatorKegiatans as $getIndikatorKegiatan) {
                        $cekOpdKegiatanIndikatorKinerja = OpdKegiatanIndikatorKinerja::where('opd_id', $data->id)->where('kegiatan_indikator_kinerja_id', $getIndikatorKegiatan->id)->first();
                        if($cekOpdKegiatanIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekKegiatanTarget = KegiatanTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_kegiatan_indikator_kinerja_id', $cekOpdKegiatanIndikatorKinerja->id)->first();
                                if($cekKegiatanTarget)
                                {
                                    foreach ($tws as $tw) {
                                        $cekKegiatanRealisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cekKegiatanTarget->id)
                                                                ->where('tw_id', $tw->id)
                                                                ->first();
                                        if($cekKegiatanRealisasi)
                                        {
                                            $countRealisasiKegiatan++;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if($jumlah_indikator_kegiatan)
                    {
                        $target_kegiatan = ($countTargetKegiatan / ($jumlah_indikator_kegiatan * count($tahuns))) * 100;
                        $realisasi_kegiatan = (($countRealisasiKegiatan/4) / ($jumlah_indikator_kegiatan * count($tahuns))) * 100;
                    } else {
                        $target_kegiatan = 0;
                        $realisasi_kegiatan = 0;
                    }
                // Kegiatan End

                // Sub Kegiatan Start
                    $getIndikatorSubKegiatans = SubKegiatanIndikatorKinerja::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($data){
                                                    $q->where('opd_id', $data->id);
                                                })->whereHas('sub_kegiatan', function($q) use ($data){
                                                    $q->whereHas('kegiatan', function($q) use ($data){
                                                        $q->whereHas('program', function($q) use ($data){
                                                            $q->whereHas('program_rpjmd', function($q) use ($data){
                                                                $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                                                    $q->whereHas('sasaran_pd', function($q) use ($data){
                                                                        $q->where('opd_id', $data->id);
                                                                    });
                                                                });
                                                            });
                                                        });
                                                    });
                                                })->get();

                    $jumlah_indikator_sub_kegiatan = SubKegiatanIndikatorKinerja::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($data){
                                                    $q->where('opd_id', $data->id);
                                                })->whereHas('sub_kegiatan', function($q) use ($data){
                                                    $q->whereHas('kegiatan', function($q) use ($data){
                                                        $q->whereHas('program', function($q) use ($data){
                                                            $q->whereHas('program_rpjmd', function($q) use ($data){
                                                                $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($data){
                                                                    $q->whereHas('sasaran_pd', function($q) use ($data){
                                                                        $q->where('opd_id', $data->id);
                                                                    });
                                                                });
                                                            });
                                                        });
                                                    });
                                                })->count();
                    $countTargetSubKegiatan = 0;
                    foreach ($getIndikatorSubKegiatans as $getIndikatorSubKegiatan) {
                        $cekOpdSubKegiatanIndikatorKinerja = OpdSubKegiatanIndikatorKinerja::where('opd_id', $data->id)->where('sub_kegiatan_indikator_kinerja_id', $getIndikatorSubKegiatan->id)->first();
                        if($cekOpdSubKegiatanIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekSubKegiatanTarget = SubKegiatanTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_sub_kegiatan_indikator_kinerja_id', $cekOpdSubKegiatanIndikatorKinerja->id)->first();
                                if($cekSubKegiatanTarget)
                                {
                                    $countTargetSubKegiatan++;
                                }
                            }
                        }
                    }

                    $countRealisasiSubKegiatan = 0;
                    foreach ($getIndikatorSubKegiatans as $getIndikatorSubKegiatan) {
                        $cekOpdSubKegiatanIndikatorKinerja = OpdSubKegiatanIndikatorKinerja::where('opd_id', $data->id)->where('sub_kegiatan_indikator_kinerja_id', $getIndikatorSubKegiatan->id)->first();
                        if($cekOpdSubKegiatanIndikatorKinerja)
                        {
                            foreach ($tahuns as $tahun) {
                                $cekSubKegiatanTarget = SubKegiatanTargetSatuanRpRealisasi::where('tahun', $tahun)->where('opd_sub_kegiatan_indikator_kinerja_id', $cekOpdSubKegiatanIndikatorKinerja->id)->first();
                                if($cekSubKegiatanTarget)
                                {
                                    foreach ($tws as $tw) {
                                        $cekSubKegiatanRealisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cekSubKegiatanTarget->id)
                                                                ->where('tw_id', $tw->id)
                                                                ->first();
                                        if($cekSubKegiatanRealisasi)
                                        {
                                            $countRealisasiSubKegiatan++;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if($jumlah_indikator_sub_kegiatan)
                    {
                        $target_sub_kegiatan = ($countTargetSubKegiatan / ($jumlah_indikator_sub_kegiatan * count($tahuns))) * 100;
                        $realisasi_sub_kegiatan = (($countRealisasiSubKegiatan/4) / ($jumlah_indikator_sub_kegiatan * count($tahuns))) * 100;
                    } else {
                        $target_sub_kegiatan = 0;
                        $realisasi_sub_kegiatan = 0;
                    }
                // Sub Kegiatan End

                return[
                    'id' => Crypt::encryptString($data->id),
                    'nama' => $data->nama,
                    'jumlah_indikator_tujuan_pd' => $jumlah_indikator_tujuan_pd,
                    'target_tujuan_pd' => number_format($target_tujuan_pd, 2),
                    'realisasi_tujuan_pd' => number_format($realisasi_tujuan_pd, 2),
                    'jumlah_indikator_sasaran_pd' => $jumlah_indikator_sasaran_pd,
                    'target_sasaran_pd' => number_format($target_sasaran_pd, 2),
                    'realisasi_sasaran_pd' => number_format($realisasi_sasaran_pd, 2),
                    'jumlah_indikator_program' => $jumlah_indikator_program,
                    'target_program' => number_format($target_program, 2),
                    'realisasi_program' => number_format($realisasi_program, 2),
                    'jumlah_indikator_kegiatan' => $jumlah_indikator_kegiatan,
                    'target_kegiatan' => number_format($target_kegiatan, 2),
                    'realisasi_kegiatan' => number_format($realisasi_kegiatan, 2),
                    'jumlah_indikator_sub_kegiatan' => $jumlah_indikator_sub_kegiatan,
                    'target_sub_kegiatan' => number_format($target_sub_kegiatan, 2),
                    'realisasi_sub_kegiatan' => number_format($realisasi_sub_kegiatan, 2),
                ];
            });
        $opdsTransformAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $opdsTransform,
            $opds->total(),
            $opds->perPage(),
            $opds->currentPage(), [
                'path' => \Request::url(),
                'query' => [
                    'page' => $opds->currentPage()
                ]
            ]
        );

        return view('admin.dashboard.index', [
            'getTahunPeriode' => $getTahunPeriode,
            'countUrusan' => $countUrusan,
            'countProgram' => $countProgram,
            'countKegiatan' => $countKegiatan,
            'countSubKegiatan' => $countSubKegiatan,
            'countOpd' => $countOpd,
            'countTujuan' => $countTujuan,
            'countSasaran' => $countSasaran,
            'targetAnggaran' => $targetAnggaran,
            'targetAnggaranPerubahan' => $targetAnggaranPerubahan,
            'targetRealisasi' => $targetRealisasi,
            'opds' => $opdsTransformAndPaginated
        ]);
    }

    public function change(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user->color_layout = $request->color_layout;
        $user->nav_color = $request->nav_color;
        $user->behaviour = $request->behaviour;
        $user->layout = $request->layout;
        $user->radius = $request->radius;
        $user->placement = $request->placement;
        $user->save();
    }

    public function normalisasi_opd()
    {
        $sekretariat_daerah = [
            'Biro Administrasi Pimpinan',
            'Biro Perekonomian dan Administrasi Pembangunan',
            'Biro Pemerintahan dan Kesejahteraan Rakyat',
            'Biro Hukum',
            'Biro Organisasi dan Reformasi Birokrasi',
            'Biro Umum',
            'Biro Pengadaan Barang / Jasa'
        ];

        for ($i=0; $i < count($sekretariat_daerah); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 2;
            $master_opd->nama = $sekretariat_daerah[$i];
            $master_opd->save();
        }

        $dinas_daerah = [
            'Dinas Perpustakaan dan Kearsipan',
            'Dinas Pemberdayaan Perempuan, Perlindungan Anak, Kependudukan dan Keluarga Berencana',
            'Dinas Lingkungan Hidup dan Kehutanan',
            'Dinas Ketahanan Pangan',
            'Dinas Perumahan Rakyat dan Kawasan Permukiman',
            'Dinas Pekerjaan Umum dan Penataan Ruang',
            'Dinas Kesehatan',
            'Dinas Pendidikan dan Kebudayaan',
            'Dinas Kepemudaan dan Olah Raga',
            'Dinas Pertanian',
            'Dinas Kelautan dan Perikanan',
            'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu',
            'Dinas Pemberdayaan Masyarakat dan Desa',
            'Dinas Komunikasi, Informatika, Statistik dan Persandian',
            'Dinas Perhubungan',
            'Dinas Energi dan Sumberdaya Mineral',
            'Dinas Perindustrian dan Perdagangan',
            'Dinas Pariwisata',
            'Dinas Sosial',
            'Dinas Tenaga Kerja dan Transmigrasi',
            'Dinas Koperasi, Usaha Kecil dan Menengah'
        ];

        for ($i=0; $i < count($dinas_daerah); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 3;
            $master_opd->nama = $dinas_daerah[$i];
            $master_opd->save();
        }

        $sekretariat_dprd = [
            'Sekretariat Dewan Perwakilan Rakyat Daerah (DPRD) Provinsi Banten'
        ];

        for ($i=0; $i < count($sekretariat_dprd); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 4;
            $master_opd->nama = $sekretariat_dprd [$i];
            $master_opd->save();
        }

        $lembaga_teknis_daerah = [
            'Inspektorat',
            'Badan Pengelolaan Keuangan dan Aset Daerah',
            'Badan Kepegawaian Daerah',
            'Badan Pengembangan Sumber Daya Manusia',
            'Badan Kesatuan Bangsa dan Politik',
            'Badan Pendapatan Daerah',
            'Badan Perencanaan Pembangunan Daerah',
            'Badan Penanggulangan Bencana Daerah',
            'Badan Penghubung'
        ];

        for ($i=0; $i < count($lembaga_teknis_daerah); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 5;
            $master_opd->nama = $lembaga_teknis_daerah [$i];
            $master_opd->save();
        }

        $satuan_polisi_pamong_praja = [
            'Satuan Polisi Pamong Praja (SATPOLPP)'
        ];

        for ($i=0; $i < count($satuan_polisi_pamong_praja); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 6;
            $master_opd->nama = $satuan_polisi_pamong_praja[$i];
            $master_opd->save();
        }

        $lembaga_lainnya = [
            'Rumah Sakit Umum Daerah'
        ];

        for ($i=0; $i < count($lembaga_lainnya); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 7;
            $master_opd->nama = $lembaga_lainnya[$i];
            $master_opd->save();
        }

        return 'berhasil';
    }

    public function grafik_tujuan_pd()
    {
        $opds = MasterOpd::all();

        $jumlah = [];
        foreach ($opds as $opd) {
            $jumlah[] = TujuanPd::where('opd_id', $opd->id)->count();
        }

        $nama = [];
        foreach ($opds as $opd) {
            $nama[] = $opd->nama;
        }

        return response()->json([
            'jumlah' => $jumlah,
            'nama' => $nama
        ]);
    }

    public function grafik_sasaran_pd()
    {
        $opds = MasterOpd::all();

        $jumlah = [];
        foreach ($opds as $opd) {
            $jumlah[] = SasaranPd::where('opd_id', $opd->id)->count();
        }

        $nama = [];
        foreach ($opds as $opd) {
            $nama[] = $opd->nama;
        }

        return response()->json([
            'jumlah' => $jumlah,
            'nama' => $nama
        ]);
    }

    public function grafik_tujuan_pd_bar()
    {
        $opds = MasterOpd::all();

        $jumlah = [];
        foreach ($opds as $opd) {
            $jumlah[] = TujuanPd::where('opd_id', $opd->id)->count();
        }

        $nama = [];
        foreach ($opds as $opd) {
            $nama[] = $opd->nama;
        }

        return response()->json([
            'jumlah' => $jumlah,
            'nama' => $nama
        ]);
    }

    public function grafik_sasaran_pd_bar()
    {
        $opds = MasterOpd::all();

        $jumlah = [];
        foreach ($opds as $opd) {
            $jumlah[] = SasaranPd::where('opd_id', $opd->id)->count();
        }

        $nama = [];
        foreach ($opds as $opd) {
            $nama[] = $opd->nama;
        }

        return response()->json([
            'jumlah' => $jumlah,
            'nama' => $nama
        ]);
    }

    public function grafik_program()
    {
        if(request()->ajax())
        {
            $opds = MasterOpd::pluck('nama', 'id');
            foreach ($opds as $id => $nama) {
                $harapan = Program::whereHas('program_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                });
                            })->count();
                $data_program[] = [
                    'x' => $nama,
                    'y' => Program::whereHas('program_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                    $q->whereHas('program_target_satuan_rp_realisasi', function($q) {
                                        $q->whereHas('program_tw_realisasi');
                                    });
                                });
                            })->count(),
                    'goals' => [[
                        'name' => 'Rencana',
                        'value' => $harapan,
                        'strokeWidth' => 5,
                        'strokeColor' => '#775DD0'
                    ]]
                ];
            }

            return response()->json([
                'data_program' => $data_program
            ]);
        }
    }

    public function grafik_kegiatan()
    {
        if(request()->ajax())
        {
            $opds = MasterOpd::pluck('nama', 'id');
            foreach ($opds as $id => $nama) {
                $harapan = Kegiatan::whereHas('kegiatan_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                });
                            })->count();
                $data_kegiatan[] = [
                    'x' => $nama,
                    'y' => Kegiatan::whereHas('kegiatan_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                    $q->whereHas('kegiatan_target_satuan_rp_realisasi', function($q) {
                                        $q->whereHas('kegiatan_tw_realisasi');
                                    });
                                });
                            })->count(),
                    'goals' => [[
                        'name' => 'Rencana',
                        'value' => $harapan,
                        'strokeWidth' => 5,
                        'strokeColor' => '#775DD0'
                    ]]
                ];
            }

            return response()->json([
                'data_kegiatan' => $data_kegiatan
            ]);
        }
    }

    public function grafik_sub_kegiatan()
    {
        if(request()->ajax())
        {
            $opds = MasterOpd::pluck('nama', 'id');
            foreach ($opds as $id => $nama) {
                $harapan = SubKegiatan::whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                });
                            })->count();
                $data_sub_kegiatan[] = [
                    'x' => $nama,
                    'y' => SubKegiatan::whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                    $q->whereHas('sub_kegiatan_target_satuan_rp_realisasi', function($q) {
                                        $q->whereHas('sub_kegiatan_tw_realisasi');
                                    });
                                });
                            })->count(),
                    'goals' => [[
                        'name' => 'Rencana',
                        'value' => $harapan,
                        'strokeWidth' => 5,
                        'strokeColor' => '#775DD0'
                    ]]
                ];
            }

            return response()->json([
                'data_sub_kegiatan' => $data_sub_kegiatan
            ]);
        }
    }

    public function normalisasi_sasaran_program_realisasi($opd_id)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tws = MasterTw::all();

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($opd_id){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd_id){
                $q->whereHas('program_rpjmd', function($q) use ($opd_id){
                    $q->whereHas('program', function($q) use ($opd_id){
                        $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                            $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                $q->where('opd_id', $opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $a = 1;
        foreach ($get_sasarans as $get_sasaran) {
            $get_programs = Program::whereHas('program_rpjmd', function($q) use ($get_sasaran){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($get_sasaran) {
                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($get_sasaran){
                        $q->whereHas('sasaran', function($q) use ($get_sasaran) {
                            $q->where('id', $get_sasaran->id);
                        });
                    });
                });
            })->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                    $q->where('opd_id', $opd_id);
                });
            })->get();

            foreach ($get_programs as $get_program) {
                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $get_program->id)
                ->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                    $q->where('opd_id', $opd_id);
                })->get();

                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                    $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                    ->where('opd_id', $opd_id)
                                                    ->get();
                    foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                        foreach ($tahuns as $tahun)
                        {
                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                        ->where('tahun', $tahun)
                                                                                                        ->first();
                            if($cek_program_target_satuan_rp_realisasi)
                            {
                                foreach ($tws as $tw)
                                {
                                    $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                    if($cek_program_tw_realisasi_renja)
                                    {
                                        if (!$cek_program_tw_realisasi_renja->sasaran_id) {
                                            $updateProgram = ProgramTwRealisasi::find($cek_program_tw_realisasi_renja->id);
                                            $updateProgram->sasaran_id = $get_sasaran->id;
                                            $updateProgram->save();
                                            $a++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $a;
    }

    public function normalisasi_sasaran_kegiatan_realisasi($opd_id)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tws = MasterTw::all();

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($opd_id){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd_id){
                $q->whereHas('program_rpjmd', function($q) use ($opd_id){
                    $q->whereHas('program', function($q) use ($opd_id){
                        $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                            $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                $q->where('opd_id', $opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $a = 1;
        foreach ($get_sasarans as $get_sasaran) {
            $get_programs = Program::whereHas('program_rpjmd', function($q) use ($get_sasaran){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($get_sasaran) {
                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($get_sasaran){
                        $q->whereHas('sasaran', function($q) use ($get_sasaran) {
                            $q->where('id', $get_sasaran->id);
                        });
                    });
                });
            })->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                    $q->where('opd_id', $opd_id);
                });
            })->get();

            foreach ($get_programs as $get_program) {
                $get_kegiatans = Kegiatan::where('program_id', $get_program->id)
                                ->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                        $q->where('opd_id', $opd_id);
                                    });
                                })->get();
                foreach ($get_kegiatans as $get_kegiatan) {
                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $get_kegiatan->id)
                                                        ->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                            $q->where('opd_id', $opd_id);
                                                        })->get();
                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                        foreach ($tahuns as $tahun)
                        {
                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                $q->where('opd_id', $opd_id);
                                $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
                            })->where('tahun', $tahun)->first();

                            if($cek_kegiatan_target_satuan_rp_realisasi)
                            {
                                foreach ($tws as $tw) {
                                    $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                        ->where('tw_id', $tw->id)->first();
                                    if($cek_kegiatan_tw_realisasi_renja)
                                    {
                                        if(!$cek_kegiatan_tw_realisasi_renja->sasaran_id && !$cek_kegiatan_tw_realisasi_renja->program_id)
                                        {
                                            $updateKegiatan = KegiatanTwRealisasi::find($cek_kegiatan_tw_realisasi_renja->id);
                                            $updateKegiatan->sasaran_id = $get_sasaran->id;
                                            $updateKegiatan->program_id = $get_program->id;
                                            $updateKegiatan->save();
                                            $a++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }
        }
        return $a;
    }

    public function normalisasi_sasaran_subkegiatan_realisasi($opd_id)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tws = MasterTw::all();

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($opd_id){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd_id){
                $q->whereHas('program_rpjmd', function($q) use ($opd_id){
                    $q->whereHas('program', function($q) use ($opd_id){
                        $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                            $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                $q->where('opd_id', $opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $a = 1;
        foreach ($get_sasarans as $get_sasaran) {
            $get_programs = Program::whereHas('program_rpjmd', function($q) use ($get_sasaran){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($get_sasaran) {
                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($get_sasaran){
                        $q->whereHas('sasaran', function($q) use ($get_sasaran) {
                            $q->where('id', $get_sasaran->id);
                        });
                    });
                });
            })->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                    $q->where('opd_id', $opd_id);
                });
            })->get();

            foreach ($get_programs as $get_program) {
                $get_kegiatans = Kegiatan::where('program_id', $get_program->id)
                                ->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                        $q->where('opd_id', $opd_id);
                                    });
                                })->get();
                foreach ($get_kegiatans as $get_kegiatan) {
                    $get_sub_kegiatans = SubKegiatan::whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                        $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                            $q->where('opd_id', $opd_id);
                        });
                    })->where('kegiatan_id', $get_kegiatan->id)->get();

                    foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                        $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                            $q->where('opd_id', $opd_id);
                        })->where('sub_kegiatan_id', $get_sub_kegiatan->id)->get();
                        foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                            foreach($tahuns as $tahun)
                            {
                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                    $q->where('opd_id', $opd_id);
                                    $q->where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id);
                                })->where('tahun', $tahun)->first();
                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                {
                                    foreach($tws as $tw)
                                    {
                                        $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                    ->where('tw_id', $tw->id)->first();
                                        if($cek_sub_kegiatan_tw_realisasi_renja)
                                        {
                                            if(!$cek_sub_kegiatan_tw_realisasi_renja->sasaran_id && !$cek_sub_kegiatan_tw_realisasi_renja->program_id && !$cek_sub_kegiatan_tw_realisasi_renja->kegiatan_id)
                                            {
                                                $updateSubKegiatan = SubKegiatanTwRealisasi::find($cek_sub_kegiatan_tw_realisasi_renja->id);
                                                $updateSubKegiatan->sasaran_id = $get_sasaran->id;
                                                $updateSubKegiatan->program_id = $get_program->id;
                                                $updateSubKegiatan->kegiatan_id = $get_kegiatan->id;
                                                $updateSubKegiatan->save();
                                                $a++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }
        }
        return $a;
    }

    public function opd_get_data($opd_id)
    {
        $opd_id = Crypt::decryptString($opd_id);
        $opd = MasterOpd::find($opd_id);
        $getTahunPeriode = TahunPeriode::where('status', 'Aktif')->first();

        $tahun_awal = $getTahunPeriode->tahun_awal;
        $jarak_tahun = $getTahunPeriode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        return view('admin.dashboard.opd-get-data', [
            'opd' => $opd,
            'tahuns' => $tahuns,
            'tahun_awal' => $tahun_awal,
            'opd_id' => Crypt::encryptString($opd_id)
        ]);
    }

    public function opd_get_data_tahun($opd_id, $tahun)
    {
        $opd_id = Crypt::decryptString($opd_id);

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $tahun_akhir = $get_periode->tahun_akhir;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $html = '';
        $tws = MasterTw::all();
        $tahun = $tahun;
        $a = 1;

        $get_tujuan_pds = TujuanPd::where('opd_id', $opd_id)->get();

        $tujuan_pds = [];
        foreach ($get_tujuan_pds as $get_tujuan_pd) {
            $cek_perubahan_tujuan_pd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)
                                        ->where('tahun_perubahan', $tahun)
                                        ->latest()
                                        ->first();
            if($cek_perubahan_tujuan_pd)
            {
                $tujuan_pds[] = [
                    'id' => $cek_perubahan_tujuan_pd->tujuan_pd_id,
                    'kode' => $cek_perubahan_tujuan_pd->kode,
                    'deskripsi' => $cek_perubahan_tujuan_pd->deskripsi,
                ];
            } else {
                $tujuan_pds[] = [
                    'id' => $get_tujuan_pd->id,
                    'kode' => $get_tujuan_pd->kode,
                    'deskripsi' => $get_tujuan_pd->deskripsi,
                ];
            }
        }

        foreach ($tujuan_pds as $tujuan_pd) {
            $html .= '<tr>';
                $html .= '<td>tpd.'.$a.'</td>';
                $html .= '<td>'.$tujuan_pd['deskripsi'].'</td>';

                $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                $i_a = 1;
                foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                    if($i_a == 1)
                    {
                            // Tujuan PD Indikator Start;
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                $cek_target_tujuan_pd = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                if($cek_target_tujuan_pd)
                                {
                                    $html .= '<td class="bg-success">'.$cek_target_tujuan_pd->target.'</td>';
                                    $html .= '<td></td>';

                                    $cek_realisasi_tujuan_pd = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_target_tujuan_pd->id)->first();
                                    if($cek_realisasi_tujuan_pd)
                                    {
                                        $html .= '<td class="bg-success" colspan="8">'.$cek_realisasi_tujuan_pd->realisasi.'</td>';
                                    } else {
                                        $html .= '<td class="bg-dark" colspan="8"></td>';
                                    }
                                } else {
                                    $html .= '<td class="bg-dark"></td>';
                                    $html .= '<td></td>';
                                }
                            // Tujuan PD Indikator End;
                        $html .='</tr>';
                    } else {
                        $html .= '<tr>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            // Tujuan PD Indikator Start;
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                $cek_target_tujuan_pd = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                if($cek_target_tujuan_pd)
                                {
                                    $html .= '<td class="bg-success">'.$cek_target_tujuan_pd->target.'</td>';
                                    $html .= '<td></td>';

                                    $cek_realisasi_tujuan_pd = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_target_tujuan_pd->id)->first();
                                    if($cek_realisasi_tujuan_pd)
                                    {
                                        $html .= '<td class="bg-success" colspan="8">'.$cek_realisasi_tujuan_pd->realisasi.'</td>';
                                    } else {
                                        $html .= '<td class="bg-dark" colspan="8"></td>';
                                    }
                                } else {
                                    $html .= '<td class="bg-dark"></td>';
                                    $html .= '<td></td>';
                                }
                            // Tujuan PD Indikator End;
                        $html .='</tr>';
                    }
                    $i_a++;
                }

                $b = 1;

                $get_sasaran_pds = SasaranPd::where('opd_id', $opd_id)->get();
                $sasaran_pds = [];
                foreach ($get_sasaran_pds as $get_sasaran_pd) {
                    $cek_perubahan_sasaran_pd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)
                                        ->where('tahun_perubahan', $tahun)
                                        ->latest()
                                        ->first();
                    if($cek_perubahan_sasaran_pd)
                    {
                        $sasaran_pds[] = [
                            'id' => $cek_perubahan_sasaran_pd->sasaran_pd_id,
                            'kode' => $cek_perubahan_sasaran_pd->kode,
                            'deskripsi' => $cek_perubahan_sasaran_pd->deskripsi,
                            'sasaran_id' => $cek_perubahan_sasaran_pd->sasaran_id
                        ];
                    } else {
                        $sasaran_pds[] = [
                            'id' => $get_sasaran_pd->id,
                            'kode' => $get_sasaran_pd->kode,
                            'deskripsi' => $get_sasaran_pd->deskripsi,
                            'sasaran_id' => $get_sasaran_pd->sasaran_id
                        ];
                    }
                }
                foreach ($sasaran_pds as $sasaran_pd) {
                    $html .= '<tr>';
                        $html .= '<td>spd.'.$b.'</td>';
                        $html .= '<td>'.$sasaran_pd['deskripsi'].'</td>';

                        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                        $i_b = 1;
                        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                            if($i_b == 1)
                            {
                                    // Sasaran PD Indikator Start;
                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                        $cek_target_sasaran_pd = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                        if($cek_target_sasaran_pd)
                                        {
                                            $html .= '<td class="bg-success">'.$cek_target_sasaran_pd->target.'</td>';
                                            $html .= '<td></td>';

                                            $cek_realisasi_sasaran_pd = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_target_sasaran_pd->id)->first();
                                            if($cek_realisasi_sasaran_pd)
                                            {
                                                $html .= '<td class="bg-success" colspan="8">'.$cek_realisasi_sasaran_pd->realisasi.'</td>';
                                            } else {
                                                $html .= '<td class="bg-dark" colspan="8"></td>';
                                            }
                                        } else {
                                            $html .= '<td class="bg-dark"></td>';
                                            $html .= '<td></td>';
                                        }
                                    // Sasaran PD Indikator End;
                                $html .= '</tr>';
                            } else {
                                $html .= '<tr>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    // Sasaran PD Indikator Start;
                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                        $cek_target_sasaran_pd = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                        if($cek_target_sasaran_pd)
                                        {
                                            $html .= '<td class="bg-success">'.$cek_target_sasaran_pd->target.'</td>';
                                            $html .= '<td></td>';

                                            $cek_realisasi_sasaran_pd = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_target_sasaran_pd->id)->first();
                                            if($cek_realisasi_sasaran_pd)
                                            {
                                                $html .= '<td class="bg-success" colspan="8">'.$cek_realisasi_sasaran_pd->realisasi.'</td>';
                                            } else {
                                                $html .= '<td class="bg-dark" colspan="8"></td>';
                                            }
                                        } else {
                                            $html .= '<td class="bg-dark"></td>';
                                            $html .= '<td></td>';
                                        }
                                    // Sasaran PD Indikator End;
                                $html .= '</tr>';
                            }
                            $i_b++;
                        }

                    $get_programs = Program::whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                                        $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                            $q->where('opd_id', $opd_id);
                                        });
                                    })->whereHas('program_rpjmd', function($q) use ($sasaran_pd){
                                        $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($sasaran_pd){
                                            $q->where('sasaran_pd_id', $sasaran_pd['id']);
                                        });
                                    })->get();
                    $programs = [];
                    foreach ($get_programs as $get_program) {
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->where('tahun_perubahan', $tahun)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $programs[] = [
                                'id' => $cek_perubahan_program->program_id,
                                'kode' => $cek_perubahan_program->kode,
                                'deskripsi' => $cek_perubahan_program->deskripsi
                            ];
                        } else {
                            $programs[] = [
                                'id' => $get_program->id,
                                'kode' => $get_program->kode,
                                'deskripsi' => $get_program->deskripsi
                            ];
                        }
                    }
                    $c = 1;
                    foreach ($programs as $program) {
                        $html .= '<tr>';
                            $html .= '<td>p.'.$c.'</td>';
                            $html .= '<td>'.$program['deskripsi'].'</td>';

                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                                                $q->where('opd_id', $opd_id);
                                                            })->get();
                            $i_c = 1;
                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                if($i_c == 1)
                                {
                                        // Program Indikator Start;
                                            $html .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            $cek_target_program = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id, $program_indikator_kinerja){
                                                $q->where('opd_id', $opd_id);
                                                $q->where('program_indikator_kinerja_id', $program_indikator_kinerja->id);
                                            })->where('tahun', $tahun)->first();
                                            if($cek_target_program)
                                            {
                                                $html .= '<td class="bg-success">'.$cek_target_program->target.'</td>';
                                                $html .= '<td class="bg-success">Rp. '.number_format((int) $cek_target_program->target_rp, 2, ',', '.').'</td>';

                                                foreach ($tws as $tw) {
                                                    $cek_realisasi_program = ProgramTwRealisasi::where('sasaran_id', $sasaran_pd['sasaran_id'])
                                                                                ->where('program_target_satuan_rp_realisasi_id', $cek_target_program->id)
                                                                                ->where('tw_id', $tw->id)
                                                                                ->first();
                                                    if($cek_realisasi_program)
                                                    {
                                                        $html .= '<td class="bg-success">'.$cek_realisasi_program->realisasi.'</td>';
                                                        $html .= '<td class="bg-success">Rp. '.number_format((int)$cek_realisasi_program->realisasi_rp, 2, ',', '.').'</td>';
                                                    } else {
                                                        $html .= '<td class="bg-dark"></td>';
                                                        $html .= '<td class="bg-dark"></td>';
                                                    }
                                                }
                                            } else {
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                            }
                                        // Program Indikator End;
                                    $html .='</tr>';
                                } else {
                                    $html .= '<tr>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        // Sasaran PD Indikator Start;
                                            $html .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            $cek_target_program = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id, $program_indikator_kinerja){
                                                $q->where('opd_id', $opd_id);
                                                $q->where('program_indikator_kinerja_id', $program_indikator_kinerja->id);
                                            })->where('tahun', $tahun)->first();
                                            if($cek_target_program)
                                            {
                                                $html .= '<td class="bg-success">'.$cek_target_program->target.'</td>';
                                                $html .= '<td class="bg-success">Rp. '.number_format((int) $cek_target_program->target_rp, 2, ',', '.').'</td>';

                                                foreach ($tws as $tw) {
                                                    $cek_realisasi_program = ProgramTwRealisasi::where('sasaran_id', $sasaran_pd['sasaran_id'])
                                                                                ->where('program_target_satuan_rp_realisasi_id', $cek_target_program->id)
                                                                                ->where('tw_id', $tw->id)
                                                                                ->first();
                                                    if($cek_realisasi_program)
                                                    {
                                                        $html .= '<td class="bg-success">'.$cek_realisasi_program->realisasi.'</td>';
                                                        $html .= '<td class="bg-success">Rp. '.number_format((int)$cek_realisasi_program->realisasi_rp, 2, ',', '.').'</td>';
                                                    } else {
                                                        $html .= '<td class="bg-dark"></td>';
                                                        $html .= '<td class="bg-dark"></td>';
                                                    }
                                                }
                                            } else {
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                                $html .= '<td class="bg-dark"></td>';
                                            }
                                        // Program Indikator End;
                                    $html .='</tr>';
                                }
                                $i_c++;
                            }

                            $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                    $q->where('opd_id', $opd_id);
                                });
                            })->get();

                            $kegiatans = [];
                            foreach ($get_kegiatans as $get_kegiatan) {
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                                            ->where('tahun_perubahan', $tahun)
                                                            ->latest()
                                                            ->first();
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatans[] = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                                    ];
                                } else {
                                    $kegiatans[] = [
                                        'id' => $get_kegiatan->id,
                                        'kode' => $get_kegiatan->kode,
                                        'deskripsi' => $get_kegiatan->deskripsi
                                    ];
                                }
                            }
                            $d = 1;
                            foreach ($kegiatans as $kegiatan) {
                                $html .= '<tr>';
                                    $html .= '<td>k.'.$d.'</td>';
                                    $html .= '<td>'.$kegiatan['deskripsi'].'</td>';

                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                    ->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                        $q->where('opd_id', $opd_id);
                                    })->get();

                                    $i_d = 1;
                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                        if($i_d == 1)
                                        {
                                                // Kegiatan Indikator Start;
                                                $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                $cek_target_kegiatan = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id, $kegiatan_indikator_kinerja){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
                                                })->where('tahun', $tahun)->first();
                                                if($cek_target_kegiatan)
                                                {
                                                    $html .= '<td class="bg-success">'.$cek_target_kegiatan->target.'</td>';
                                                    $html .= '<td class="bg-success">Rp. '.number_format((int) $cek_target_kegiatan->target_rp, 2, ',', '.').'</td>';

                                                    foreach ($tws as $tw) {
                                                        $cek_realisasi_kegiatan = KegiatanTwRealisasi::where('sasaran_id', $sasaran_pd['sasaran_id'])
                                                                                    ->where('program_id', $program['id'])
                                                                                    ->where('kegiatan_target_satuan_rp_realisasi_id', $cek_target_kegiatan->id)
                                                                                    ->where('tw_id', $tw->id)
                                                                                    ->first();
                                                        if($cek_realisasi_kegiatan)
                                                        {
                                                            $html .= '<td class="bg-success">'.$cek_realisasi_kegiatan->realisasi.'</td>';
                                                            $html .= '<td class="bg-success">Rp. '.number_format((int)$cek_realisasi_kegiatan->realisasi_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $html .= '<td class="bg-dark"></td>';
                                                            $html .= '<td class="bg-dark"></td>';
                                                        }
                                                    }
                                                } else {
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                }
                                                // Kegiatan Indikator End;
                                            $html .='</tr>';
                                        } else {
                                            $html .='<tr>';
                                                $html .= '<td></td>';
                                                $html .= '<td></td>';
                                                // Kegiatan Indikator Start;
                                                $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                $cek_target_kegiatan = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id, $kegiatan_indikator_kinerja){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
                                                })->where('tahun', $tahun)->first();
                                                if($cek_target_kegiatan)
                                                {
                                                    $html .= '<td class="bg-success">'.$cek_target_kegiatan->target.'</td>';
                                                    $html .= '<td class="bg-success">Rp. '.number_format((int) $cek_target_kegiatan->target_rp, 2, ',', '.').'</td>';

                                                    foreach ($tws as $tw) {
                                                        $cek_realisasi_kegiatan = KegiatanTwRealisasi::where('sasaran_id', $sasaran_pd['sasaran_id'])
                                                                                    ->where('program_id', $program['id'])
                                                                                    ->where('kegiatan_target_satuan_rp_realisasi_id', $cek_target_kegiatan->id)
                                                                                    ->where('tw_id', $tw->id)
                                                                                    ->first();
                                                        if($cek_realisasi_kegiatan)
                                                        {
                                                            $html .= '<td class="bg-success">'.$cek_realisasi_kegiatan->realisasi.'</td>';
                                                            $html .= '<td class="bg-success">Rp. '.number_format((int)$cek_realisasi_kegiatan->realisasi_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $html .= '<td class="bg-dark"></td>';
                                                            $html .= '<td class="bg-dark"></td>';
                                                        }
                                                    }
                                                } else {
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                    $html .= '<td class="bg-dark"></td>';
                                                }
                                                // Kegiatan Indikator End;
                                            $html .='</tr>';
                                        }
                                        $i_d++;
                                    }

                                    $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan['id'])->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                        $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                            $q->where('opd_id', $opd_id);
                                        });
                                    })->get();

                                    $sub_kegiatans = [];
                                    foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)
                                                                        ->where('tahun_perubahan', $tahun)
                                                                        ->latest()
                                                                        ->first();
                                        if($cek_perubahan_sub_kegiatan)
                                        {
                                            $sub_kegiatans[] = [
                                                'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                                                'kode' => $cek_perubahan_sub_kegiatan->kode,
                                                'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi
                                            ];
                                        } else {
                                            $sub_kegiatans[] = [
                                                'id' => $get_sub_kegiatan->id,
                                                'kode' => $get_sub_kegiatan->kode,
                                                'deskripsi' => $get_sub_kegiatan->deskripsi
                                            ];
                                        }
                                    }

                                    $e = 1;
                                    foreach ($sub_kegiatans as $sub_kegiatan) {
                                        $html .= '<tr>';
                                            $html .= '<td>sk.'.$e.'</td>';
                                            $html .= '<td>'.$sub_kegiatan['deskripsi'].'</td>';

                                            $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])
                                                                                ->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                    $q->where('opd_id', $opd_id);
                                                                                })->get();
                                            $i_e = 1;
                                            foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                                if($i_e == 1)
                                                {
                                                        // Sub Kegiatan Indikator Start;
                                                            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                            $cek_target_sub_kegiatan = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id, $sub_kegiatan_indikator_kinerja){
                                                                $q->where('opd_id', $opd_id);
                                                                $q->where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id);
                                                            })->where('tahun', $tahun)->first();
                                                            if($cek_target_sub_kegiatan)
                                                            {
                                                                $html .= '<td class="bg-success">'.$cek_target_sub_kegiatan->target.'</td>';
                                                                $html .= '<td class="bg-success">Rp. '.number_format((int) $cek_target_sub_kegiatan->target_rp, 2, ',', '.').'</td>';

                                                                foreach ($tws as $tw) {
                                                                    $cek_realisasi_sub_kegiatan = SubKegiatanTwRealisasi::where('sasaran_id', $sasaran_pd['sasaran_id'])
                                                                                                ->where('program_id', $program['id'])
                                                                                                ->where('kegiatan_id', $kegiatan['id'])
                                                                                                ->where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_target_sub_kegiatan->id)
                                                                                                ->where('tw_id', $tw->id)
                                                                                                ->first();
                                                                    if($cek_realisasi_sub_kegiatan)
                                                                    {
                                                                        $html .= '<td class="bg-success">'.$cek_realisasi_sub_kegiatan->realisasi.'</td>';
                                                                        $html .= '<td class="bg-success">Rp. '.number_format((int)$cek_realisasi_sub_kegiatan->realisasi_rp, 2, ',', '.').'</td>';
                                                                    } else {
                                                                        $html .= '<td class="bg-dark"></td>';
                                                                        $html .= '<td class="bg-dark"></td>';
                                                                    }
                                                                }
                                                            } else {
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                            }
                                                        // Sub Kegiatan Indikator End;
                                                    $html .= '</tr>';
                                                } else {
                                                    $html .= '<tr>';
                                                        $html .= '<td></td>';
                                                        $html .= '<td></td>';
                                                        // Sub Kegiatan Indikator Start;
                                                            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                            $cek_target_sub_kegiatan = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id, $sub_kegiatan_indikator_kinerja){
                                                                $q->where('opd_id', $opd_id);
                                                                $q->where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id);
                                                            })->where('tahun', $tahun)->first();
                                                            if($cek_target_sub_kegiatan)
                                                            {
                                                                $html .= '<td class="bg-success">'.$cek_target_sub_kegiatan->target.'</td>';
                                                                $html .= '<td class="bg-success">Rp. '.number_format((int) $cek_target_sub_kegiatan->target_rp, 2, ',', '.').'</td>';

                                                                foreach ($tws as $tw) {
                                                                    $cek_realisasi_sub_kegiatan = SubKegiatanTwRealisasi::where('sasaran_id', $sasaran_pd['sasaran_id'])
                                                                                                ->where('program_id', $program['id'])
                                                                                                ->where('kegiatan_id', $kegiatan['id'])
                                                                                                ->where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_target_sub_kegiatan->id)
                                                                                                ->where('tw_id', $tw->id)
                                                                                                ->first();
                                                                    if($cek_realisasi_sub_kegiatan)
                                                                    {
                                                                        $html .= '<td class="bg-success">'.$cek_realisasi_sub_kegiatan->realisasi.'</td>';
                                                                        $html .= '<td class="bg-success">Rp. '.number_format((int)$cek_realisasi_sub_kegiatan->realisasi_rp, 2, ',', '.').'</td>';
                                                                    } else {
                                                                        $html .= '<td class="bg-dark"></td>';
                                                                        $html .= '<td class="bg-dark"></td>';
                                                                    }
                                                                }
                                                            } else {
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                                $html .= '<td class="bg-dark"></td>';
                                                            }
                                                        // Sub Kegiatan Indikator End;
                                                    $html .= '</tr>';
                                                }
                                                $i_e++;
                                            }

                                        $e++;
                                    }
                                $d++;
                            }

                        $c++;
                    }

                    $b++;
                }
            $a++;
        }

        return $html;
    }
}
