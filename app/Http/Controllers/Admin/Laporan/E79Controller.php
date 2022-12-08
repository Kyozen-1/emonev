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
use App\Exports\E79Ekspor;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\SubKegiatanTwRealisasi;

class E79Controller extends Controller
{
    public function e_79()
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

        // E 79 Start
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
        $e_79 = '';
        $get_opd = [];
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_79 .= '<tr>';
                $e_79 .= '<td>'.$a++.'</td>';
                $e_79 .= '<td>'.$sasaran['deskripsi'].'</td>';
                $e_79 .= '<td>'.$sasaran['kode'].'</td>';

                $get_urusans = Urusan::whereHas('program', function($q) use ($sasaran){
                    $q->whereHas('program_rpjmd', function($q) use ($sasaran){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                $q->whereHas('sasaran', function($q) use ($sasaran){
                                    $q->where('id', $sasaran['id']);
                                });
                            });
                        });
                    });
                })->get();
                $urusans = [];
                foreach ($get_urusans as $get_urusan) {
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        $urusans[] = [
                            'id' => $cek_perubahan_urusan->urusan_id,
                            'kode' => $cek_perubahan_urusan->kode,
                            'deskripsi' => $cek_perubahan_urusan->deskripsi,
                        ];
                    } else {
                        $urusans[] = [
                            'id' => $get_urusan->id,
                            'kode' => $get_urusan->kode,
                            'deskripsi' => $get_urusan->deskripsi,
                        ];
                    }
                }

                $b = 1;
                foreach ($urusans as $urusan) {
                    if($b == 1)
                    {
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        // Start Program
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                                $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                    $q->whereHas('sasaran', function($q) use ($sasaran){
                                        $q->where('id', $sasaran['id']);
                                    });
                                });
                            });
                        })->get();

                        $programs = [];

                        foreach ($get_programs as $get_program) {
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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

                        foreach ($programs as $program) {
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    }
                                    $c++;
                                }

                            $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
                            $kegiatans = [];
                            foreach ($get_kegiatans as $get_kegiatan) {
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatans[] = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                    ];
                                } else {
                                    $kegiatans[] = [
                                        'id' => $get_kegiatan->id,
                                        'kode' => $get_kegiatan->kode,
                                        'deskripsi' => $get_kegiatan->deskripsi,
                                    ];
                                }
                            }

                            foreach ($kegiatans as $kegiatan) {
                                $e_79 .= '<tr>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                    $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$program['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';

                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                    $e = 1;
                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                        if($e == 1)
                                        {
                                                $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                // Opd Kegiatan Indikator Kinerja
                                                $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                $f = 1;
                                                foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                    if($f == 1)
                                                    {
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    } else {
                                                        $e_79 .= '<tr>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                            ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    }
                                                    $f++;
                                                }
                                        } else {
                                            $e_79 .= '<tr>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                // Opd Kegiatan Indikator Kinerja
                                                $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                $f = 1;
                                                foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                    if($f == 1)
                                                    {
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    } else {
                                                        $e_79 .= '<tr>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                            ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    }
                                                    $f++;
                                                }
                                            $e_79 .= '</tr>';
                                        }
                                        $e++;
                                    }
                            }
                        }

                    } else {
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                                $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                    $q->whereHas('sasaran', function($q) use ($sasaran){
                                        $q->where('id', $sasaran['id']);
                                    });
                                });
                            });
                        })->get();

                        $programs = [];

                        foreach ($get_programs as $get_program) {
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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

                        foreach ($programs as $program) {
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    }
                                    $c++;
                                }

                                foreach ($kegiatans as $kegiatan) {
                                    $e_79 .= '<tr>';
                                        $e_79 .= '<td></td>';
                                        $e_79 .= '<td></td>';
                                        $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                        $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                        $e_79 .= '<td>'.$program['kode'].'</td>';
                                        $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                        $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';

                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                        $e = 1;
                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                            if($e == 1)
                                            {
                                                    $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                    // Opd Kegiatan Indikator Kinerja
                                                    $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                    $f = 1;
                                                    foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                        if($f == 1)
                                                        {
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        } else {
                                                            $e_79 .= '<tr>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        }
                                                        $f++;
                                                    }
                                            } else {
                                                $e_79 .= '<tr>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                    // Opd Kegiatan Indikator Kinerja
                                                    $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                    $f = 1;
                                                    foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                        if($f == 1)
                                                        {
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        } else {
                                                            $e_79 .= '<tr>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        }
                                                        $f++;
                                                    }
                                                $e_79 .= '</tr>';
                                            }
                                            $e++;
                                        }
                                }
                        }
                    }
                    $b++;
                }
        }
        // E 79 End

        return response()->json(['e_79' => $e_79]);
    }

    public function laporan_e_79(Request $request)
    {
        $tahun_awal = $request->tahun;
        // E 79 Start
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
        $e_79 = '';
        $get_opd = [];
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_79 .= '<tr>';
                $e_79 .= '<td>'.$a++.'</td>';
                $e_79 .= '<td>'.$sasaran['deskripsi'].'</td>';
                $e_79 .= '<td>'.$sasaran['kode'].'</td>';

                $get_urusans = Urusan::whereHas('program', function($q) use ($sasaran){
                    $q->whereHas('program_rpjmd', function($q) use ($sasaran){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                $q->whereHas('sasaran', function($q) use ($sasaran){
                                    $q->where('id', $sasaran['id']);
                                });
                            });
                        });
                    });
                })->get();
                $urusans = [];
                foreach ($get_urusans as $get_urusan) {
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        $urusans[] = [
                            'id' => $cek_perubahan_urusan->urusan_id,
                            'kode' => $cek_perubahan_urusan->kode,
                            'deskripsi' => $cek_perubahan_urusan->deskripsi,
                        ];
                    } else {
                        $urusans[] = [
                            'id' => $get_urusan->id,
                            'kode' => $get_urusan->kode,
                            'deskripsi' => $get_urusan->deskripsi,
                        ];
                    }
                }

                $b = 1;
                foreach ($urusans as $urusan) {
                    if($b == 1)
                    {
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        // Start Program
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                                $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                    $q->whereHas('sasaran', function($q) use ($sasaran){
                                        $q->where('id', $sasaran['id']);
                                    });
                                });
                            });
                        })->get();

                        $programs = [];

                        foreach ($get_programs as $get_program) {
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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

                        foreach ($programs as $program) {
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    }
                                    $c++;
                                }

                            $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
                            $kegiatans = [];
                            foreach ($get_kegiatans as $get_kegiatan) {
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatans[] = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                    ];
                                } else {
                                    $kegiatans[] = [
                                        'id' => $get_kegiatan->id,
                                        'kode' => $get_kegiatan->kode,
                                        'deskripsi' => $get_kegiatan->deskripsi,
                                    ];
                                }
                            }

                            foreach ($kegiatans as $kegiatan) {
                                $e_79 .= '<tr>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                    $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$program['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';

                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                    $e = 1;
                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                        if($e == 1)
                                        {
                                                $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                // Opd Kegiatan Indikator Kinerja
                                                $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                $f = 1;
                                                foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                    if($f == 1)
                                                    {
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    } else {
                                                        $e_79 .= '<tr>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                            ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    }
                                                    $f++;
                                                }
                                        } else {
                                            $e_79 .= '<tr>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                // Opd Kegiatan Indikator Kinerja
                                                $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                $f = 1;
                                                foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                    if($f == 1)
                                                    {
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    } else {
                                                        $e_79 .= '<tr>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                            ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    }
                                                    $f++;
                                                }
                                            $e_79 .= '</tr>';
                                        }
                                        $e++;
                                    }
                            }
                        }

                    } else {
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                                $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                    $q->whereHas('sasaran', function($q) use ($sasaran){
                                        $q->where('id', $sasaran['id']);
                                    });
                                });
                            });
                        })->get();

                        $programs = [];

                        foreach ($get_programs as $get_program) {
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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

                        foreach ($programs as $program) {
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    }
                                    $c++;
                                }

                                foreach ($kegiatans as $kegiatan) {
                                    $e_79 .= '<tr>';
                                        $e_79 .= '<td></td>';
                                        $e_79 .= '<td></td>';
                                        $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                        $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                        $e_79 .= '<td>'.$program['kode'].'</td>';
                                        $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                        $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';

                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                        $e = 1;
                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                            if($e == 1)
                                            {
                                                    $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                    // Opd Kegiatan Indikator Kinerja
                                                    $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                    $f = 1;
                                                    foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                        if($f == 1)
                                                        {
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        } else {
                                                            $e_79 .= '<tr>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        }
                                                        $f++;
                                                    }
                                            } else {
                                                $e_79 .= '<tr>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                    // Opd Kegiatan Indikator Kinerja
                                                    $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                    $f = 1;
                                                    foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                        if($f == 1)
                                                        {
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        } else {
                                                            $e_79 .= '<tr>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        }
                                                        $f++;
                                                    }
                                                $e_79 .= '</tr>';
                                            }
                                            $e++;
                                        }
                                }
                        }
                    }
                    $b++;
                }
        }
        // E 79 End

        return response()->json(['e_79' => $e_79]);
    }

    public function e_79_ekspor_pdf($tahun)
    {
        $tahun_awal = $tahun;
        // E 79 Start
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
        $e_79 = '';
        $get_opd = [];
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_79 .= '<tr>';
                $e_79 .= '<td>'.$a++.'</td>';
                $e_79 .= '<td>'.$sasaran['deskripsi'].'</td>';
                $e_79 .= '<td>'.$sasaran['kode'].'</td>';

                $get_urusans = Urusan::whereHas('program', function($q) use ($sasaran){
                    $q->whereHas('program_rpjmd', function($q) use ($sasaran){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                $q->whereHas('sasaran', function($q) use ($sasaran){
                                    $q->where('id', $sasaran['id']);
                                });
                            });
                        });
                    });
                })->get();
                $urusans = [];
                foreach ($get_urusans as $get_urusan) {
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        $urusans[] = [
                            'id' => $cek_perubahan_urusan->urusan_id,
                            'kode' => $cek_perubahan_urusan->kode,
                            'deskripsi' => $cek_perubahan_urusan->deskripsi,
                        ];
                    } else {
                        $urusans[] = [
                            'id' => $get_urusan->id,
                            'kode' => $get_urusan->kode,
                            'deskripsi' => $get_urusan->deskripsi,
                        ];
                    }
                }

                $b = 1;
                foreach ($urusans as $urusan) {
                    if($b == 1)
                    {
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        // Start Program
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                                $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                    $q->whereHas('sasaran', function($q) use ($sasaran){
                                        $q->where('id', $sasaran['id']);
                                    });
                                });
                            });
                        })->get();

                        $programs = [];

                        foreach ($get_programs as $get_program) {
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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

                        foreach ($programs as $program) {
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    }
                                    $c++;
                                }

                            $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
                            $kegiatans = [];
                            foreach ($get_kegiatans as $get_kegiatan) {
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatans[] = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                    ];
                                } else {
                                    $kegiatans[] = [
                                        'id' => $get_kegiatan->id,
                                        'kode' => $get_kegiatan->kode,
                                        'deskripsi' => $get_kegiatan->deskripsi,
                                    ];
                                }
                            }

                            foreach ($kegiatans as $kegiatan) {
                                $e_79 .= '<tr>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                    $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$program['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';

                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                    $e = 1;
                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                        if($e == 1)
                                        {
                                                $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                // Opd Kegiatan Indikator Kinerja
                                                $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                $f = 1;
                                                foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                    if($f == 1)
                                                    {
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    } else {
                                                        $e_79 .= '<tr>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                            ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    }
                                                    $f++;
                                                }
                                        } else {
                                            $e_79 .= '<tr>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td></td>';
                                                $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                // Opd Kegiatan Indikator Kinerja
                                                $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                $f = 1;
                                                foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                    if($f == 1)
                                                    {
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    } else {
                                                        $e_79 .= '<tr>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                            ->where('tahun', $tahun_awal)->first();
                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td></td>';
                                                            } else {
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                            }
                                                            $e_79 .= '<td colspan="18"></td>';
                                                            $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                        $e_79 .= '</tr>';
                                                    }
                                                    $f++;
                                                }
                                            $e_79 .= '</tr>';
                                        }
                                        $e++;
                                    }
                            }
                        }

                    } else {
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                                $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                    $q->whereHas('sasaran', function($q) use ($sasaran){
                                        $q->where('id', $sasaran['id']);
                                    });
                                });
                            });
                        })->get();

                        $programs = [];

                        foreach ($get_programs as $get_program) {
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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

                        foreach ($programs as $program) {
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td></td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    }
                                    $c++;
                                }

                                foreach ($kegiatans as $kegiatan) {
                                    $e_79 .= '<tr>';
                                        $e_79 .= '<td></td>';
                                        $e_79 .= '<td></td>';
                                        $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                        $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                        $e_79 .= '<td>'.$program['kode'].'</td>';
                                        $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                        $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';

                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                        $e = 1;
                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                            if($e == 1)
                                            {
                                                    $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                    // Opd Kegiatan Indikator Kinerja
                                                    $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                    $f = 1;
                                                    foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                        if($f == 1)
                                                        {
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        } else {
                                                            $e_79 .= '<tr>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        }
                                                        $f++;
                                                    }
                                            } else {
                                                $e_79 .= '<tr>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td></td>';
                                                    $e_79 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                    // Opd Kegiatan Indikator Kinerja
                                                    $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                    $f = 1;
                                                    foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                        if($f == 1)
                                                        {
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        } else {
                                                            $e_79 .= '<tr>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $e_79 .= '<td></td>';
                                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                ->where('tahun', $tahun_awal)->first();
                                                                if($kegiatan_target_satuan_rp_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td></td>';
                                                                } else {
                                                                    $e_79 .= '<td></td>';
                                                                    $e_79 .= '<td></td>';
                                                                }
                                                                $e_79 .= '<td colspan="18"></td>';
                                                                $e_79 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                            $e_79 .= '</tr>';
                                                        }
                                                        $f++;
                                                    }
                                                $e_79 .= '</tr>';
                                            }
                                            $e++;
                                        }
                                }
                        }
                    }
                    $b++;
                }
        }
        // E 79 End

        $pdf = PDF::loadView('admin.laporan.e-79', [
            'e_79' => $e_79,
            'tahun' => $tahun
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan E-79.pdf');
    }

    public function e_79_ekspor_excel($tahun)
    {
        return Excel::download(new E79Ekspor($tahun), 'Laporan E - 79.xlsx');
    }
}
