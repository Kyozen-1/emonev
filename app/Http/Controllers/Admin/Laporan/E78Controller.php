<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use RealRashid\SweetAlert\Facades\Alert;
use DB;
use Validator;
use DataTables;
use Excel;
use PDF;
use Carbon\Carbon;
use App\Models\TahunPeriode;
use App\Models\Visi;
use App\Models\PivotPerubahanVisi;
use App\Models\Misi;
use App\Models\PivotPerubahanMisi;
use App\Models\Tujuan;
use App\Models\PivotPerubahanTujuan;
use App\Models\Sasaran;
use App\Models\PivotPerubahanSasaran;
use App\Models\PivotSasaranIndikator;
use App\Models\ProgramRpjmd;
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Models\MasterOpd;
use App\Models\PivotSasaranIndikatorProgramRpjmd;
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotProgramKegiatanRenstra;
use App\Models\TargetRpPertahunProgram;
use App\Models\RenstraKegiatan;
use App\Models\PivotPerubahanKegiatan;
use App\Models\Kegiatan;
use App\Models\PivotOpdRentraKegiatan;
use App\Models\TargetRpPertahunRenstraKegiatan;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\TujuanIndikatorKinerja;
use App\Models\TujuanTargetSatuanRpRealisasi;
use App\Models\SasaranIndikatorKinerja;
use App\Models\SasaranTargetSatuanRpRealisasi;
use App\Models\TujuanPd;
use App\Models\PivotPerubahanTujuanPd;
use App\Models\TujuanPdIndikatorKinerja;
use App\Models\TujuanPdTargetSatuanRpRealisasi;
use App\Models\SasaranPd;
use App\Models\PivotPerubahanSasaranPd;
use App\Models\SasaranPdIndikatorKinerja;
use App\Models\SasaranPdTargetSatuanRpRealisasi;
use App\Models\ProgramIndikatorKinerja;
use App\Models\OpdProgramIndikatorKinerja;
use App\Models\ProgramTargetSatuanRpRealisasi;
use App\Models\KegiatanIndikatorKinerja;
use App\Models\KegiatanTargetSatuanRpRealisasi;
use App\Models\OpdKegiatanIndikatorKinerja;
use App\Exports\E78Ekspor;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\SubKegiatanTwRealisasi;
use App\Models\TujuanTwRealisasi;
use App\Models\SasaranTwRealisasi;

class E78Controller extends Controller
{
    public function e_78()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $new_get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $new_tahun_awal = $new_get_periode->tahun_awal;
        $new_jarak_tahun = $new_get_periode->tahun_akhir - $new_tahun_awal;
        $new_tahun_akhir = $new_get_periode->tahun_akhir;
        $new_tahuns = [];
        for ($i=0; $i < $new_jarak_tahun + 1; $i++) {
            $new_tahuns[] = $new_tahun_awal + $i;
        }

