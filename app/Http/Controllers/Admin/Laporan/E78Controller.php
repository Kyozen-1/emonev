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
        $get_sasarans = Sasaran::all();
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
                                    $indikator_c = 1;
                                    $len_c = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->get();
                                                $e_78_program_target = [];
                                                $e_78_program_target_rp = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                    $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $e_78_program_target = [];
                                            $e_78_program_target_rp = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $tingkat_capaian = (array_sum($program_tw_realisasi) / $cek_program_target_satuan_rp_realisasi->target) * 100;
                                            $e_78 .= '<td>'.number_format($tingkat_capaian, 2, ',', '.').'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                                $program_tw_realisasi = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                    }
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }
                                    $e_78 .= '<td></td>';
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    $indikator_c = 1;
                                    $len_c = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->get();
                                                $e_78_program_target = [];
                                                $e_78_program_target_rp = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                    $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $e_78_program_target = [];
                                            $e_78_program_target_rp = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $tingkat_capaian = (array_sum($program_tw_realisasi) / $cek_program_target_satuan_rp_realisasi->target) * 100;
                                            $e_78 .= '<td>'.number_format($tingkat_capaian, 2, ',', '.').'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                                $program_tw_realisasi = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                    }
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }
                                    $e_78 .= '<td></td>';
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
                                    $indikator_c = 1;
                                    $len_c = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->get();
                                                $e_78_program_target = [];
                                                $e_78_program_target_rp = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                    $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $e_78_program_target = [];
                                            $e_78_program_target_rp = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $tingkat_capaian = (array_sum($program_tw_realisasi) / $cek_program_target_satuan_rp_realisasi->target) * 100;
                                            $e_78 .= '<td>'.number_format($tingkat_capaian, 2, ',', '.').'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                                $program_tw_realisasi = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                    }
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }
                                    $e_78 .= '<td></td>';
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    $indikator_c = 1;
                                    $len_c = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->get();
                                                $e_78_program_target = [];
                                                $e_78_program_target_rp = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                    $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $e_78_program_target = [];
                                            $e_78_program_target_rp = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $tingkat_capaian = (array_sum($program_tw_realisasi) / $cek_program_target_satuan_rp_realisasi->target) * 100;
                                            $e_78 .= '<td>'.number_format($tingkat_capaian, 2, ',', '.').'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                                $program_tw_realisasi = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                    }
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }
                                    $e_78 .= '<td></td>';
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
        $get_sasarans = Sasaran::all();
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
                                    $indikator_c = 1;
                                    $len_c = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->get();
                                                $e_78_program_target = [];
                                                $e_78_program_target_rp = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                    $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $e_78_program_target = [];
                                            $e_78_program_target_rp = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $tingkat_capaian = (array_sum($program_tw_realisasi) / $cek_program_target_satuan_rp_realisasi->target) * 100;
                                            $e_78 .= '<td>'.number_format($tingkat_capaian, 2, ',', '.').'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                                $program_tw_realisasi = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                    }
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }
                                    $e_78 .= '<td></td>';
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    $indikator_c = 1;
                                    $len_c = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->get();
                                                $e_78_program_target = [];
                                                $e_78_program_target_rp = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                    $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $e_78_program_target = [];
                                            $e_78_program_target_rp = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $tingkat_capaian = (array_sum($program_tw_realisasi) / $cek_program_target_satuan_rp_realisasi->target) * 100;
                                            $e_78 .= '<td>'.number_format($tingkat_capaian, 2, ',', '.').'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                                $program_tw_realisasi = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                    }
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }
                                    $e_78 .= '<td></td>';
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
                                    $indikator_c = 1;
                                    $len_c = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->get();
                                                $e_78_program_target = [];
                                                $e_78_program_target_rp = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                    $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $e_78_program_target = [];
                                            $e_78_program_target_rp = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $tingkat_capaian = (array_sum($program_tw_realisasi) / $cek_program_target_satuan_rp_realisasi->target) * 100;
                                            $e_78 .= '<td>'.number_format($tingkat_capaian, 2, ',', '.').'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                                $program_tw_realisasi = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                    }
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }
                                    $e_78 .= '<td></td>';
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Program Target Satuan Rp Realisasi
                                    $e_78 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                    $indikator_c = 1;
                                    $len_c = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->get();
                                                $e_78_program_target = [];
                                                $e_78_program_target_rp = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                    $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $e_78_program_target = [];
                                            $e_78_program_target_rp = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $e_78_program_target[] = $program_target_satuan_rp_realisasi->target;
                                                $e_78_program_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $e_78 .= '<td>'.array_sum($e_78_program_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td>Rp'.number_format(array_sum($e_78_program_target_rp), 2, ',', '.').'</td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                            $program_tw_realisasi = [];
                                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                }
                                            }
                                            $tingkat_capaian = (array_sum($program_tw_realisasi) / $cek_program_target_satuan_rp_realisasi->target) * 100;
                                            $e_78 .= '<td>'.number_format($tingkat_capaian, 2, ',', '.').'</td>';
                                            $e_78 .= '<td></td>';
                                        } else {
                                            $e_78 .= '<td></td>';
                                            $e_78 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            if($indikator_c == $len_c)
                                            {
                                                $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                        ->whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                                $q->where('id', $program_indikator_kinerja->id);
                                                                                            });
                                                                                        })->get();
                                                $program_tw_realisasi = [];
                                                foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_realisasi[] = $cek_program_tw_realisasi->realisasi;
                                                    }
                                                }
                                                $e_78 .= '<td>'.array_sum($program_tw_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        } else {
                                            if($indikator_c == $len_c)
                                            {
                                                $e_78 .= '<td></td>';
                                                $e_78 .= '<td></td>';
                                            }
                                        }
                                        $indikator_c++;
                                    }
                                    $e_78 .= '<td></td>';
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
