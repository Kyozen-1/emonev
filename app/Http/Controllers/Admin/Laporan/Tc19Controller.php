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
use App\Exports\Tc19Ekspor;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\SubKegiatanTwRealisasi;
use App\Models\MasterTw;

class Tc19Controller extends Controller
{
    public function tc_19()
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

        // TC 19 Start
        $tc_19 = '';

        $get_urusans = Urusan::orderBy('kode', 'asc')->get();
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
        $a = 1;

        $tws = MasterTw::all();

        foreach ($urusans as $urusan) {
            $tc_19 .= '<tr>';
                $tc_19 .= '<td>'.$a++.'</td>';
                $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td style="text-align:left">'.$urusan['deskripsi'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
            $tc_19 .= '</tr>';

            $get_programs = Program::where('urusan_id', $urusan['id'])->orderBy('kode', 'asc')->get();
            $programs = [];
            foreach ($get_programs as $get_program) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program)->latest()->first();
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

            foreach ($programs as $program) {
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td>'.$a++.'</td>';
                    $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                    $tc_19 .= '<td>'.$program['kode'].'</td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td style="text-align:left">'.$program['deskripsi'].'</td>';
                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                    $b = 1;
                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                        if($b == 1)
                        {
                                $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                // Opd Program Indikator
                                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                $c = 1;
                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            // Kolom 5 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi->target;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi->satuan;
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $tc_19 .= '<td>'.array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End
                                            // Kolom 6 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                $program_realisasi = [];
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                    foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                        $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 6 End
                                            // Kolom 7 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 7 End
                                            // Kolom 8 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                                $tc_19 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td>'.array_sum($program_tw_realisasi_realisasi) / $cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 8 End
                                            // Kolom 9 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }

                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 9 End
                                            // Kolom 10 Start
                                            $cek_program_target_satuan_rp_realisasi_tahun_lalu = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi_tahun_lalu)
                                            {
                                                $cek_program_tw_realisasi_tahun_lalu = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                $program_realisasi_tahun_lalu = [];
                                                if($cek_program_tw_realisasi_tahun_lalu)
                                                {
                                                    $program_tw_realisasi_tahun_lalus = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                    foreach ($program_tw_realisasi_tahun_lalus as $program_tw_realisasi_tahun_lalu) {
                                                        $program_realisasi_tahun_lalu[] = $program_tw_realisasi_tahun_lalu->realisasi;
                                                    }
                                                } else {
                                                    $program_realisasi_tahun_lalu[] = 0;
                                                }
                                            } else {
                                                $program_realisasi_tahun_lalu[] = 0;
                                            }

                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                            } else {
                                                $program_tw_realisasi_realisasi[] = 0;
                                                $program_tw_realisasi_satuan[] = 0;
                                            }
                                            $tc_19 .= '<td>'.array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 10 End
                                            // Kolom 11 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target : 0;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->satuan : '';
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : '';
                                            }
                                            if((array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) != 0)
                                            {
                                                $tc_19 .= '<td>'.(array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) / array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 11 End
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 5 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi->target;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi->satuan;
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $tc_19 .= '<td>'.array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End
                                            // Kolom 6 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                $program_realisasi = [];
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                    foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                        $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 6 End
                                            // Kolom 7 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 7 End
                                            // Kolom 8 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                                $tc_19 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 8 End
                                            // Kolom 9 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }

                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 9 End
                                            // Kolom 10 Start
                                            $cek_program_target_satuan_rp_realisasi_tahun_lalu = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi_tahun_lalu)
                                            {
                                                $cek_program_tw_realisasi_tahun_lalu = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                $program_realisasi_tahun_lalu = [];
                                                if($cek_program_tw_realisasi_tahun_lalu)
                                                {
                                                    $program_tw_realisasi_tahun_lalus = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                    foreach ($program_tw_realisasi_tahun_lalus as $program_tw_realisasi_tahun_lalu) {
                                                        $program_realisasi_tahun_lalu[] = $program_tw_realisasi_tahun_lalu->realisasi;
                                                    }
                                                } else {
                                                    $program_realisasi_tahun_lalu[] = 0;
                                                }
                                            } else {
                                                $program_realisasi_tahun_lalu[] = 0;
                                            }

                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                            } else {
                                                $program_tw_realisasi_realisasi[] = 0;
                                                $program_tw_realisasi_satuan[] = 0;
                                            }
                                            $tc_19 .= '<td>'.array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 10 End
                                            // Kolom 11 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target : 0;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->satuan : '';
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : '';
                                            }
                                            if((array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) != 0)
                                            {
                                                $tc_19 .= '<td>'.(array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) / array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 11 End
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        } else {
                            $tc_19 .= '<tr>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                // Opd Program Indikator
                                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                $c = 1;
                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            // Kolom 5 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi->target;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi->satuan;
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $tc_19 .= '<td>'.array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End
                                            // Kolom 6 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                $program_realisasi = [];
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                    foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                        $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 6 End
                                            // Kolom 7 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 7 End
                                            // Kolom 8 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                                $tc_19 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 8 End
                                            // Kolom 9 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }

                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 9 End
                                            // Kolom 10 Start
                                            $cek_program_target_satuan_rp_realisasi_tahun_lalu = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi_tahun_lalu)
                                            {
                                                $cek_program_tw_realisasi_tahun_lalu = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                $program_realisasi_tahun_lalu = [];
                                                if($cek_program_tw_realisasi_tahun_lalu)
                                                {
                                                    $program_tw_realisasi_tahun_lalus = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                    foreach ($program_tw_realisasi_tahun_lalus as $program_tw_realisasi_tahun_lalu) {
                                                        $program_realisasi_tahun_lalu[] = $program_tw_realisasi_tahun_lalu->realisasi;
                                                    }
                                                } else {
                                                    $program_realisasi_tahun_lalu[] = 0;
                                                }
                                            } else {
                                                $program_realisasi_tahun_lalu[] = 0;
                                            }

                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                            } else {
                                                $program_tw_realisasi_realisasi[] = 0;
                                                $program_tw_realisasi_satuan[] = 0;
                                            }
                                            $tc_19 .= '<td>'.array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 10 End
                                            // Kolom 11 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target : 0;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->satuan : '';
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : '';
                                            }
                                            if((array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) != 0)
                                            {
                                                $tc_19 .= '<td>'.(array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) / array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 11 End
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 5 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi->target;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi->satuan;
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $tc_19 .= '<td>'.array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End
                                            // Kolom 6 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                $program_realisasi = [];
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                    foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                        $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 6 End
                                            // Kolom 7 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 7 End
                                            // Kolom 8 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                                $tc_19 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 8 End
                                            // Kolom 9 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }

                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 9 End
                                            // Kolom 10 Start
                                            $cek_program_target_satuan_rp_realisasi_tahun_lalu = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi_tahun_lalu)
                                            {
                                                $cek_program_tw_realisasi_tahun_lalu = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                $program_realisasi_tahun_lalu = [];
                                                if($cek_program_tw_realisasi_tahun_lalu)
                                                {
                                                    $program_tw_realisasi_tahun_lalus = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                    foreach ($program_tw_realisasi_tahun_lalus as $program_tw_realisasi_tahun_lalu) {
                                                        $program_realisasi_tahun_lalu[] = $program_tw_realisasi_tahun_lalu->realisasi;
                                                    }
                                                } else {
                                                    $program_realisasi_tahun_lalu[] = 0;
                                                }
                                            } else {
                                                $program_realisasi_tahun_lalu[] = 0;
                                            }

                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                            } else {
                                                $program_tw_realisasi_realisasi[] = 0;
                                                $program_tw_realisasi_satuan[] = 0;
                                            }
                                            $tc_19 .= '<td>'.array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 10 End
                                            // Kolom 11 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target : 0;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->satuan : '';
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : '';
                                            }
                                            if((array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) != 0)
                                            {
                                                $tc_19 .= '<td>'.(array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) / array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 11 End
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        }
                        $b++;
                    }
                $get_kegiatans = Kegiatan::where('program_id',$program['id'])->orderBy('kode', 'asc')->get();
                $kegiatans = [];
                foreach ($get_kegiatans as $get_kegiatan) {
                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
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

                foreach($kegiatans as $kegiatan)
                {
                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td>'.$a++.'</td>';
                        $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                        $tc_19 .= '<td>'.$program['kode'].'</td>';
                        $tc_19 .= '<td>'.$kegiatan['kode'].'</td>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td style="text-align:left">'.$kegiatan['deskripsi'].'</td>';
                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                        $d = 1;
                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                            if($d == 1)
                            {
                                    $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                    // OPD Kegiatan Indikator Kinerja
                                    $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                    $e = 1;
                                    foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                        if($e == 1)
                                        {
                                                // Kolom 5 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : '';
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }

                                                $tc_19 .= '<td>'.array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End
                                                // Kolom 6 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_kegiatan_tw_realisasi = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $kegiatan_realisasi = [];
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $kegiatan_tw_realisasies = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                        }
                                                        $tc_19 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 6 End
                                                // Kolom 7 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $tc_19 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 7 End
                                                // Kolom 8 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 8 End
                                                // Kolom 9 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }

                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 9 End
                                                // Kolom 10 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu)
                                                {
                                                    $cek_kegiatan_tw_realisasi_tahun_lalu = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                    $kegiatan_realisasi_tahun_lalu = [];
                                                    if($cek_kegiatan_tw_realisasi_tahun_lalu)
                                                    {
                                                        $kegiatan_tw_realisasi_tahun_lalus = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                        foreach ($kegiatan_tw_realisasi_tahun_lalus as $kegiatan_tw_realisasi_tahun_lalu) {
                                                            $kegiatan_realisasi_tahun_lalu[] = $kegiatan_tw_realisasi_tahun_lalu->realisasi;
                                                        }
                                                    } else {
                                                        $kegiatan_realisasi_tahun_lalu[] = 0;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_tahun_lalu[] = 0;
                                                }

                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                } else {
                                                    $kegiatan_tw_realisasi_realisasi[] = 0;
                                                    $kegiatan_tw_realisasi_satuan[] = 0;
                                                }
                                                $tc_19 .= '<td>'.array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 10 End
                                                // Kolom 11 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : 0;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }
                                                if((array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) != 0)
                                                {
                                                    if(array_sum($kegiatan_target_satuan_rp_realisasi_target) != 0)
                                                    {
                                                        $tc_19 .= '<td>'.(array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) / array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 11 End
                                                $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                $tc_19 .= '<td></td>';
                                            $tc_19 .= '</tr>';
                                        } else {
                                            $tc_19 .= '<tr>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 5 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi->target;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi->satuan;
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $tc_19 .= '<td>'.array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End
                                                // Kolom 6 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_kegiatan_tw_realisasi = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $kegiatan_realisasi = [];
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $kegiatan_tw_realisasies = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                        }
                                                        $tc_19 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 6 End
                                                // Kolom 7 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $tc_19 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 7 End
                                                // Kolom 8 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 8 End
                                                // Kolom 9 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }

                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 9 End
                                                // Kolom 10 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu)
                                                {
                                                    $cek_kegiatan_tw_realisasi_tahun_lalu = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                    $kegiatan_realisasi_tahun_lalu = [];
                                                    if($cek_kegiatan_tw_realisasi_tahun_lalu)
                                                    {
                                                        $kegiatan_tw_realisasi_tahun_lalus = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                        foreach ($kegiatan_tw_realisasi_tahun_lalus as $kegiatan_tw_realisasi_tahun_lalu) {
                                                            $kegiatan_realisasi_tahun_lalu[] = $kegiatan_tw_realisasi_tahun_lalu->realisasi;
                                                        }
                                                    } else {
                                                        $kegiatan_realisasi_tahun_lalu[] = 0;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_tahun_lalu[] = 0;
                                                }

                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                } else {
                                                    $kegiatan_tw_realisasi_realisasi[] = 0;
                                                    $kegiatan_tw_realisasi_satuan[] = 0;
                                                }
                                                $tc_19 .= '<td>'.array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 10 End
                                                // Kolom 11 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : 0;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }
                                                if((array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) != 0)
                                                {
                                                    $tc_19 .= '<td>'.(array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) / array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 11 End
                                                $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                $tc_19 .= '<td></td>';
                                            $tc_19 .= '</tr>';
                                        }
                                        $e++;
                                    }
                            } else {
                                $tc_19 .= '<tr>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                    // OPD Kegiatan Indikator Kinerja
                                    $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                    $e = 1;
                                    foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                        if($e == 1)
                                        {
                                                // Kolom 5 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : '';
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }

                                                $tc_19 .= '<td>'.array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End
                                                // Kolom 6 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_kegiatan_tw_realisasi = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $kegiatan_realisasi = [];
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $kegiatan_tw_realisasies = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                        }
                                                        $tc_19 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 6 End
                                                // Kolom 7 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $tc_19 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 7 End
                                                // Kolom 8 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 8 End
                                                // Kolom 9 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }

                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 9 End
                                                // Kolom 10 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu)
                                                {
                                                    $cek_kegiatan_tw_realisasi_tahun_lalu = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                    $kegiatan_realisasi_tahun_lalu = [];
                                                    if($cek_kegiatan_tw_realisasi_tahun_lalu)
                                                    {
                                                        $kegiatan_tw_realisasi_tahun_lalus = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                        foreach ($kegiatan_tw_realisasi_tahun_lalus as $kegiatan_tw_realisasi_tahun_lalu) {
                                                            $kegiatan_realisasi_tahun_lalu[] = $kegiatan_tw_realisasi_tahun_lalu->realisasi;
                                                        }
                                                    } else {
                                                        $kegiatan_realisasi_tahun_lalu[] = 0;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_tahun_lalu[] = 0;
                                                }

                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                } else {
                                                    $kegiatan_tw_realisasi_realisasi[] = 0;
                                                    $kegiatan_tw_realisasi_satuan[] = 0;
                                                }
                                                $tc_19 .= '<td>'.array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 10 End
                                                // Kolom 11 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : 0;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }
                                                if((array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) != 0)
                                                {
                                                    if(array_sum($kegiatan_target_satuan_rp_realisasi_target) != 0)
                                                    {
                                                        $tc_19 .= '<td>'.(array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) / array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 11 End
                                                $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                $tc_19 .= '<td></td>';
                                            $tc_19 .= '</tr>';
                                        } else {
                                            $tc_19 .= '<tr>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 5 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi->target;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi->satuan;
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $tc_19 .= '<td>'.array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End
                                                // Kolom 6 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_kegiatan_tw_realisasi = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $kegiatan_realisasi = [];
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $kegiatan_tw_realisasies = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                        }
                                                        $tc_19 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 6 End
                                                // Kolom 7 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $tc_19 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 7 End
                                                // Kolom 8 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 8 End
                                                // Kolom 9 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }

                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 9 End
                                                // Kolom 10 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu)
                                                {
                                                    $cek_kegiatan_tw_realisasi_tahun_lalu = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                    $kegiatan_realisasi_tahun_lalu = [];
                                                    if($cek_kegiatan_tw_realisasi_tahun_lalu)
                                                    {
                                                        $kegiatan_tw_realisasi_tahun_lalus = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                        foreach ($kegiatan_tw_realisasi_tahun_lalus as $kegiatan_tw_realisasi_tahun_lalu) {
                                                            $kegiatan_realisasi_tahun_lalu[] = $kegiatan_tw_realisasi_tahun_lalu->realisasi;
                                                        }
                                                    } else {
                                                        $kegiatan_realisasi_tahun_lalu[] = 0;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_tahun_lalu[] = 0;
                                                }

                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                } else {
                                                    $kegiatan_tw_realisasi_realisasi[] = 0;
                                                    $kegiatan_tw_realisasi_satuan[] = 0;
                                                }
                                                $tc_19 .= '<td>'.array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 10 End
                                                // Kolom 11 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : 0;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }
                                                if((array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) != 0)
                                                {
                                                    $tc_19 .= '<td>'.(array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) / array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 11 End
                                                $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                $tc_19 .= '<td></td>';
                                            $tc_19 .= '</tr>';
                                        }
                                        $e++;
                                    }
                                $tc_19 .= '</tr>';
                            }
                            $d++;
                        }
                }
            }
        }
        // TC 19 End

        return response()->json(['tc_19' => $tc_19]);
    }

    public function laporan_tc_19(Request $request)
    {
        $tahun_awal = $request->tahun;
        // TC 19 Start
        $tc_19 = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $jarak_tahun = $get_periode->tahun_akhir - $get_periode->tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tws = MasterTw::all();

        $get_urusans = Urusan::orderBy('kode', 'asc')->get();
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
        $a = 1;

        foreach ($urusans as $urusan) {
            $tc_19 .= '<tr>';
                $tc_19 .= '<td>'.$a++.'</td>';
                $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td style="text-align:left">'.$urusan['deskripsi'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
            $tc_19 .= '</tr>';

            $get_programs = Program::where('urusan_id', $urusan['id'])->orderBy('kode', 'asc')->get();
            $programs = [];
            foreach ($get_programs as $get_program) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program)->latest()->first();
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

            foreach ($programs as $program) {
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td>'.$a++.'</td>';
                    $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                    $tc_19 .= '<td>'.$program['kode'].'</td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td style="text-align:left">'.$program['deskripsi'].'</td>';
                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                    $b = 1;
                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                        if($b == 1)
                        {
                                $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                // Opd Program Indikator
                                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                $c = 1;
                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            // Kolom 5 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target : 0;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->satuan : '';
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                            }

                                            $tc_19 .= '<td>'.array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End
                                            // Kolom 6 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                $program_realisasi = [];
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                    foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                        $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 6 End
                                            // Kolom 7 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 7 End
                                            // Kolom 8 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                                $tc_19 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td>'.array_sum($program_tw_realisasi_realisasi) / $cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 8 End
                                            // Kolom 9 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }

                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 9 End
                                            // Kolom 10 Start
                                            $cek_program_target_satuan_rp_realisasi_tahun_lalu = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi_tahun_lalu)
                                            {
                                                $cek_program_tw_realisasi_tahun_lalu = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                $program_realisasi_tahun_lalu = [];
                                                if($cek_program_tw_realisasi_tahun_lalu)
                                                {
                                                    $program_tw_realisasi_tahun_lalus = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                    foreach ($program_tw_realisasi_tahun_lalus as $program_tw_realisasi_tahun_lalu) {
                                                        $program_realisasi_tahun_lalu[] = $program_tw_realisasi_tahun_lalu->realisasi;
                                                    }
                                                } else {
                                                    $program_realisasi_tahun_lalu[] = 0;
                                                }
                                            } else {
                                                $program_realisasi_tahun_lalu[] = 0;
                                            }

                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                            } else {
                                                $program_tw_realisasi_realisasi[] = 0;
                                                $program_tw_realisasi_satuan[] = 0;
                                            }
                                            $tc_19 .= '<td>'.array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 10 End
                                            // Kolom 11 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target : 0;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->satuan : '';
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : '';
                                            }
                                            if((array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) != 0)
                                            {
                                                $tc_19 .= '<td>'.(array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) / array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 11 End
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 5 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi->target;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi->satuan;
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $tc_19 .= '<td>'.array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End
                                            // Kolom 6 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                $program_realisasi = [];
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                    foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                        $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 6 End
                                            // Kolom 7 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 7 End
                                            // Kolom 8 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                                $tc_19 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 8 End
                                            // Kolom 9 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }

                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 9 End
                                            // Kolom 10 Start
                                            $cek_program_target_satuan_rp_realisasi_tahun_lalu = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi_tahun_lalu)
                                            {
                                                $cek_program_tw_realisasi_tahun_lalu = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                $program_realisasi_tahun_lalu = [];
                                                if($cek_program_tw_realisasi_tahun_lalu)
                                                {
                                                    $program_tw_realisasi_tahun_lalus = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                    foreach ($program_tw_realisasi_tahun_lalus as $program_tw_realisasi_tahun_lalu) {
                                                        $program_realisasi_tahun_lalu[] = $program_tw_realisasi_tahun_lalu->realisasi;
                                                    }
                                                } else {
                                                    $program_realisasi_tahun_lalu[] = 0;
                                                }
                                            } else {
                                                $program_realisasi_tahun_lalu[] = 0;
                                            }

                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                            } else {
                                                $program_tw_realisasi_realisasi[] = 0;
                                                $program_tw_realisasi_satuan[] = 0;
                                            }
                                            $tc_19 .= '<td>'.array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 10 End
                                            // Kolom 11 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target : 0;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->satuan : '';
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : '';
                                            }
                                            if((array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) != 0)
                                            {
                                                $tc_19 .= '<td>'.(array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) / array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 11 End
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        } else {
                            $tc_19 .= '<tr>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                // Opd Program Indikator
                                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                $c = 1;
                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            // Kolom 5 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target : 0;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->satuan : '';
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                            }

                                            $tc_19 .= '<td>'.array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End
                                            // Kolom 6 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                $program_realisasi = [];
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                    foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                        $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 6 End
                                            // Kolom 7 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 7 End
                                            // Kolom 8 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                                $tc_19 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 8 End
                                            // Kolom 9 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }

                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 9 End
                                            // Kolom 10 Start
                                            $cek_program_target_satuan_rp_realisasi_tahun_lalu = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi_tahun_lalu)
                                            {
                                                $cek_program_tw_realisasi_tahun_lalu = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                $program_realisasi_tahun_lalu = [];
                                                if($cek_program_tw_realisasi_tahun_lalu)
                                                {
                                                    $program_tw_realisasi_tahun_lalus = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                    foreach ($program_tw_realisasi_tahun_lalus as $program_tw_realisasi_tahun_lalu) {
                                                        $program_realisasi_tahun_lalu[] = $program_tw_realisasi_tahun_lalu->realisasi;
                                                    }
                                                } else {
                                                    $program_realisasi_tahun_lalu[] = 0;
                                                }
                                            } else {
                                                $program_realisasi_tahun_lalu[] = 0;
                                            }

                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                            } else {
                                                $program_tw_realisasi_realisasi[] = 0;
                                                $program_tw_realisasi_satuan[] = 0;
                                            }
                                            $tc_19 .= '<td>'.array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 10 End
                                            // Kolom 11 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target : 0;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->satuan : '';
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : '';
                                            }
                                            if((array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) != 0)
                                            {
                                                $tc_19 .= '<td>'.(array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) / array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 11 End
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 5 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi->target;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi->satuan;
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                            }

                                            $tc_19 .= '<td>'.array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End
                                            // Kolom 6 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                $program_realisasi = [];
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                    foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                        $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 6 End
                                            // Kolom 7 Start
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 7 End
                                            // Kolom 8 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                                $tc_19 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 8 End
                                            // Kolom 9 Start
                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }

                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 9 End
                                            // Kolom 10 Start
                                            $cek_program_target_satuan_rp_realisasi_tahun_lalu = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal - 1)->first();

                                            if($cek_program_target_satuan_rp_realisasi_tahun_lalu)
                                            {
                                                $cek_program_tw_realisasi_tahun_lalu = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                $program_realisasi_tahun_lalu = [];
                                                if($cek_program_tw_realisasi_tahun_lalu)
                                                {
                                                    $program_tw_realisasi_tahun_lalus = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                    foreach ($program_tw_realisasi_tahun_lalus as $program_tw_realisasi_tahun_lalu) {
                                                        $program_realisasi_tahun_lalu[] = $program_tw_realisasi_tahun_lalu->realisasi;
                                                    }
                                                } else {
                                                    $program_realisasi_tahun_lalu[] = 0;
                                                }
                                            } else {
                                                $program_realisasi_tahun_lalu[] = 0;
                                            }

                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun_awal)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_tw_realisasi_realisasi = [];
                                                $program_tw_realisasi_satuan = [];
                                                foreach ($tws as $tw) {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : '';
                                                    $program_tw_realisasi_satuan[] = $cek_program_tw_realisasi ? $cek_program_tw_realisasi->satuan : '';
                                                }
                                            } else {
                                                $program_tw_realisasi_realisasi[] = 0;
                                                $program_tw_realisasi_satuan[] = 0;
                                            }
                                            $tc_19 .= '<td>'.array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $tc_19 .= '<td></td>';
                                            // Kolom 10 End
                                            // Kolom 11 Start
                                            $program_target_satuan_rp_realisasi_target = [];
                                            $program_indikator_kinerja_satuan = [];
                                            $program_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $program_target_satuan_rp_realisasi_target[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target : 0;
                                                $program_indikator_kinerja_satuan[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->satuan : '';
                                                $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : '';
                                            }
                                            if((array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) != 0)
                                            {
                                                $tc_19 .= '<td>'.(array_sum($program_realisasi_tahun_lalu) + array_sum($program_tw_realisasi_realisasi)) / array_sum($program_target_satuan_rp_realisasi_target).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            // Kolom 11 End
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        }
                        $b++;
                    }
                $get_kegiatans = Kegiatan::where('program_id',$program['id'])->orderBy('kode', 'asc')->get();
                $kegiatans = [];
                foreach ($get_kegiatans as $get_kegiatan) {
                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
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

                foreach($kegiatans as $kegiatan)
                {
                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td>'.$a++.'</td>';
                        $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                        $tc_19 .= '<td>'.$program['kode'].'</td>';
                        $tc_19 .= '<td>'.$kegiatan['kode'].'</td>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td style="text-align:left">'.$kegiatan['deskripsi'].'</td>';
                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                        $d = 1;
                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                            if($d == 1)
                            {
                                    $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                    // OPD Kegiatan Indikator Kinerja
                                    $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                    $e = 1;
                                    foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                        if($e == 1)
                                        {
                                                // Kolom 5 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : '';
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }

                                                $tc_19 .= '<td>'.array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End
                                                // Kolom 6 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_kegiatan_tw_realisasi = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $kegiatan_realisasi = [];
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $kegiatan_tw_realisasies = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                        }
                                                        $tc_19 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 6 End
                                                // Kolom 7 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $tc_19 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 7 End
                                                // Kolom 8 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 8 End
                                                // Kolom 9 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }

                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 9 End
                                                // Kolom 10 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu)
                                                {
                                                    $cek_kegiatan_tw_realisasi_tahun_lalu = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                    $kegiatan_realisasi_tahun_lalu = [];
                                                    if($cek_kegiatan_tw_realisasi_tahun_lalu)
                                                    {
                                                        $kegiatan_tw_realisasi_tahun_lalus = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                        foreach ($kegiatan_tw_realisasi_tahun_lalus as $kegiatan_tw_realisasi_tahun_lalu) {
                                                            $kegiatan_realisasi_tahun_lalu[] = $kegiatan_tw_realisasi_tahun_lalu->realisasi;
                                                        }
                                                    } else {
                                                        $kegiatan_realisasi_tahun_lalu[] = 0;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_tahun_lalu[] = 0;
                                                }

                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                } else {
                                                    $kegiatan_tw_realisasi_realisasi[] = 0;
                                                    $kegiatan_tw_realisasi_satuan[] = 0;
                                                }
                                                $tc_19 .= '<td>'.array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 10 End
                                                // Kolom 11 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : 0;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }
                                                if((array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) != 0)
                                                {
                                                    if(array_sum($kegiatan_target_satuan_rp_realisasi_target) != 0)
                                                    {
                                                        $tc_19 .= '<td>'.(array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) / array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 11 End
                                                $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                $tc_19 .= '<td></td>';
                                            $tc_19 .= '</tr>';
                                        } else {
                                            $tc_19 .= '<tr>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 5 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi->target;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi->satuan;
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $tc_19 .= '<td>'.array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End
                                                // Kolom 6 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_kegiatan_tw_realisasi = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $kegiatan_realisasi = [];
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $kegiatan_tw_realisasies = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                        }
                                                        $tc_19 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 6 End
                                                // Kolom 7 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $tc_19 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 7 End
                                                // Kolom 8 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 8 End
                                                // Kolom 9 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }

                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 9 End
                                                // Kolom 10 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu)
                                                {
                                                    $cek_kegiatan_tw_realisasi_tahun_lalu = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                    $kegiatan_realisasi_tahun_lalu = [];
                                                    if($cek_kegiatan_tw_realisasi_tahun_lalu)
                                                    {
                                                        $kegiatan_tw_realisasi_tahun_lalus = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                        foreach ($kegiatan_tw_realisasi_tahun_lalus as $kegiatan_tw_realisasi_tahun_lalu) {
                                                            $kegiatan_realisasi_tahun_lalu[] = $kegiatan_tw_realisasi_tahun_lalu->realisasi;
                                                        }
                                                    } else {
                                                        $kegiatan_realisasi_tahun_lalu[] = 0;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_tahun_lalu[] = 0;
                                                }

                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                } else {
                                                    $kegiatan_tw_realisasi_realisasi[] = 0;
                                                    $kegiatan_tw_realisasi_satuan[] = 0;
                                                }
                                                $tc_19 .= '<td>'.array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 10 End
                                                // Kolom 11 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : 0;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }
                                                if((array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) != 0)
                                                {
                                                    $tc_19 .= '<td>'.(array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) / array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 11 End
                                                $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                $tc_19 .= '<td></td>';
                                            $tc_19 .= '</tr>';
                                        }
                                        $e++;
                                    }
                            } else {
                                $tc_19 .= '<tr>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td></td>';
                                    $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                    // OPD Kegiatan Indikator Kinerja
                                    $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                    $e = 1;
                                    foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                        if($e == 1)
                                        {
                                                // Kolom 5 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : '';
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }

                                                $tc_19 .= '<td>'.array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End
                                                // Kolom 6 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_kegiatan_tw_realisasi = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $kegiatan_realisasi = [];
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $kegiatan_tw_realisasies = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                        }
                                                        $tc_19 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 6 End
                                                // Kolom 7 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $tc_19 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 7 End
                                                // Kolom 8 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 8 End
                                                // Kolom 9 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }

                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 9 End
                                                // Kolom 10 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu)
                                                {
                                                    $cek_kegiatan_tw_realisasi_tahun_lalu = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                    $kegiatan_realisasi_tahun_lalu = [];
                                                    if($cek_kegiatan_tw_realisasi_tahun_lalu)
                                                    {
                                                        $kegiatan_tw_realisasi_tahun_lalus = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                        foreach ($kegiatan_tw_realisasi_tahun_lalus as $kegiatan_tw_realisasi_tahun_lalu) {
                                                            $kegiatan_realisasi_tahun_lalu[] = $kegiatan_tw_realisasi_tahun_lalu->realisasi;
                                                        }
                                                    } else {
                                                        $kegiatan_realisasi_tahun_lalu[] = 0;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_tahun_lalu[] = 0;
                                                }

                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                } else {
                                                    $kegiatan_tw_realisasi_realisasi[] = 0;
                                                    $kegiatan_tw_realisasi_satuan[] = 0;
                                                }
                                                $tc_19 .= '<td>'.array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 10 End
                                                // Kolom 11 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : 0;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }
                                                if((array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) != 0)
                                                {
                                                    if(array_sum($kegiatan_target_satuan_rp_realisasi_target) != 0)
                                                    {
                                                        $tc_19 .= '<td>'.(array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) / array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 11 End
                                                $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                $tc_19 .= '<td></td>';
                                            $tc_19 .= '</tr>';
                                        } else {
                                            $tc_19 .= '<tr>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 5 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi->target;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi->satuan;
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi->target_rp;
                                                }

                                                $tc_19 .= '<td>'.array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End
                                                // Kolom 6 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_kegiatan_tw_realisasi = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $kegiatan_realisasi = [];
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $kegiatan_tw_realisasies = kegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                        }
                                                        $tc_19 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_19 .= '<td></td>';
                                                    } else {
                                                        $tc_19 .= '<td></td>';
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 6 End
                                                // Kolom 7 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $tc_19 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 7 End
                                                // Kolom 8 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                    $tc_19 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 8 End
                                                // Kolom 9 Start
                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }

                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 9 End
                                                // Kolom 10 Start
                                                $cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal - 1)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu)
                                                {
                                                    $cek_kegiatan_tw_realisasi_tahun_lalu = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->first();
                                                    $kegiatan_realisasi_tahun_lalu = [];
                                                    if($cek_kegiatan_tw_realisasi_tahun_lalu)
                                                    {
                                                        $kegiatan_tw_realisasi_tahun_lalus = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_tahun_lalu->id)->get();
                                                        foreach ($kegiatan_tw_realisasi_tahun_lalus as $kegiatan_tw_realisasi_tahun_lalu) {
                                                            $kegiatan_realisasi_tahun_lalu[] = $kegiatan_tw_realisasi_tahun_lalu->realisasi;
                                                        }
                                                    } else {
                                                        $kegiatan_realisasi_tahun_lalu[] = 0;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_tahun_lalu[] = 0;
                                                }

                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun_awal)->first();

                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_tw_realisasi_realisasi = [];
                                                    $kegiatan_tw_realisasi_satuan = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : '';
                                                        $kegiatan_tw_realisasi_satuan[] = $cek_kegiatan_tw_realisasi ? $cek_kegiatan_tw_realisasi->satuan : '';
                                                    }
                                                } else {
                                                    $kegiatan_tw_realisasi_realisasi[] = 0;
                                                    $kegiatan_tw_realisasi_satuan[] = 0;
                                                }
                                                $tc_19 .= '<td>'.array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $tc_19 .= '<td></td>';
                                                // Kolom 10 End
                                                // Kolom 11 Start
                                                $kegiatan_target_satuan_rp_realisasi_target = [];
                                                $kegiatan_indikator_kinerja_satuan = [];
                                                $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $kegiatan_target_satuan_rp_realisasi_target[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target : 0;
                                                    $kegiatan_indikator_kinerja_satuan[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->satuan : '';
                                                    $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : '';
                                                }
                                                if((array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) != 0)
                                                {
                                                    $tc_19 .= '<td>'.(array_sum($kegiatan_realisasi_tahun_lalu) + array_sum($kegiatan_tw_realisasi_realisasi)) / array_sum($kegiatan_target_satuan_rp_realisasi_target).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                } else {
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                }
                                                // Kolom 11 End
                                                $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                $tc_19 .= '<td></td>';
                                            $tc_19 .= '</tr>';
                                        }
                                        $e++;
                                    }
                                $tc_19 .= '</tr>';
                            }
                            $d++;
                        }
                }
            }
        }
        // TC 19 End

        return response()->json(['tc_19' => $tc_19]);
    }

    public function tc_19_ekspor_pdf($tahun)
    {
        $tahun_awal = $tahun;
        // TC 19 Start
        $tc_19 = '';

        $get_urusans = Urusan::orderBy('kode', 'asc')->get();
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
        $a = 1;

        foreach ($urusans as $urusan) {
            $tc_19 .= '<tr>';
                $tc_19 .= '<td>'.$a++.'</td>';
                $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td style="text-align:left">'.$urusan['deskripsi'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
            $tc_19 .= '</tr>';

            $get_programs = Program::where('urusan_id', $urusan['id'])->orderBy('kode', 'asc')->get();
            $programs = [];
            foreach ($get_programs as $get_program) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program)->latest()->first();
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

            foreach ($programs as $program) {
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td>'.$a++.'</td>';
                    $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                    $tc_19 .= '<td>'.$program['kode'].'</td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td style="text-align:left">'.$program['deskripsi'].'</td>';
                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                    $b = 1;
                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                        if($b == 1)
                        {
                                $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                // Opd Program Indikator
                                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                $c = 1;
                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                        // Target Program Indikator Kinerja
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            } else{
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            // Target Program Indikator Kinerja
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            } else{
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        } else {
                            $tc_19 .= '<tr>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                // Opd Program Indikator
                                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                $c = 1;
                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                        // Target Program Indikator Kinerja
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            } else{
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            // Target Program Indikator Kinerja
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            } else{
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        }
                        $b++;
                    }
                    $get_kegiatans = Kegiatan::where('program_id',$program['id'])->orderBy('kode', 'asc')->get();
                    $kegiatans = [];
                    foreach ($get_kegiatans as $get_kegiatan) {
                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
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

                    foreach($kegiatans as $kegiatan)
                    {
                        $tc_19 .= '<tr>';
                            $tc_19 .= '<td>'.$a++.'</td>';
                            $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                            $tc_19 .= '<td>'.$program['kode'].'</td>';
                            $tc_19 .= '<td>'.$kegiatan['kode'].'</td>';
                            $tc_19 .= '<td></td>';
                            $tc_19 .= '<td style="text-align:left">'.$kegiatan['deskripsi'].'</td>';
                            $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                            $d = 1;
                            foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                if($d == 1)
                                {
                                        $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                        // OPD Kegiatan Indikator Kinerja
                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                        $e = 1;
                                        foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                            if($e == 1)
                                            {
                                                    // Kegiatan Target Realisasi
                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun_awal)->first();
                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    } else{
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                $tc_19 .= '</tr>';
                                            } else {
                                                $tc_19 .= '<tr>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun_awal)->first();
                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    } else{
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                $tc_19 .= '</tr>';
                                            }
                                            $e++;
                                        }
                                } else {
                                    $tc_19 .= '<tr>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                        // OPD Kegiatan Indikator Kinerja
                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                        $e = 1;
                                        foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                            if($e == 1)
                                            {
                                                    // Kegiatan Target Realisasi
                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun_awal)->first();
                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    } else{
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                $tc_19 .= '</tr>';
                                            } else {
                                                $tc_19 .= '<tr>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun_awal)->first();
                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    } else{
                                                        $tc_19 .= '<td></td>';
                                                    }
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td></td>';
                                                    $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                    $tc_19 .= '<td></td>';
                                                $tc_19 .= '</tr>';
                                            }
                                            $e++;
                                        }
                                }
                                $d++;
                            }
                    }
            }
        }
        // TC 19 End

        $pdf = PDF::loadView('admin.laporan.tc-19', [
            'tc_19' => $tc_19,
            'tahun' => $tahun
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan TC-19.pdf');
    }

    public function tc_19_ekspor_excel($tahun)
    {
        return Excel::download(new Tc19Ekspor($tahun), 'Laporan TC-19.xlsx');
    }
}