        // E 78 Start
        $get_sasarans = Sasaran::where('tahun_periode_id', $get_periode->id)->get();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();
            if($cek_perubahan_sasaran)
            {
                $sasarans[] = [
                    'id' => $cek_perubahan_sasaran->sasaran_id,
                    'kode' => $cek_perubahan_sasaran->kode,
                    'deskripsi' => $cek_perubahan_sasaran->deskripsi
                ];
            } else {
                $sasarans[] = [
                    'id' => $get_sasaran->id,
                    'kode' => $get_sasaran->kode,
                    'deskripsi' => $get_sasaran->deskripsi
                ];
            }
        }

        $e_78 = '';
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_78 .= '<tr>';
                $e_78 .= '<td>'.$a++.'</td>';
                $e_78 .= '<td>'.$sasaran['deskripsi'].'</td>';
                $e_78 .= '<td></td>';

                // Indikator Sasaran
                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                $indikator_a = 1;
                foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                    if($indikator_a == 1)
                    {
                            $e_78 .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                            $e_78 .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            // Kolom 6 Start

                            $sasaran_target_satuan_rp_realisasi_kolom_6_k = SasaranTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                            if($sasaran_target_satuan_rp_realisasi_kolom_6_k)
                            {
                                $e_78 .= '<td>'.$sasaran_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            } else {
                                $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            }

                            $e_78 .= '<td>Rp. 0,00</td>';

                            // Kolom 6 End

                            // Kolom 7 - 11 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $e_78 .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                } else {
                                    $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0, 00</td>';
                                }
                            }

                            // Kolom 7 - 11 End

                            // Kolom 12 - 16 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                        ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                            $q->where('id', $sasaran_indikator_kinerja->id);
                                                                        })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->first();
                                    if($cek_sasaran_tw_realisasi)
                                    {
                                        $get_sasaran_tw_realisasis = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->get();
                                        $sasaran_tw_realisasi = [];
                                        foreach ($get_sasaran_tw_realisasis as $get_sasaran_tw_realisasi) {
                                            $sasaran_tw_realisasi[] = $get_sasaran_tw_realisasi->realisasi;
                                        }
                                        $e_78 .= '<td>'.array_sum($sasaran_tw_realisasi).'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $e_78 .= '<td>Rp. 0, 00</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $e_78 .= '<td>Rp. 0,00</td>';
                                    }
                                } else {
                                    $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                }
                            }

                            // Kolom 12 - 16 End

                            // Kolom 17 - 21 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->first();

                                    $sasaran_tw_realisasi = [];

                                    if($cek_sasaran_tw_realisasi)
                                    {
                                        $get_sasaran_tw_realisasis = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->get();
                                        foreach ($get_sasaran_tw_realisasis as $get_sasaran_tw_realisasi) {
                                            $sasaran_tw_realisasi[] = $get_sasaran_tw_realisasi->realisasi;
                                        }
                                    } else {
                                        $sasaran_tw_realisasi[] = 0;
                                    }

                                    if((int) $cek_sasaran_target_satuan_rp_realisasi->target != 0)
                                    {
                                        $kolom_17_21_k = (array_sum($sasaran_tw_realisasi) / (int) $cek_sasaran_target_satuan_rp_realisasi->target) * 100;
                                        $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                    } else {
                                        $e_78 .= '<td>0,00</td>';
                                    }

                                    $e_78 .= '<td>Rp. 0,00</td>';

                                } else {
                                    $e_78 .= '<td>0,00</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                }
                            }

                            // Kolom 17 - 21 End

                            // Kolom 22 Start

                            $last_sasaran_target_satuan_rp_realisasi_kolom_22 = SasaranTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                    $q->where('id', $sasaran_indikator_kinerja->id);
                                                                                })->first();
                            $sasaran_tw_realisasi_kolom_22 = [];
                            if($last_sasaran_target_satuan_rp_realisasi_kolom_22)
                            {
                                $cek_sasaran_tw_realisasi_kolom_22 = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $last_sasaran_target_satuan_rp_realisasi_kolom_22->id)->first();
                                if($cek_sasaran_tw_realisasi_kolom_22)
                                {
                                    $get_sasaran_tw_realisasis_kolom_22 = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $last_sasaran_target_satuan_rp_realisasi_kolom_22->id)->get();
                                    foreach ($get_sasaran_tw_realisasis_kolom_22 as $get_sasaran_tw_realisasi_kolom_22) {
                                        $sasaran_tw_realisasi_kolom_22[] = $get_sasaran_tw_realisasi_kolom_22->realisasi;
                                    }
                                } else {
                                    $sasaran_tw_realisasi_kolom_22[] = 0;
                                }
                            } else {
                                $sasaran_tw_realisasi_kolom_22[] = 0;
                            }
                            $e_78 .= '<td>'.array_sum($sasaran_tw_realisasi_kolom_22).'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            $e_78 .= '<td>Rp. 0,00</td>';

                            // Kolom 22 End

                            // Kolom 23 Start
                            if($sasaran_target_satuan_rp_realisasi_kolom_6_k)
                            {
                                if((int) $sasaran_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                {
                                    $kolom_23_k = (array_sum($sasaran_tw_realisasi_kolom_22) / (int) $sasaran_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                } else {
                                    $kolom_23_k = 0;
                                }
                            } else {
                                $kolom_23_k = 0;
                            }

                            $e_78 .= '<td>'.$kolom_23_k.'</td>';
                            $e_78 .= '<td>0,00</td>';
                            // Kolom 23 End
                        $e_78 .='</tr>';
                    } else {
                        $e_78 .= '<tr>';
                            $e_78 .= '<td></td>';
                            $e_78 .= '<td></td>';
                            $e_78 .= '<td></td>';
                            $e_78 .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                            $e_78 .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            // Kolom 6 Start

                            $sasaran_target_satuan_rp_realisasi_kolom_6_k = SasaranTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                            if($sasaran_target_satuan_rp_realisasi_kolom_6_k)
                            {
                                $e_78 .= '<td>'.$sasaran_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            } else {
                                $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            }

                            $e_78 .= '<td>Rp. 0,00</td>';

                            // Kolom 6 End

                            // Kolom 7 - 11 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $e_78 .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                } else {
                                    $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0, 00</td>';
                                }
                            }

                            // Kolom 7 - 11 End

                            // Kolom 12 - 16 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                        ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                            $q->where('id', $sasaran_indikator_kinerja->id);
                                                                        })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->first();
                                    if($cek_sasaran_tw_realisasi)
                                    {
                                        $get_sasaran_tw_realisasis = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->get();
                                        $sasaran_tw_realisasi = [];
                                        foreach ($get_sasaran_tw_realisasis as $get_sasaran_tw_realisasi) {
                                            $sasaran_tw_realisasi[] = $get_sasaran_tw_realisasi->realisasi;
                                        }
                                        $e_78 .= '<td>'.array_sum($sasaran_tw_realisasi).'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $e_78 .= '<td>Rp. 0, 00</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $e_78 .= '<td>Rp. 0,00</td>';
                                    }
                                } else {
                                    $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                }
                            }

                            // Kolom 12 - 16 End

                            // Kolom 17 - 21 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->first();

                                    $sasaran_tw_realisasi = [];

                                    if($cek_sasaran_tw_realisasi)
                                    {
                                        $get_sasaran_tw_realisasis = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->get();
                                        foreach ($get_sasaran_tw_realisasis as $get_sasaran_tw_realisasi) {
                                            $sasaran_tw_realisasi[] = $get_sasaran_tw_realisasi->realisasi;
                                        }
                                    } else {
                                        $sasaran_tw_realisasi[] = 0;
                                    }

                                    if((int) $cek_sasaran_target_satuan_rp_realisasi->target != 0)
                                    {
                                        $kolom_17_21_k = (array_sum($sasaran_tw_realisasi) / (int) $cek_sasaran_target_satuan_rp_realisasi->target) * 100;
                                        $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                    } else {
                                        $e_78 .= '<td>0,00</td>';
                                    }

                                    $e_78 .= '<td>Rp. 0,00</td>';

                                } else {
                                    $e_78 .= '<td>0,00</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                }
                            }

                            // Kolom 17 - 21 End

                            // Kolom 22 Start

                            $last_sasaran_target_satuan_rp_realisasi_kolom_22 = SasaranTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                    $q->where('id', $sasaran_indikator_kinerja->id);
                                                                                })->first();
                            $sasaran_tw_realisasi_kolom_22 = [];
                            if($last_sasaran_target_satuan_rp_realisasi_kolom_22)
                            {
                                $cek_sasaran_tw_realisasi_kolom_22 = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $last_sasaran_target_satuan_rp_realisasi_kolom_22->id)->first();
                                if($cek_sasaran_tw_realisasi_kolom_22)
                                {
                                    $get_sasaran_tw_realisasis_kolom_22 = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $last_sasaran_target_satuan_rp_realisasi_kolom_22->id)->get();
                                    foreach ($get_sasaran_tw_realisasis_kolom_22 as $get_sasaran_tw_realisasi_kolom_22) {
                                        $sasaran_tw_realisasi_kolom_22[] = $get_sasaran_tw_realisasi_kolom_22->realisasi;
                                    }
                                } else {
                                    $sasaran_tw_realisasi_kolom_22[] = 0;
                                }
                            } else {
                                $sasaran_tw_realisasi_kolom_22[] = 0;
                            }
                            $e_78 .= '<td>'.array_sum($sasaran_tw_realisasi_kolom_22).'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            $e_78 .= '<td>Rp. 0,00</td>';

                            // Kolom 22 End

                            // Kolom 23 Start
                            if($sasaran_target_satuan_rp_realisasi_kolom_6_k)
                            {
                                if((int) $sasaran_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                {
                                    $kolom_23_k = (array_sum($sasaran_tw_realisasi_kolom_22) / (int) $sasaran_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                } else {
                                    $kolom_23_k = 0;
                                }
                            } else {
                                $kolom_23_k = 0;
                            }

                            $e_78 .= '<td>'.$kolom_23_k.'</td>';
                            $e_78 .= '<td>0,00</td>';
                            // Kolom 23 End
                        $e_78 .='</tr>';
                    }
                    $indikator_a++;
                }

            $get_programs = Program::whereHas('program_rpjmd', function($q) use ($sasaran) {
                $q->where('status_program', 'Prioritas');
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran) {
                        $q->whereHas('sasaran', function($q) use ($sasaran) {
                            $q->where('id', $sasaran['id']);
                        });
                    });
                });
            })->get();
            $programs = [];
            foreach ($get_programs as $get_program) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
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
            $e_78 .= '<tr>';
                $e_78 .= '<td></td>';
                $e_78 .= '<td></td>';
                $b = 1;
                foreach ($programs as $program) {
                    if($b == 1)
                    {
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                        // Indikator Program Start
                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                        $c = 1;
                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                            if($c == 1)
                            {
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    // Kolom 6 Start
                                    $program_target_satuan_rp_realisasi_kolom_6_k = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        $e_78 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                    }

                                    $kolom_6_rp = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_6_rp = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_6_rp)
                                        {
                                            $kolom_6_rp[] = $cek_program_target_satuan_rp_realisasi_kolom_6_rp->target_rp;
                                        } else {
                                            $kolom_6_rp[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp'.number_format(array_sum($kolom_6_rp), 2, ',', '.').'</td>';

                                    // Kolom 6 End

                                    // Kolom 7 - 11 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_78 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }

                                    // Kolom 7 - 11 End

                                    // Kolom 12 - 16 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                $program_tw_realisasi = [];
                                                $program_tw_realisasi_rp = [];
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 12 - 16 End

                                    // Kolom 17 - 21 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();

                                            $program_tw_realisasi = [];
                                            $program_tw_realisasi_rp = [];

                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $program_tw_realisasi[] = 0;
                                                $program_tw_realisasi_rp[] = 0;
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target != 0)
                                            {
                                                $kolom_17_21_k = (array_sum($program_tw_realisasi) / (int) $cek_program_target_satuan_rp_realisasi->target) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0,00</td>';
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target_rp != 0)
                                            {
                                                $kolom_17_21_rp = (array_sum($program_tw_realisasi_rp) / (int) $cek_program_target_satuan_rp_realisasi->target_rp) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }

                                        } else {
                                            $e_78 .= '<td>0,00</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 17 - 21 End

                                    // Kolom 22 Start

                                    $last_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    $program_tw_realisasi_kolom_22 = [];
                                    if($last_program_target_satuan_rp_realisasi_kolom_22)
                                    {
                                        $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->first();
                                        if($cek_program_tw_realisasi_kolom_22)
                                        {
                                            $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                            foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                $program_tw_realisasi_kolom_22[] = $get_program_tw_realisasi_kolom_22->realisasi;
                                            }
                                        } else {
                                            $program_tw_realisasi_kolom_22[] = 0;
                                        }
                                    } else {
                                        $program_tw_realisasi_kolom_22[] = 0;
                                    }
                                    $e_78 .= '<td>'.array_sum($program_tw_realisasi_kolom_22).'/'.$program_indikator_kinerja->satuan.'</td>';

                                    $realisasi_rp_kolom_22 = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_22)
                                        {
                                            $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->first();

                                            if($cek_program_tw_realisasi_kolom_22)
                                            {
                                                $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                                foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                    $realisasi_rp_kolom_22[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $realisasi_rp_kolom_22[] = 0;
                                            }
                                        } else {
                                            $realisasi_rp_kolom_22[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp. '.number_format(array_sum($realisasi_rp_kolom_22), 2, ',', '.').'</td>';

                                    // Kolom 22 End

                                    // Kolom 23 Start
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        if((int) $program_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                        {
                                            $kolom_23_k = (array_sum($program_tw_realisasi_kolom_22) / (int) $program_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                        } else {
                                            $kolom_23_k = 0;
                                        }
                                    } else {
                                        $kolom_23_k = 0;
                                    }

                                    if(array_sum($kolom_6_rp) != 0)
                                    {
                                        $kolom_23_rp = (array_sum($realisasi_rp_kolom_22) / array_sum($kolom_6_rp)) * 100;
                                    } else {
                                        $kolom_23_rp = 0;
                                    }

                                    $e_78 .= '<td>'.$kolom_23_k.'</td>';
                                    $e_78 .= '<td>'.number_format($kolom_23_rp, 2, ',', '.').'</td>';
                                    // Kolom 23 End
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    // Kolom 6 Start
                                    $program_target_satuan_rp_realisasi_kolom_6_k = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        $e_78 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                    }

                                    $kolom_6_rp = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_6_rp = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_6_rp)
                                        {
                                            $kolom_6_rp[] = $cek_program_target_satuan_rp_realisasi_kolom_6_rp->target_rp;
                                        } else {
                                            $kolom_6_rp[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp'.number_format(array_sum($kolom_6_rp), 2, ',', '.').'</td>';

                                    // Kolom 6 End

                                    // Kolom 7 - 11 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_78 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }

                                    // Kolom 7 - 11 End

                                    // Kolom 12 - 16 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                $program_tw_realisasi = [];
                                                $program_tw_realisasi_rp = [];
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 12 - 16 End

                                    // Kolom 17 - 21 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();

                                            $program_tw_realisasi = [];
                                            $program_tw_realisasi_rp = [];

                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $program_tw_realisasi[] = 0;
                                                $program_tw_realisasi_rp[] = 0;
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target != 0)
                                            {
                                                $kolom_17_21_k = (array_sum($program_tw_realisasi) / (int) $cek_program_target_satuan_rp_realisasi->target) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0,00</td>';
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target_rp != 0)
                                            {
                                                $kolom_17_21_rp = (array_sum($program_tw_realisasi_rp) / (int) $cek_program_target_satuan_rp_realisasi->target_rp) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }

                                        } else {
                                            $e_78 .= '<td>0,00</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 17 - 21 End

                                    // Kolom 22 Start

                                    $last_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    $program_tw_realisasi_kolom_22 = [];
                                    if($last_program_target_satuan_rp_realisasi_kolom_22)
                                    {
                                        $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->first();
                                        if($cek_program_tw_realisasi_kolom_22)
                                        {
                                            $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                            foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                $program_tw_realisasi_kolom_22[] = $get_program_tw_realisasi_kolom_22->realisasi;
                                            }
                                        } else {
                                            $program_tw_realisasi_kolom_22[] = 0;
                                        }
                                    } else {
                                        $program_tw_realisasi_kolom_22[] = 0;
                                    }
                                    $e_78 .= '<td>'.array_sum($program_tw_realisasi_kolom_22).'/'.$program_indikator_kinerja->satuan.'</td>';

                                    $realisasi_rp_kolom_22 = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_22)
                                        {
                                            $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->first();

                                            if($cek_program_tw_realisasi_kolom_22)
                                            {
                                                $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                                foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                    $realisasi_rp_kolom_22[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $realisasi_rp_kolom_22[] = 0;
                                            }
                                        } else {
                                            $realisasi_rp_kolom_22[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp. '.number_format(array_sum($realisasi_rp_kolom_22), 2, ',', '.').'</td>';

                                    // Kolom 22 End

                                    // Kolom 23 Start
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        if((int) $program_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                        {
                                            $kolom_23_k = (array_sum($program_tw_realisasi_kolom_22) / (int) $program_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                        } else {
                                            $kolom_23_k = 0;
                                        }
                                    } else {
                                        $kolom_23_k = 0;
                                    }

                                    if(array_sum($kolom_6_rp) != 0)
                                    {
                                        $kolom_23_rp = (array_sum($realisasi_rp_kolom_22) / array_sum($kolom_6_rp)) * 100;
                                    } else {
                                        $kolom_23_rp = 0;
                                    }

                                    $e_78 .= '<td>'.$kolom_23_k.'</td>';
                                    $e_78 .= '<td>'.number_format($kolom_23_rp, 2, ',', '.').'</td>';
                                    // Kolom 23 End
                                $e_78 .= '</tr>';
                            }
                            $c++;
                        }
                    } else {
                        $e_78 .= '<tr>';
                        $e_78 .= '<td></td>';
                        $e_78 .= '<td></td>';
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                        // Indikator Program Start
                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                        $c = 1;
                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                            if($c == 1)
                            {
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    // Kolom 6 Start
                                    $program_target_satuan_rp_realisasi_kolom_6_k = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        $e_78 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                    }

                                    $kolom_6_rp = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_6_rp = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_6_rp)
                                        {
                                            $kolom_6_rp[] = $cek_program_target_satuan_rp_realisasi_kolom_6_rp->target_rp;
                                        } else {
                                            $kolom_6_rp[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp'.number_format(array_sum($kolom_6_rp), 2, ',', '.').'</td>';

                                    // Kolom 6 End

                                    // Kolom 7 - 11 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_78 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }

                                    // Kolom 7 - 11 End

                                    // Kolom 12 - 16 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                $program_tw_realisasi = [];
                                                $program_tw_realisasi_rp = [];
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 12 - 16 End

                                    // Kolom 17 - 21 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();

                                            $program_tw_realisasi = [];
                                            $program_tw_realisasi_rp = [];

                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $program_tw_realisasi[] = 0;
                                                $program_tw_realisasi_rp[] = 0;
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target != 0)
                                            {
                                                $kolom_17_21_k = (array_sum($program_tw_realisasi) / (int) $cek_program_target_satuan_rp_realisasi->target) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0,00</td>';
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target_rp != 0)
                                            {
                                                $kolom_17_21_rp = (array_sum($program_tw_realisasi_rp) / (int) $cek_program_target_satuan_rp_realisasi->target_rp) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }

                                        } else {
                                            $e_78 .= '<td>0,00</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 17 - 21 End

                                    // Kolom 22 Start

                                    $last_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    $program_tw_realisasi_kolom_22 = [];
                                    if($last_program_target_satuan_rp_realisasi_kolom_22)
                                    {
                                        $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->first();
                                        if($cek_program_tw_realisasi_kolom_22)
                                        {
                                            $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                            foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                $program_tw_realisasi_kolom_22[] = $get_program_tw_realisasi_kolom_22->realisasi;
                                            }
                                        } else {
                                            $program_tw_realisasi_kolom_22[] = 0;
                                        }
                                    } else {
                                        $program_tw_realisasi_kolom_22[] = 0;
                                    }
                                    $e_78 .= '<td>'.array_sum($program_tw_realisasi_kolom_22).'/'.$program_indikator_kinerja->satuan.'</td>';

                                    $realisasi_rp_kolom_22 = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_22)
                                        {
                                            $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->first();

                                            if($cek_program_tw_realisasi_kolom_22)
                                            {
                                                $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                                foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                    $realisasi_rp_kolom_22[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $realisasi_rp_kolom_22[] = 0;
                                            }
                                        } else {
                                            $realisasi_rp_kolom_22[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp. '.number_format(array_sum($realisasi_rp_kolom_22), 2, ',', '.').'</td>';

                                    // Kolom 22 End

                                    // Kolom 23 Start
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        if((int) $program_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                        {
                                            $kolom_23_k = (array_sum($program_tw_realisasi_kolom_22) / (int) $program_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                        } else {
                                            $kolom_23_k = 0;
                                        }
                                    } else {
                                        $kolom_23_k = 0;
                                    }

                                    if(array_sum($kolom_6_rp) != 0)
                                    {
                                        $kolom_23_rp = (array_sum($realisasi_rp_kolom_22) / array_sum($kolom_6_rp)) * 100;
                                    } else {
                                        $kolom_23_rp = 0;
                                    }

                                    $e_78 .= '<td>'.$kolom_23_k.'</td>';
                                    $e_78 .= '<td>'.number_format($kolom_23_rp, 2, ',', '.').'</td>';
                                    // Kolom 23 End
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    // Kolom 6 Start
                                    $program_target_satuan_rp_realisasi_kolom_6_k = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        $e_78 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                    }

                                    $kolom_6_rp = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_6_rp = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_6_rp)
                                        {
                                            $kolom_6_rp[] = $cek_program_target_satuan_rp_realisasi_kolom_6_rp->target_rp;
                                        } else {
                                            $kolom_6_rp[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp'.number_format(array_sum($kolom_6_rp), 2, ',', '.').'</td>';

                                    // Kolom 6 End

                                    // Kolom 7 - 11 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_78 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }

                                    // Kolom 7 - 11 End

                                    // Kolom 12 - 16 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                $program_tw_realisasi = [];
                                                $program_tw_realisasi_rp = [];
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 12 - 16 End

                                    // Kolom 17 - 21 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();

                                            $program_tw_realisasi = [];
                                            $program_tw_realisasi_rp = [];

                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $program_tw_realisasi[] = 0;
                                                $program_tw_realisasi_rp[] = 0;
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target != 0)
                                            {
                                                $kolom_17_21_k = (array_sum($program_tw_realisasi) / (int) $cek_program_target_satuan_rp_realisasi->target) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0,00</td>';
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target_rp != 0)
                                            {
                                                $kolom_17_21_rp = (array_sum($program_tw_realisasi_rp) / (int) $cek_program_target_satuan_rp_realisasi->target_rp) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }

                                        } else {
                                            $e_78 .= '<td>0,00</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 17 - 21 End

                                    // Kolom 22 Start

                                    $last_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    $program_tw_realisasi_kolom_22 = [];
                                    if($last_program_target_satuan_rp_realisasi_kolom_22)
                                    {
                                        $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->first();
                                        if($cek_program_tw_realisasi_kolom_22)
                                        {
                                            $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                            foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                $program_tw_realisasi_kolom_22[] = $get_program_tw_realisasi_kolom_22->realisasi;
                                            }
                                        } else {
                                            $program_tw_realisasi_kolom_22[] = 0;
                                        }
                                    } else {
                                        $program_tw_realisasi_kolom_22[] = 0;
                                    }
                                    $e_78 .= '<td>'.array_sum($program_tw_realisasi_kolom_22).'/'.$program_indikator_kinerja->satuan.'</td>';

                                    $realisasi_rp_kolom_22 = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_22)
                                        {
                                            $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->first();

                                            if($cek_program_tw_realisasi_kolom_22)
                                            {
                                                $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                                foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                    $realisasi_rp_kolom_22[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $realisasi_rp_kolom_22[] = 0;
                                            }
                                        } else {
                                            $realisasi_rp_kolom_22[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp. '.number_format(array_sum($realisasi_rp_kolom_22), 2, ',', '.').'</td>';

                                    // Kolom 22 End

                                    // Kolom 23 Start
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        if((int) $program_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                        {
                                            $kolom_23_k = (array_sum($program_tw_realisasi_kolom_22) / (int) $program_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                        } else {
                                            $kolom_23_k = 0;
                                        }
                                    } else {
                                        $kolom_23_k = 0;
                                    }

                                    if(array_sum($kolom_6_rp) != 0)
                                    {
                                        $kolom_23_rp = (array_sum($realisasi_rp_kolom_22) / array_sum($kolom_6_rp)) * 100;
                                    } else {
                                        $kolom_23_rp = 0;
                                    }

                                    $e_78 .= '<td>'.$kolom_23_k.'</td>';
                                    $e_78 .= '<td>'.number_format($kolom_23_rp, 2, ',', '.').'</td>';
                                    // Kolom 23 End
                                $e_78 .= '</tr>';
                            }
                            $c++;
                        }
                    }
                    $b++;
                }
        }

         // E 78 End
        return response()->json(['e_78' => $e_78]);
    }

    public function e_78_ekspor_pdf()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $new_get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $new_tahun_awal = $new_get_periode->tahun_awal;
        $new_jarak_tahun = $new_get_periode->tahun_akhir - $new_tahun_awal;
        $new_tahun_akhir = $new_get_periode->tahun_akhir;
        $new_tahuns = [];
        for ($i=0; $i < $new_jarak_tahun + 1; $i++) {
            $new_tahuns[] = $new_tahun_awal + $i;
        }

        // E 78 Start
        $get_sasarans = Sasaran::where('tahun_periode_id', $get_periode->id)->get();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();
            if($cek_perubahan_sasaran)
            {
                $sasarans[] = [
                    'id' => $cek_perubahan_sasaran->sasaran_id,
                    'kode' => $cek_perubahan_sasaran->kode,
                    'deskripsi' => $cek_perubahan_sasaran->deskripsi
                ];
            } else {
                $sasarans[] = [
                    'id' => $get_sasaran->id,
                    'kode' => $get_sasaran->kode,
                    'deskripsi' => $get_sasaran->deskripsi
                ];
            }
        }

        $e_78 = '';
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_78 .= '<tr>';
                $e_78 .= '<td>'.$a++.'</td>';
                $e_78 .= '<td>'.$sasaran['deskripsi'].'</td>';
                $e_78 .= '<td></td>';

                // Indikator Sasaran
                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                $indikator_a = 1;
                foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                    if($indikator_a == 1)
                    {
                            $e_78 .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                            $e_78 .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            // Kolom 6 Start

                            $sasaran_target_satuan_rp_realisasi_kolom_6_k = SasaranTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                            if($sasaran_target_satuan_rp_realisasi_kolom_6_k)
                            {
                                $e_78 .= '<td>'.$sasaran_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            } else {
                                $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            }

                            $e_78 .= '<td>Rp. 0,00</td>';

                            // Kolom 6 End

                            // Kolom 7 - 11 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $e_78 .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                } else {
                                    $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0, 00</td>';
                                }
                            }

                            // Kolom 7 - 11 End

                            // Kolom 12 - 16 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                        ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                            $q->where('id', $sasaran_indikator_kinerja->id);
                                                                        })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->first();
                                    if($cek_sasaran_tw_realisasi)
                                    {
                                        $get_sasaran_tw_realisasis = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->get();
                                        $sasaran_tw_realisasi = [];
                                        foreach ($get_sasaran_tw_realisasis as $get_sasaran_tw_realisasi) {
                                            $sasaran_tw_realisasi[] = $get_sasaran_tw_realisasi->realisasi;
                                        }
                                        $e_78 .= '<td>'.array_sum($sasaran_tw_realisasi).'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $e_78 .= '<td>Rp. 0, 00</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $e_78 .= '<td>Rp. 0,00</td>';
                                    }
                                } else {
                                    $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                }
                            }

                            // Kolom 12 - 16 End

                            // Kolom 17 - 21 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->first();

                                    $sasaran_tw_realisasi = [];

                                    if($cek_sasaran_tw_realisasi)
                                    {
                                        $get_sasaran_tw_realisasis = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->get();
                                        foreach ($get_sasaran_tw_realisasis as $get_sasaran_tw_realisasi) {
                                            $sasaran_tw_realisasi[] = $get_sasaran_tw_realisasi->realisasi;
                                        }
                                    } else {
                                        $sasaran_tw_realisasi[] = 0;
                                    }

                                    if((int) $cek_sasaran_target_satuan_rp_realisasi->target != 0)
                                    {
                                        $kolom_17_21_k = (array_sum($sasaran_tw_realisasi) / (int) $cek_sasaran_target_satuan_rp_realisasi->target) * 100;
                                        $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                    } else {
                                        $e_78 .= '<td>0,00</td>';
                                    }

                                    $e_78 .= '<td>Rp. 0,00</td>';

                                } else {
                                    $e_78 .= '<td>0,00</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                }
                            }

                            // Kolom 17 - 21 End

                            // Kolom 22 Start

                            $last_sasaran_target_satuan_rp_realisasi_kolom_22 = SasaranTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                    $q->where('id', $sasaran_indikator_kinerja->id);
                                                                                })->first();
                            $sasaran_tw_realisasi_kolom_22 = [];
                            if($last_sasaran_target_satuan_rp_realisasi_kolom_22)
                            {
                                $cek_sasaran_tw_realisasi_kolom_22 = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $last_sasaran_target_satuan_rp_realisasi_kolom_22->id)->first();
                                if($cek_sasaran_tw_realisasi_kolom_22)
                                {
                                    $get_sasaran_tw_realisasis_kolom_22 = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $last_sasaran_target_satuan_rp_realisasi_kolom_22->id)->get();
                                    foreach ($get_sasaran_tw_realisasis_kolom_22 as $get_sasaran_tw_realisasi_kolom_22) {
                                        $sasaran_tw_realisasi_kolom_22[] = $get_sasaran_tw_realisasi_kolom_22->realisasi;
                                    }
                                } else {
                                    $sasaran_tw_realisasi_kolom_22[] = 0;
                                }
                            } else {
                                $sasaran_tw_realisasi_kolom_22[] = 0;
                            }
                            $e_78 .= '<td>'.array_sum($sasaran_tw_realisasi_kolom_22).'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            $e_78 .= '<td>Rp. 0,00</td>';

                            // Kolom 22 End

                            // Kolom 23 Start
                            if($sasaran_target_satuan_rp_realisasi_kolom_6_k)
                            {
                                if((int) $sasaran_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                {
                                    $kolom_23_k = (array_sum($sasaran_tw_realisasi_kolom_22) / (int) $sasaran_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                } else {
                                    $kolom_23_k = 0;
                                }
                            } else {
                                $kolom_23_k = 0;
                            }

                            $e_78 .= '<td>'.$kolom_23_k.'</td>';
                            $e_78 .= '<td>0,00</td>';
                            // Kolom 23 End
                        $e_78 .='</tr>';
                    } else {
                        $e_78 .= '<tr>';
                            $e_78 .= '<td></td>';
                            $e_78 .= '<td></td>';
                            $e_78 .= '<td></td>';
                            $e_78 .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                            $e_78 .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            // Kolom 6 Start

                            $sasaran_target_satuan_rp_realisasi_kolom_6_k = SasaranTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                            if($sasaran_target_satuan_rp_realisasi_kolom_6_k)
                            {
                                $e_78 .= '<td>'.$sasaran_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            } else {
                                $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            }

                            $e_78 .= '<td>Rp. 0,00</td>';

                            // Kolom 6 End

                            // Kolom 7 - 11 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $e_78 .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                } else {
                                    $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0, 00</td>';
                                }
                            }

                            // Kolom 7 - 11 End

                            // Kolom 12 - 16 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                        ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                            $q->where('id', $sasaran_indikator_kinerja->id);
                                                                        })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->first();
                                    if($cek_sasaran_tw_realisasi)
                                    {
                                        $get_sasaran_tw_realisasis = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->get();
                                        $sasaran_tw_realisasi = [];
                                        foreach ($get_sasaran_tw_realisasis as $get_sasaran_tw_realisasi) {
                                            $sasaran_tw_realisasi[] = $get_sasaran_tw_realisasi->realisasi;
                                        }
                                        $e_78 .= '<td>'.array_sum($sasaran_tw_realisasi).'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $e_78 .= '<td>Rp. 0, 00</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $e_78 .= '<td>Rp. 0,00</td>';
                                    }
                                } else {
                                    $e_78 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                }
                            }

                            // Kolom 12 - 16 End

                            // Kolom 17 - 21 Start

                            foreach ($tahuns as $tahun) {
                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                            ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                $q->where('id', $sasaran_indikator_kinerja->id);
                                                                            })->first();
                                if($cek_sasaran_target_satuan_rp_realisasi)
                                {
                                    $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->first();

                                    $sasaran_tw_realisasi = [];

                                    if($cek_sasaran_tw_realisasi)
                                    {
                                        $get_sasaran_tw_realisasis = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)->get();
                                        foreach ($get_sasaran_tw_realisasis as $get_sasaran_tw_realisasi) {
                                            $sasaran_tw_realisasi[] = $get_sasaran_tw_realisasi->realisasi;
                                        }
                                    } else {
                                        $sasaran_tw_realisasi[] = 0;
                                    }

                                    if((int) $cek_sasaran_target_satuan_rp_realisasi->target != 0)
                                    {
                                        $kolom_17_21_k = (array_sum($sasaran_tw_realisasi) / (int) $cek_sasaran_target_satuan_rp_realisasi->target) * 100;
                                        $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                    } else {
                                        $e_78 .= '<td>0,00</td>';
                                    }

                                    $e_78 .= '<td>Rp. 0,00</td>';

                                } else {
                                    $e_78 .= '<td>0,00</td>';
                                    $e_78 .= '<td>Rp. 0,00</td>';
                                }
                            }

                            // Kolom 17 - 21 End

                            // Kolom 22 Start

                            $last_sasaran_target_satuan_rp_realisasi_kolom_22 = SasaranTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                ->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                                    $q->where('id', $sasaran_indikator_kinerja->id);
                                                                                })->first();
                            $sasaran_tw_realisasi_kolom_22 = [];
                            if($last_sasaran_target_satuan_rp_realisasi_kolom_22)
                            {
                                $cek_sasaran_tw_realisasi_kolom_22 = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $last_sasaran_target_satuan_rp_realisasi_kolom_22->id)->first();
                                if($cek_sasaran_tw_realisasi_kolom_22)
                                {
                                    $get_sasaran_tw_realisasis_kolom_22 = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $last_sasaran_target_satuan_rp_realisasi_kolom_22->id)->get();
                                    foreach ($get_sasaran_tw_realisasis_kolom_22 as $get_sasaran_tw_realisasi_kolom_22) {
                                        $sasaran_tw_realisasi_kolom_22[] = $get_sasaran_tw_realisasi_kolom_22->realisasi;
                                    }
                                } else {
                                    $sasaran_tw_realisasi_kolom_22[] = 0;
                                }
                            } else {
                                $sasaran_tw_realisasi_kolom_22[] = 0;
                            }
                            $e_78 .= '<td>'.array_sum($sasaran_tw_realisasi_kolom_22).'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                            $e_78 .= '<td>Rp. 0,00</td>';

                            // Kolom 22 End

                            // Kolom 23 Start
                            if($sasaran_target_satuan_rp_realisasi_kolom_6_k)
                            {
                                if((int) $sasaran_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                {
                                    $kolom_23_k = (array_sum($sasaran_tw_realisasi_kolom_22) / (int) $sasaran_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                } else {
                                    $kolom_23_k = 0;
                                }
                            } else {
                                $kolom_23_k = 0;
                            }

                            $e_78 .= '<td>'.$kolom_23_k.'</td>';
                            $e_78 .= '<td>0,00</td>';
                            // Kolom 23 End
                        $e_78 .='</tr>';
                    }
                    $indikator_a++;
                }

            $get_programs = Program::whereHas('program_rpjmd', function($q) use ($sasaran) {
                $q->where('status_program', 'Prioritas');
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran) {
                        $q->whereHas('sasaran', function($q) use ($sasaran) {
                            $q->where('id', $sasaran['id']);
                        });
                    });
                });
            })->get();
            $programs = [];
            foreach ($get_programs as $get_program) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
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
            $e_78 .= '<tr>';
                $e_78 .= '<td></td>';
                $e_78 .= '<td></td>';
                $b = 1;
                foreach ($programs as $program) {
                    if($b == 1)
                    {
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                        // Indikator Program Start
                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                        $c = 1;
                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                            if($c == 1)
                            {
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    // Kolom 6 Start
                                    $program_target_satuan_rp_realisasi_kolom_6_k = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        $e_78 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                    }

                                    $kolom_6_rp = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_6_rp = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_6_rp)
                                        {
                                            $kolom_6_rp[] = $cek_program_target_satuan_rp_realisasi_kolom_6_rp->target_rp;
                                        } else {
                                            $kolom_6_rp[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp'.number_format(array_sum($kolom_6_rp), 2, ',', '.').'</td>';

                                    // Kolom 6 End

                                    // Kolom 7 - 11 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_78 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }

                                    // Kolom 7 - 11 End

                                    // Kolom 12 - 16 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                $program_tw_realisasi = [];
                                                $program_tw_realisasi_rp = [];
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 12 - 16 End

                                    // Kolom 17 - 21 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();

                                            $program_tw_realisasi = [];
                                            $program_tw_realisasi_rp = [];

                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $program_tw_realisasi[] = 0;
                                                $program_tw_realisasi_rp[] = 0;
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target != 0)
                                            {
                                                $kolom_17_21_k = (array_sum($program_tw_realisasi) / (int) $cek_program_target_satuan_rp_realisasi->target) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0,00</td>';
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target_rp != 0)
                                            {
                                                $kolom_17_21_rp = (array_sum($program_tw_realisasi_rp) / (int) $cek_program_target_satuan_rp_realisasi->target_rp) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }

                                        } else {
                                            $e_78 .= '<td>0,00</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 17 - 21 End

                                    // Kolom 22 Start

                                    $last_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    $program_tw_realisasi_kolom_22 = [];
                                    if($last_program_target_satuan_rp_realisasi_kolom_22)
                                    {
                                        $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->first();
                                        if($cek_program_tw_realisasi_kolom_22)
                                        {
                                            $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                            foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                $program_tw_realisasi_kolom_22[] = $get_program_tw_realisasi_kolom_22->realisasi;
                                            }
                                        } else {
                                            $program_tw_realisasi_kolom_22[] = 0;
                                        }
                                    } else {
                                        $program_tw_realisasi_kolom_22[] = 0;
                                    }
                                    $e_78 .= '<td>'.array_sum($program_tw_realisasi_kolom_22).'/'.$program_indikator_kinerja->satuan.'</td>';

                                    $realisasi_rp_kolom_22 = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_22)
                                        {
                                            $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->first();

                                            if($cek_program_tw_realisasi_kolom_22)
                                            {
                                                $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                                foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                    $realisasi_rp_kolom_22[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $realisasi_rp_kolom_22[] = 0;
                                            }
                                        } else {
                                            $realisasi_rp_kolom_22[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp. '.number_format(array_sum($realisasi_rp_kolom_22), 2, ',', '.').'</td>';

                                    // Kolom 22 End

                                    // Kolom 23 Start
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        if((int) $program_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                        {
                                            $kolom_23_k = (array_sum($program_tw_realisasi_kolom_22) / (int) $program_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                        } else {
                                            $kolom_23_k = 0;
                                        }
                                    } else {
                                        $kolom_23_k = 0;
                                    }

                                    if(array_sum($kolom_6_rp) != 0)
                                    {
                                        $kolom_23_rp = (array_sum($realisasi_rp_kolom_22) / array_sum($kolom_6_rp)) * 100;
                                    } else {
                                        $kolom_23_rp = 0;
                                    }

                                    $e_78 .= '<td>'.$kolom_23_k.'</td>';
                                    $e_78 .= '<td>'.number_format($kolom_23_rp, 2, ',', '.').'</td>';
                                    // Kolom 23 End
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    // Kolom 6 Start
                                    $program_target_satuan_rp_realisasi_kolom_6_k = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        $e_78 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                    }

                                    $kolom_6_rp = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_6_rp = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_6_rp)
                                        {
                                            $kolom_6_rp[] = $cek_program_target_satuan_rp_realisasi_kolom_6_rp->target_rp;
                                        } else {
                                            $kolom_6_rp[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp'.number_format(array_sum($kolom_6_rp), 2, ',', '.').'</td>';

                                    // Kolom 6 End

                                    // Kolom 7 - 11 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_78 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }

                                    // Kolom 7 - 11 End

                                    // Kolom 12 - 16 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                $program_tw_realisasi = [];
                                                $program_tw_realisasi_rp = [];
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 12 - 16 End

                                    // Kolom 17 - 21 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();

                                            $program_tw_realisasi = [];
                                            $program_tw_realisasi_rp = [];

                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $program_tw_realisasi[] = 0;
                                                $program_tw_realisasi_rp[] = 0;
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target != 0)
                                            {
                                                $kolom_17_21_k = (array_sum($program_tw_realisasi) / (int) $cek_program_target_satuan_rp_realisasi->target) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0,00</td>';
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target_rp != 0)
                                            {
                                                $kolom_17_21_rp = (array_sum($program_tw_realisasi_rp) / (int) $cek_program_target_satuan_rp_realisasi->target_rp) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }

                                        } else {
                                            $e_78 .= '<td>0,00</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 17 - 21 End

                                    // Kolom 22 Start

                                    $last_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    $program_tw_realisasi_kolom_22 = [];
                                    if($last_program_target_satuan_rp_realisasi_kolom_22)
                                    {
                                        $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->first();
                                        if($cek_program_tw_realisasi_kolom_22)
                                        {
                                            $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                            foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                $program_tw_realisasi_kolom_22[] = $get_program_tw_realisasi_kolom_22->realisasi;
                                            }
                                        } else {
                                            $program_tw_realisasi_kolom_22[] = 0;
                                        }
                                    } else {
                                        $program_tw_realisasi_kolom_22[] = 0;
                                    }
                                    $e_78 .= '<td>'.array_sum($program_tw_realisasi_kolom_22).'/'.$program_indikator_kinerja->satuan.'</td>';

                                    $realisasi_rp_kolom_22 = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_22)
                                        {
                                            $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->first();

                                            if($cek_program_tw_realisasi_kolom_22)
                                            {
                                                $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                                foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                    $realisasi_rp_kolom_22[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $realisasi_rp_kolom_22[] = 0;
                                            }
                                        } else {
                                            $realisasi_rp_kolom_22[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp. '.number_format(array_sum($realisasi_rp_kolom_22), 2, ',', '.').'</td>';

                                    // Kolom 22 End

                                    // Kolom 23 Start
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        if((int) $program_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                        {
                                            $kolom_23_k = (array_sum($program_tw_realisasi_kolom_22) / (int) $program_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                        } else {
                                            $kolom_23_k = 0;
                                        }
                                    } else {
                                        $kolom_23_k = 0;
                                    }

                                    if(array_sum($kolom_6_rp) != 0)
                                    {
                                        $kolom_23_rp = (array_sum($realisasi_rp_kolom_22) / array_sum($kolom_6_rp)) * 100;
                                    } else {
                                        $kolom_23_rp = 0;
                                    }

                                    $e_78 .= '<td>'.$kolom_23_k.'</td>';
                                    $e_78 .= '<td>'.number_format($kolom_23_rp, 2, ',', '.').'</td>';
                                    // Kolom 23 End
                                $e_78 .= '</tr>';
                            }
                            $c++;
                        }
                    } else {
                        $e_78 .= '<tr>';
                        $e_78 .= '<td></td>';
                        $e_78 .= '<td></td>';
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                        // Indikator Program Start
                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                        $c = 1;
                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                            if($c == 1)
                            {
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    // Kolom 6 Start
                                    $program_target_satuan_rp_realisasi_kolom_6_k = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        $e_78 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                    }

                                    $kolom_6_rp = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_6_rp = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_6_rp)
                                        {
                                            $kolom_6_rp[] = $cek_program_target_satuan_rp_realisasi_kolom_6_rp->target_rp;
                                        } else {
                                            $kolom_6_rp[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp'.number_format(array_sum($kolom_6_rp), 2, ',', '.').'</td>';

                                    // Kolom 6 End

                                    // Kolom 7 - 11 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_78 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }

                                    // Kolom 7 - 11 End

                                    // Kolom 12 - 16 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                $program_tw_realisasi = [];
                                                $program_tw_realisasi_rp = [];
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 12 - 16 End

                                    // Kolom 17 - 21 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();

                                            $program_tw_realisasi = [];
                                            $program_tw_realisasi_rp = [];

                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $program_tw_realisasi[] = 0;
                                                $program_tw_realisasi_rp[] = 0;
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target != 0)
                                            {
                                                $kolom_17_21_k = (array_sum($program_tw_realisasi) / (int) $cek_program_target_satuan_rp_realisasi->target) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0,00</td>';
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target_rp != 0)
                                            {
                                                $kolom_17_21_rp = (array_sum($program_tw_realisasi_rp) / (int) $cek_program_target_satuan_rp_realisasi->target_rp) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }

                                        } else {
                                            $e_78 .= '<td>0,00</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 17 - 21 End

                                    // Kolom 22 Start

                                    $last_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    $program_tw_realisasi_kolom_22 = [];
                                    if($last_program_target_satuan_rp_realisasi_kolom_22)
                                    {
                                        $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->first();
                                        if($cek_program_tw_realisasi_kolom_22)
                                        {
                                            $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                            foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                $program_tw_realisasi_kolom_22[] = $get_program_tw_realisasi_kolom_22->realisasi;
                                            }
                                        } else {
                                            $program_tw_realisasi_kolom_22[] = 0;
                                        }
                                    } else {
                                        $program_tw_realisasi_kolom_22[] = 0;
                                    }
                                    $e_78 .= '<td>'.array_sum($program_tw_realisasi_kolom_22).'/'.$program_indikator_kinerja->satuan.'</td>';

                                    $realisasi_rp_kolom_22 = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_22)
                                        {
                                            $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->first();

                                            if($cek_program_tw_realisasi_kolom_22)
                                            {
                                                $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                                foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                    $realisasi_rp_kolom_22[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $realisasi_rp_kolom_22[] = 0;
                                            }
                                        } else {
                                            $realisasi_rp_kolom_22[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp. '.number_format(array_sum($realisasi_rp_kolom_22), 2, ',', '.').'</td>';

                                    // Kolom 22 End

                                    // Kolom 23 Start
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        if((int) $program_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                        {
                                            $kolom_23_k = (array_sum($program_tw_realisasi_kolom_22) / (int) $program_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                        } else {
                                            $kolom_23_k = 0;
                                        }
                                    } else {
                                        $kolom_23_k = 0;
                                    }

                                    if(array_sum($kolom_6_rp) != 0)
                                    {
                                        $kolom_23_rp = (array_sum($realisasi_rp_kolom_22) / array_sum($kolom_6_rp)) * 100;
                                    } else {
                                        $kolom_23_rp = 0;
                                    }

                                    $e_78 .= '<td>'.$kolom_23_k.'</td>';
                                    $e_78 .= '<td>'.number_format($kolom_23_rp, 2, ',', '.').'</td>';
                                    // Kolom 23 End
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    // Kolom 6 Start
                                    $program_target_satuan_rp_realisasi_kolom_6_k = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        $e_78 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_6_k->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                    }

                                    $kolom_6_rp = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_6_rp = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_6_rp)
                                        {
                                            $kolom_6_rp[] = $cek_program_target_satuan_rp_realisasi_kolom_6_rp->target_rp;
                                        } else {
                                            $kolom_6_rp[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp'.number_format(array_sum($kolom_6_rp), 2, ',', '.').'</td>';

                                    // Kolom 6 End

                                    // Kolom 7 - 11 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_78 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }

                                    // Kolom 7 - 11 End

                                    // Kolom 12 - 16 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                $program_tw_realisasi = [];
                                                $program_tw_realisasi_rp = [];
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }
                                        } else {
                                            $e_78 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 12 - 16 End

                                    // Kolom 17 - 21 Start

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();

                                            $program_tw_realisasi = [];
                                            $program_tw_realisasi_rp = [];

                                            if($cek_program_tw_realisasi)
                                            {
                                                $get_program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                foreach ($get_program_tw_realisasis as $get_program_tw_realisasi) {
                                                    $program_tw_realisasi[] = $get_program_tw_realisasi->realisasi;
                                                    $program_tw_realisasi_rp[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $program_tw_realisasi[] = 0;
                                                $program_tw_realisasi_rp[] = 0;
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target != 0)
                                            {
                                                $kolom_17_21_k = (array_sum($program_tw_realisasi) / (int) $cek_program_target_satuan_rp_realisasi->target) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_k, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>0,00</td>';
                                            }

                                            if((int) $cek_program_target_satuan_rp_realisasi->target_rp != 0)
                                            {
                                                $kolom_17_21_rp = (array_sum($program_tw_realisasi_rp) / (int) $cek_program_target_satuan_rp_realisasi->target_rp) * 100;
                                                $e_78 .= '<td>'.number_format($kolom_17_21_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $e_78 .= '<td>Rp. 0,00</td>';
                                            }

                                        } else {
                                            $e_78 .= '<td>0,00</td>';
                                            $e_78 .= '<td>Rp. 0,00</td>';
                                        }
                                    }

                                    // Kolom 17 - 21 End

                                    // Kolom 22 Start

                                    $last_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                    $program_tw_realisasi_kolom_22 = [];
                                    if($last_program_target_satuan_rp_realisasi_kolom_22)
                                    {
                                        $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->first();
                                        if($cek_program_tw_realisasi_kolom_22)
                                        {
                                            $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $last_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                            foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                $program_tw_realisasi_kolom_22[] = $get_program_tw_realisasi_kolom_22->realisasi;
                                            }
                                        } else {
                                            $program_tw_realisasi_kolom_22[] = 0;
                                        }
                                    } else {
                                        $program_tw_realisasi_kolom_22[] = 0;
                                    }
                                    $e_78 .= '<td>'.array_sum($program_tw_realisasi_kolom_22).'/'.$program_indikator_kinerja->satuan.'</td>';

                                    $realisasi_rp_kolom_22 = [];
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi_kolom_22 = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi_kolom_22)
                                        {
                                            $cek_program_tw_realisasi_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->first();

                                            if($cek_program_tw_realisasi_kolom_22)
                                            {
                                                $get_program_tw_realisasis_kolom_22 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi_kolom_22->id)->get();
                                                foreach ($get_program_tw_realisasis_kolom_22 as $get_program_tw_realisasi_kolom_22) {
                                                    $realisasi_rp_kolom_22[] = $get_program_tw_realisasi->realisasi_rp;
                                                }
                                            } else {
                                                $realisasi_rp_kolom_22[] = 0;
                                            }
                                        } else {
                                            $realisasi_rp_kolom_22[] = 0;
                                        }
                                    }
                                    $e_78 .= '<td>Rp. '.number_format(array_sum($realisasi_rp_kolom_22), 2, ',', '.').'</td>';

                                    // Kolom 22 End

                                    // Kolom 23 Start
                                    if($program_target_satuan_rp_realisasi_kolom_6_k)
                                    {
                                        if((int) $program_target_satuan_rp_realisasi_kolom_6_k->target != 0)
                                        {
                                            $kolom_23_k = (array_sum($program_tw_realisasi_kolom_22) / (int) $program_target_satuan_rp_realisasi_kolom_6_k->target) * 100;
                                        } else {
                                            $kolom_23_k = 0;
                                        }
                                    } else {
                                        $kolom_23_k = 0;
                                    }

                                    if(array_sum($kolom_6_rp) != 0)
                                    {
                                        $kolom_23_rp = (array_sum($realisasi_rp_kolom_22) / array_sum($kolom_6_rp)) * 100;
                                    } else {
                                        $kolom_23_rp = 0;
                                    }

                                    $e_78 .= '<td>'.$kolom_23_k.'</td>';
                                    $e_78 .= '<td>'.number_format($kolom_23_rp, 2, ',', '.').'</td>';
                                    // Kolom 23 End
                                $e_78 .= '</tr>';
                            }
                            $c++;
                        }
                    }
                    $b++;
                }
        }

         // E 78 End

        $pdf = PDF::loadView('admin.laporan.e-78', [
            'e_78' => $e_78
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan E-78.pdf');
    }

    public function e_78_ekspor_excel()
    {
        return Excel::download(new E78Ekspor, 'Laporan E - 78.xlsx');
    }
}
