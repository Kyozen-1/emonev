<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
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
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\SubKegiatanTwRealisasi;
use App\Models\MasterTw;

class E79Ekspor implements FromView
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        $jarak_tahun = $get_periode->tahun_akhir - $get_periode->tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tahun_awal = $this->tahun;
        // E 79 Start
        $get_sasarans = Sasaran::where('tahun_periode_id', $get_periode->id)->get();
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
        $tws = MasterTw::all();
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
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

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q){
                                    $q->whereHas('program_target_satuan_rp_realisasi');
                                })->get();
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
                                                        // Kolom 6 Start
                                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();
                                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();

                                                        if($last_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        }
                                                        $e_79 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                        // Kolom 6 End

                                                        // Kolom 7 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                            $program_realisasi = [];
                                                            $program_realisasi_rp = [];
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                                foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                                    $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                                    $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                        // Kolom 7 End

                                                        // Kolom 8 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 8 End
                                                        // Kolom 9 - 12 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                if($cek_program_tw_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0,00</td>';
                                                                }
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 9 - 12 End
                                                        // Kolom 13 Start
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $program_tw_realisasi_realisasi = [];
                                                            $program_tw_realisasi_realisasi_rp = [];
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                                            }
                                                            $e_79 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 13 End
                                                        // Kolom 14 Start
                                                        $cek_program_target_satuan_rp_realisasi_kolom_14_7 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();
                                                        $program_realisasi_kolom_14_7 = [];
                                                        $program_realisasi_rp_kolom_14_7 = [];
                                                        if($cek_program_target_satuan_rp_realisasi_kolom_14_7)
                                                        {
                                                            $cek_program_tw_realisasi_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                            if($cek_program_tw_realisasi_kolom_14_7)
                                                            {
                                                                $program_tw_realisasies_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                foreach ($program_tw_realisasies_kolom_14_7 as $program_tw_realisasi_kolom_14_7) {
                                                                    $program_realisasi_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi;
                                                                    $program_realisasi_rp_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                }
                                                            } else {
                                                                $program_realisasi_kolom_14_7[] = 0;
                                                                $program_realisasi_rp_kolom_14_7[] = 0;
                                                            }
                                                        } else {
                                                            $program_realisasi_kolom_14_7[] = 0;
                                                            $program_realisasi_rp_kolom_14_7[] = 0;
                                                        }

                                                        $program_tw_realisasi_realisasi_kolom_14_13 = [];
                                                        $program_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi_kolom_14_13 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                            }
                                                        } else {
                                                            $program_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                            $program_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                        }
                                                        $realisasi_kolom_14 = array_sum($program_realisasi_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_kolom_14_13);
                                                        $realisasi_rp_kolom_14 = array_sum($program_realisasi_rp_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_rp_kolom_14_13);
                                                        $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                        // Kolom 14 End
                                                        // Kolom 15 Start
                                                        $program_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();

                                                            $program_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_program_target_satuan_rp_realisasi_15_6 ? $cek_program_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();
                                                        if($last_program_target_satuan_rp_realisasi_15_6)
                                                        {
                                                            if($last_program_target_satuan_rp_realisasi_15_6->target)
                                                            {
                                                                if((int) $last_program_target_satuan_rp_realisasi_15_6->target != 0)
                                                                {
                                                                    $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_program_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }
                                                        } else {
                                                            $target_kolom_15 = 0;
                                                        }

                                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                        {
                                                            $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($program_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                        } else {
                                                            $target_rp_kolom_15 = 0;
                                                        }

                                                        $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                        $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                        // Kolom 15 End
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
                                                        // Kolom 6 Start
                                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();
                                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();

                                                        if($last_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        }
                                                        $e_79 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                        // Kolom 6 End

                                                        // Kolom 7 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                            $program_realisasi = [];
                                                            $program_realisasi_rp = [];
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                                foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                                    $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                                    $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                        // Kolom 7 End

                                                        // Kolom 8 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 8 End
                                                        // Kolom 9 - 12 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                if($cek_program_tw_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0,00</td>';
                                                                }
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 9 - 12 End
                                                        // Kolom 13 Start
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $program_tw_realisasi_realisasi = [];
                                                            $program_tw_realisasi_realisasi_rp = [];
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                                            }
                                                            $e_79 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 13 End
                                                        // Kolom 14 Start
                                                        $cek_program_target_satuan_rp_realisasi_kolom_14_7 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();
                                                        $program_realisasi_kolom_14_7 = [];
                                                        $program_realisasi_rp_kolom_14_7 = [];
                                                        if($cek_program_target_satuan_rp_realisasi_kolom_14_7)
                                                        {
                                                            $cek_program_tw_realisasi_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                            if($cek_program_tw_realisasi_kolom_14_7)
                                                            {
                                                                $program_tw_realisasies_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                foreach ($program_tw_realisasies_kolom_14_7 as $program_tw_realisasi_kolom_14_7) {
                                                                    $program_realisasi_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi;
                                                                    $program_realisasi_rp_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                }
                                                            } else {
                                                                $program_realisasi_kolom_14_7[] = 0;
                                                                $program_realisasi_rp_kolom_14_7[] = 0;
                                                            }
                                                        } else {
                                                            $program_realisasi_kolom_14_7[] = 0;
                                                            $program_realisasi_rp_kolom_14_7[] = 0;
                                                        }

                                                        $program_tw_realisasi_realisasi_kolom_14_13 = [];
                                                        $program_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi_kolom_14_13 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                            }
                                                        } else {
                                                            $program_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                            $program_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                        }
                                                        $realisasi_kolom_14 = array_sum($program_realisasi_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_kolom_14_13);
                                                        $realisasi_rp_kolom_14 = array_sum($program_realisasi_rp_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_rp_kolom_14_13);
                                                        $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                        // Kolom 14 End
                                                        // Kolom 15 Start
                                                        $program_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();

                                                            $program_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_program_target_satuan_rp_realisasi_15_6 ? $cek_program_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();
                                                        if($last_program_target_satuan_rp_realisasi_15_6)
                                                        {
                                                            if($last_program_target_satuan_rp_realisasi_15_6->target)
                                                            {
                                                                if((int) $last_program_target_satuan_rp_realisasi_15_6->target != 0)
                                                                {
                                                                    $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_program_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }
                                                        } else {
                                                            $target_kolom_15 = 0;
                                                        }

                                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                        {
                                                            $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($program_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                        } else {
                                                            $target_rp_kolom_15 = 0;
                                                        }

                                                        $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                        $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                        // Kolom 15 End
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
                                                        // Kolom 6 Start
                                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();
                                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();

                                                        if($last_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        }
                                                        $e_79 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                        // Kolom 6 End

                                                        // Kolom 7 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                            $program_realisasi = [];
                                                            $program_realisasi_rp = [];
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                                foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                                    $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                                    $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                        // Kolom 7 End

                                                        // Kolom 8 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 8 End
                                                        // Kolom 9 - 12 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                if($cek_program_tw_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0,00</td>';
                                                                }
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 9 - 12 End
                                                        // Kolom 13 Start
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $program_tw_realisasi_realisasi = [];
                                                            $program_tw_realisasi_realisasi_rp = [];
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                                            }
                                                            $e_79 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 13 End
                                                        // Kolom 14 Start
                                                        $cek_program_target_satuan_rp_realisasi_kolom_14_7 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();
                                                        $program_realisasi_kolom_14_7 = [];
                                                        $program_realisasi_rp_kolom_14_7 = [];
                                                        if($cek_program_target_satuan_rp_realisasi_kolom_14_7)
                                                        {
                                                            $cek_program_tw_realisasi_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                            if($cek_program_tw_realisasi_kolom_14_7)
                                                            {
                                                                $program_tw_realisasies_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                foreach ($program_tw_realisasies_kolom_14_7 as $program_tw_realisasi_kolom_14_7) {
                                                                    $program_realisasi_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi;
                                                                    $program_realisasi_rp_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                }
                                                            } else {
                                                                $program_realisasi_kolom_14_7[] = 0;
                                                                $program_realisasi_rp_kolom_14_7[] = 0;
                                                            }
                                                        } else {
                                                            $program_realisasi_kolom_14_7[] = 0;
                                                            $program_realisasi_rp_kolom_14_7[] = 0;
                                                        }

                                                        $program_tw_realisasi_realisasi_kolom_14_13 = [];
                                                        $program_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi_kolom_14_13 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                            }
                                                        } else {
                                                            $program_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                            $program_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                        }
                                                        $realisasi_kolom_14 = array_sum($program_realisasi_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_kolom_14_13);
                                                        $realisasi_rp_kolom_14 = array_sum($program_realisasi_rp_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_rp_kolom_14_13);
                                                        $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                        // Kolom 14 End
                                                        // Kolom 15 Start
                                                        $program_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();

                                                            $program_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_program_target_satuan_rp_realisasi_15_6 ? $cek_program_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();
                                                        if($last_program_target_satuan_rp_realisasi_15_6)
                                                        {
                                                            if($last_program_target_satuan_rp_realisasi_15_6->target)
                                                            {
                                                                if((int) $last_program_target_satuan_rp_realisasi_15_6->target != 0)
                                                                {
                                                                    $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_program_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }
                                                        } else {
                                                            $target_kolom_15 = 0;
                                                        }

                                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                        {
                                                            $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($program_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                        } else {
                                                            $target_rp_kolom_15 = 0;
                                                        }

                                                        $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                        $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                        // Kolom 15 End
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
                                                        // Kolom 6 Start
                                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();
                                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();

                                                        if($last_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        }
                                                        $e_79 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                        // Kolom 6 End

                                                        // Kolom 7 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                            $program_realisasi = [];
                                                            $program_realisasi_rp = [];
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                                foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                                    $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                                    $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                        // Kolom 7 End

                                                        // Kolom 8 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 8 End
                                                        // Kolom 9 - 12 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                if($cek_program_tw_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0,00</td>';
                                                                }
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 9 - 12 End
                                                        // Kolom 13 Start
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $program_tw_realisasi_realisasi = [];
                                                            $program_tw_realisasi_realisasi_rp = [];
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                                            }
                                                            $e_79 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 13 End
                                                        // Kolom 14 Start
                                                        $cek_program_target_satuan_rp_realisasi_kolom_14_7 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();
                                                        $program_realisasi_kolom_14_7 = [];
                                                        $program_realisasi_rp_kolom_14_7 = [];
                                                        if($cek_program_target_satuan_rp_realisasi_kolom_14_7)
                                                        {
                                                            $cek_program_tw_realisasi_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                            if($cek_program_tw_realisasi_kolom_14_7)
                                                            {
                                                                $program_tw_realisasies_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                foreach ($program_tw_realisasies_kolom_14_7 as $program_tw_realisasi_kolom_14_7) {
                                                                    $program_realisasi_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi;
                                                                    $program_realisasi_rp_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                }
                                                            } else {
                                                                $program_realisasi_kolom_14_7[] = 0;
                                                                $program_realisasi_rp_kolom_14_7[] = 0;
                                                            }
                                                        } else {
                                                            $program_realisasi_kolom_14_7[] = 0;
                                                            $program_realisasi_rp_kolom_14_7[] = 0;
                                                        }

                                                        $program_tw_realisasi_realisasi_kolom_14_13 = [];
                                                        $program_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi_kolom_14_13 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                            }
                                                        } else {
                                                            $program_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                            $program_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                        }
                                                        $realisasi_kolom_14 = array_sum($program_realisasi_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_kolom_14_13);
                                                        $realisasi_rp_kolom_14 = array_sum($program_realisasi_rp_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_rp_kolom_14_13);
                                                        $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                        // Kolom 14 End
                                                        // Kolom 15 Start
                                                        $program_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();

                                                            $program_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_program_target_satuan_rp_realisasi_15_6 ? $cek_program_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();
                                                        if($last_program_target_satuan_rp_realisasi_15_6)
                                                        {
                                                            if($last_program_target_satuan_rp_realisasi_15_6->target)
                                                            {
                                                                if((int) $last_program_target_satuan_rp_realisasi_15_6->target != 0)
                                                                {
                                                                    $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_program_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }
                                                        } else {
                                                            $target_kolom_15 = 0;
                                                        }

                                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                        {
                                                            $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($program_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                        } else {
                                                            $target_rp_kolom_15 = 0;
                                                        }

                                                        $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                        $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                        // Kolom 15 End
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

                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                        $q->whereHas('kegiatan_target_satuan_rp_realisasi');
                                    })->get();
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
                                                            // Kolom 6 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();
                                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();

                                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            }
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                            // Kolom 6 End

                                                            // Kolom 7 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                $kegiatan_realisasi = [];
                                                                $kegiatan_realisasi_rp = [];
                                                                if($cek_kegiatan_tw_realisasi)
                                                                {
                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                    }
                                                                    $e_79 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0, 00</td>';
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                            // Kolom 7 End

                                                            // Kolom 8 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 8 End
                                                            // Kolom 9 - 12 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    if($cek_kegiatan_tw_realisasi)
                                                                    {
                                                                        $e_79 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                    } else {
                                                                        $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. 0,00</td>';
                                                                    }
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 9 - 12 End
                                                            // Kolom 13 Start
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $kegiatan_tw_realisasi_realisasi = [];
                                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 13 End
                                                            // Kolom 14 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();
                                                            $kegiatan_realisasi_kolom_14_7 = [];
                                                            $kegiatan_realisasi_rp_kolom_14_7 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7)
                                                            {
                                                                $cek_kegiatan_tw_realisasi_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                                if($cek_kegiatan_tw_realisasi_kolom_14_7)
                                                                {
                                                                    $kegiatan_tw_realisasies_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies_kolom_14_7 as $kegiatan_tw_realisasi_kolom_14_7) {
                                                                        $kegiatan_realisasi_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi;
                                                                        $kegiatan_realisasi_rp_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                    }
                                                                } else {
                                                                    $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                    $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                                }
                                                            } else {
                                                                $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                            }

                                                            $kegiatan_tw_realisasi_realisasi_kolom_14_13 = [];
                                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi_kolom_14_13 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                                }
                                                            } else {
                                                                $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                            }
                                                            $realisasi_kolom_14 = array_sum($kegiatan_realisasi_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_14_13);
                                                            $realisasi_rp_kolom_14 = array_sum($kegiatan_realisasi_rp_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_14_13);
                                                            $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                            // Kolom 14 End
                                                            // Kolom 15 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();

                                                                $kegiatan_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_kegiatan_target_satuan_rp_realisasi_15_6 ? $cek_kegiatan_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();
                                                            if($last_kegiatan_target_satuan_rp_realisasi_15_6)
                                                            {
                                                                if($last_kegiatan_target_satuan_rp_realisasi_15_6->target)
                                                                {
                                                                    if((int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target != 0)
                                                                    {
                                                                        $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                    } else {
                                                                        $target_kolom_15 = 0;
                                                                    }
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }

                                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                            {
                                                                $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                            } else {
                                                                $target_rp_kolom_15 = 0;
                                                            }

                                                            $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                            $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                            // Kolom 15 End
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
                                                            // Kolom 6 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();
                                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();

                                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            }
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                            // Kolom 6 End

                                                            // Kolom 7 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                $kegiatan_realisasi = [];
                                                                $kegiatan_realisasi_rp = [];
                                                                if($cek_kegiatan_tw_realisasi)
                                                                {
                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                    }
                                                                    $e_79 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0, 00</td>';
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                            // Kolom 7 End

                                                            // Kolom 8 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 8 End
                                                            // Kolom 9 - 12 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    if($cek_kegiatan_tw_realisasi)
                                                                    {
                                                                        $e_79 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                    } else {
                                                                        $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. 0,00</td>';
                                                                    }
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 9 - 12 End
                                                            // Kolom 13 Start
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $kegiatan_tw_realisasi_realisasi = [];
                                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 13 End
                                                            // Kolom 14 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();
                                                            $kegiatan_realisasi_kolom_14_7 = [];
                                                            $kegiatan_realisasi_rp_kolom_14_7 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7)
                                                            {
                                                                $cek_kegiatan_tw_realisasi_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                                if($cek_kegiatan_tw_realisasi_kolom_14_7)
                                                                {
                                                                    $kegiatan_tw_realisasies_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies_kolom_14_7 as $kegiatan_tw_realisasi_kolom_14_7) {
                                                                        $kegiatan_realisasi_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi;
                                                                        $kegiatan_realisasi_rp_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                    }
                                                                } else {
                                                                    $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                    $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                                }
                                                            } else {
                                                                $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                            }

                                                            $kegiatan_tw_realisasi_realisasi_kolom_14_13 = [];
                                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi_kolom_14_13 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                                }
                                                            } else {
                                                                $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                            }
                                                            $realisasi_kolom_14 = array_sum($kegiatan_realisasi_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_14_13);
                                                            $realisasi_rp_kolom_14 = array_sum($kegiatan_realisasi_rp_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_14_13);
                                                            $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                            // Kolom 14 End
                                                            // Kolom 15 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();

                                                                $kegiatan_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_kegiatan_target_satuan_rp_realisasi_15_6 ? $cek_kegiatan_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();
                                                            if($last_kegiatan_target_satuan_rp_realisasi_15_6)
                                                            {
                                                                if($last_kegiatan_target_satuan_rp_realisasi_15_6->target)
                                                                {
                                                                    if((int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target != 0)
                                                                    {
                                                                        $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                    } else {
                                                                        $target_kolom_15 = 0;
                                                                    }
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }

                                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                            {
                                                                $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                            } else {
                                                                $target_rp_kolom_15 = 0;
                                                            }

                                                            $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                            $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                            // Kolom 15 End
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
                                                            // Kolom 6 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();
                                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();

                                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            }
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                            // Kolom 6 End

                                                            // Kolom 7 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                $kegiatan_realisasi = [];
                                                                $kegiatan_realisasi_rp = [];
                                                                if($cek_kegiatan_tw_realisasi)
                                                                {
                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                    }
                                                                    $e_79 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0, 00</td>';
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                            // Kolom 7 End

                                                            // Kolom 8 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 8 End
                                                            // Kolom 9 - 12 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    if($cek_kegiatan_tw_realisasi)
                                                                    {
                                                                        $e_79 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                    } else {
                                                                        $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. 0,00</td>';
                                                                    }
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 9 - 12 End
                                                            // Kolom 13 Start
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $kegiatan_tw_realisasi_realisasi = [];
                                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 13 End
                                                            // Kolom 14 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();
                                                            $kegiatan_realisasi_kolom_14_7 = [];
                                                            $kegiatan_realisasi_rp_kolom_14_7 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7)
                                                            {
                                                                $cek_kegiatan_tw_realisasi_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                                if($cek_kegiatan_tw_realisasi_kolom_14_7)
                                                                {
                                                                    $kegiatan_tw_realisasies_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies_kolom_14_7 as $kegiatan_tw_realisasi_kolom_14_7) {
                                                                        $kegiatan_realisasi_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi;
                                                                        $kegiatan_realisasi_rp_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                    }
                                                                } else {
                                                                    $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                    $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                                }
                                                            } else {
                                                                $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                            }

                                                            $kegiatan_tw_realisasi_realisasi_kolom_14_13 = [];
                                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi_kolom_14_13 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                                }
                                                            } else {
                                                                $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                            }
                                                            $realisasi_kolom_14 = array_sum($kegiatan_realisasi_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_14_13);
                                                            $realisasi_rp_kolom_14 = array_sum($kegiatan_realisasi_rp_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_14_13);
                                                            $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                            // Kolom 14 End
                                                            // Kolom 15 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();

                                                                $kegiatan_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_kegiatan_target_satuan_rp_realisasi_15_6 ? $cek_kegiatan_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();
                                                            if($last_kegiatan_target_satuan_rp_realisasi_15_6)
                                                            {
                                                                if($last_kegiatan_target_satuan_rp_realisasi_15_6->target)
                                                                {
                                                                    if((int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target != 0)
                                                                    {
                                                                        $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                    } else {
                                                                        $target_kolom_15 = 0;
                                                                    }
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }

                                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                            {
                                                                $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                            } else {
                                                                $target_rp_kolom_15 = 0;
                                                            }

                                                            $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                            $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                            // Kolom 15 End
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
                                                            // Kolom 6 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();
                                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();

                                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            }
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                            // Kolom 6 End

                                                            // Kolom 7 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                $kegiatan_realisasi = [];
                                                                $kegiatan_realisasi_rp = [];
                                                                if($cek_kegiatan_tw_realisasi)
                                                                {
                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                    }
                                                                    $e_79 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0, 00</td>';
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                            // Kolom 7 End

                                                            // Kolom 8 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 8 End
                                                            // Kolom 9 - 12 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    if($cek_kegiatan_tw_realisasi)
                                                                    {
                                                                        $e_79 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                    } else {
                                                                        $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. 0,00</td>';
                                                                    }
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 9 - 12 End
                                                            // Kolom 13 Start
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $kegiatan_tw_realisasi_realisasi = [];
                                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 13 End
                                                            // Kolom 14 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();
                                                            $kegiatan_realisasi_kolom_14_7 = [];
                                                            $kegiatan_realisasi_rp_kolom_14_7 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7)
                                                            {
                                                                $cek_kegiatan_tw_realisasi_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                                if($cek_kegiatan_tw_realisasi_kolom_14_7)
                                                                {
                                                                    $kegiatan_tw_realisasies_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies_kolom_14_7 as $kegiatan_tw_realisasi_kolom_14_7) {
                                                                        $kegiatan_realisasi_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi;
                                                                        $kegiatan_realisasi_rp_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                    }
                                                                } else {
                                                                    $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                    $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                                }
                                                            } else {
                                                                $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                            }

                                                            $kegiatan_tw_realisasi_realisasi_kolom_14_13 = [];
                                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi_kolom_14_13 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                                }
                                                            } else {
                                                                $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                            }
                                                            $realisasi_kolom_14 = array_sum($kegiatan_realisasi_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_14_13);
                                                            $realisasi_rp_kolom_14 = array_sum($kegiatan_realisasi_rp_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_14_13);
                                                            $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                            // Kolom 14 End
                                                            // Kolom 15 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();

                                                                $kegiatan_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_kegiatan_target_satuan_rp_realisasi_15_6 ? $cek_kegiatan_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();
                                                            if($last_kegiatan_target_satuan_rp_realisasi_15_6)
                                                            {
                                                                if($last_kegiatan_target_satuan_rp_realisasi_15_6->target)
                                                                {
                                                                    if((int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target != 0)
                                                                    {
                                                                        $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                    } else {
                                                                        $target_kolom_15 = 0;
                                                                    }
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }

                                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                            {
                                                                $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                            } else {
                                                                $target_rp_kolom_15 = 0;
                                                            }

                                                            $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                            $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                            // Kolom 15 End
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

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q){
                                    $q->whereHas('program_target_satuan_rp_realisasi');
                                })->get();
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
                                                        // Kolom 6 Start
                                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();
                                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();

                                                        if($last_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        }
                                                        $e_79 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                        // Kolom 6 End

                                                        // Kolom 7 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                            $program_realisasi = [];
                                                            $program_realisasi_rp = [];
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                                foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                                    $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                                    $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                        // Kolom 7 End

                                                        // Kolom 8 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 8 End
                                                        // Kolom 9 - 12 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                if($cek_program_tw_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0,00</td>';
                                                                }
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 9 - 12 End
                                                        // Kolom 13 Start
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $program_tw_realisasi_realisasi = [];
                                                            $program_tw_realisasi_realisasi_rp = [];
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                                            }
                                                            $e_79 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 13 End
                                                        // Kolom 14 Start
                                                        $cek_program_target_satuan_rp_realisasi_kolom_14_7 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();
                                                        $program_realisasi_kolom_14_7 = [];
                                                        $program_realisasi_rp_kolom_14_7 = [];
                                                        if($cek_program_target_satuan_rp_realisasi_kolom_14_7)
                                                        {
                                                            $cek_program_tw_realisasi_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                            if($cek_program_tw_realisasi_kolom_14_7)
                                                            {
                                                                $program_tw_realisasies_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                foreach ($program_tw_realisasies_kolom_14_7 as $program_tw_realisasi_kolom_14_7) {
                                                                    $program_realisasi_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi;
                                                                    $program_realisasi_rp_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                }
                                                            } else {
                                                                $program_realisasi_kolom_14_7[] = 0;
                                                                $program_realisasi_rp_kolom_14_7[] = 0;
                                                            }
                                                        } else {
                                                            $program_realisasi_kolom_14_7[] = 0;
                                                            $program_realisasi_rp_kolom_14_7[] = 0;
                                                        }

                                                        $program_tw_realisasi_realisasi_kolom_14_13 = [];
                                                        $program_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi_kolom_14_13 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                            }
                                                        } else {
                                                            $program_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                            $program_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                        }
                                                        $realisasi_kolom_14 = array_sum($program_realisasi_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_kolom_14_13);
                                                        $realisasi_rp_kolom_14 = array_sum($program_realisasi_rp_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_rp_kolom_14_13);
                                                        $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                        // Kolom 14 End
                                                        // Kolom 15 Start
                                                        $program_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();

                                                            $program_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_program_target_satuan_rp_realisasi_15_6 ? $cek_program_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();
                                                        if($last_program_target_satuan_rp_realisasi_15_6)
                                                        {
                                                            if($last_program_target_satuan_rp_realisasi_15_6->target)
                                                            {
                                                                if((int) $last_program_target_satuan_rp_realisasi_15_6->target != 0)
                                                                {
                                                                    $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_program_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }
                                                        } else {
                                                            $target_kolom_15 = 0;
                                                        }

                                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                        {
                                                            $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($program_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                        } else {
                                                            $target_rp_kolom_15 = 0;
                                                        }

                                                        $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                        $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                        // Kolom 15 End
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
                                                        // Kolom 6 Start
                                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();
                                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();

                                                        if($last_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        }
                                                        $e_79 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                        // Kolom 6 End

                                                        // Kolom 7 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                            $program_realisasi = [];
                                                            $program_realisasi_rp = [];
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                                foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                                    $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                                    $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                        // Kolom 7 End

                                                        // Kolom 8 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 8 End
                                                        // Kolom 9 - 12 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                if($cek_program_tw_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0,00</td>';
                                                                }
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 9 - 12 End
                                                        // Kolom 13 Start
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $program_tw_realisasi_realisasi = [];
                                                            $program_tw_realisasi_realisasi_rp = [];
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                                            }
                                                            $e_79 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 13 End
                                                        // Kolom 14 Start
                                                        $cek_program_target_satuan_rp_realisasi_kolom_14_7 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();
                                                        $program_realisasi_kolom_14_7 = [];
                                                        $program_realisasi_rp_kolom_14_7 = [];
                                                        if($cek_program_target_satuan_rp_realisasi_kolom_14_7)
                                                        {
                                                            $cek_program_tw_realisasi_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                            if($cek_program_tw_realisasi_kolom_14_7)
                                                            {
                                                                $program_tw_realisasies_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                foreach ($program_tw_realisasies_kolom_14_7 as $program_tw_realisasi_kolom_14_7) {
                                                                    $program_realisasi_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi;
                                                                    $program_realisasi_rp_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                }
                                                            } else {
                                                                $program_realisasi_kolom_14_7[] = 0;
                                                                $program_realisasi_rp_kolom_14_7[] = 0;
                                                            }
                                                        } else {
                                                            $program_realisasi_kolom_14_7[] = 0;
                                                            $program_realisasi_rp_kolom_14_7[] = 0;
                                                        }

                                                        $program_tw_realisasi_realisasi_kolom_14_13 = [];
                                                        $program_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi_kolom_14_13 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                            }
                                                        } else {
                                                            $program_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                            $program_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                        }
                                                        $realisasi_kolom_14 = array_sum($program_realisasi_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_kolom_14_13);
                                                        $realisasi_rp_kolom_14 = array_sum($program_realisasi_rp_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_rp_kolom_14_13);
                                                        $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                        // Kolom 14 End
                                                        // Kolom 15 Start
                                                        $program_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();

                                                            $program_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_program_target_satuan_rp_realisasi_15_6 ? $cek_program_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();
                                                        if($last_program_target_satuan_rp_realisasi_15_6)
                                                        {
                                                            if($last_program_target_satuan_rp_realisasi_15_6->target)
                                                            {
                                                                if((int) $last_program_target_satuan_rp_realisasi_15_6->target != 0)
                                                                {
                                                                    $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_program_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }
                                                        } else {
                                                            $target_kolom_15 = 0;
                                                        }

                                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                        {
                                                            $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($program_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                        } else {
                                                            $target_rp_kolom_15 = 0;
                                                        }

                                                        $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                        $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                        // Kolom 15 End
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
                                                        // Kolom 6 Start
                                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();
                                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();

                                                        if($last_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        }
                                                        $e_79 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                        // Kolom 6 End

                                                        // Kolom 7 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                            $program_realisasi = [];
                                                            $program_realisasi_rp = [];
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                                foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                                    $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                                    $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                        // Kolom 7 End

                                                        // Kolom 8 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 8 End
                                                        // Kolom 9 - 12 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                if($cek_program_tw_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0,00</td>';
                                                                }
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 9 - 12 End
                                                        // Kolom 13 Start
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $program_tw_realisasi_realisasi = [];
                                                            $program_tw_realisasi_realisasi_rp = [];
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                                            }
                                                            $e_79 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 13 End
                                                        // Kolom 14 Start
                                                        $cek_program_target_satuan_rp_realisasi_kolom_14_7 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();
                                                        $program_realisasi_kolom_14_7 = [];
                                                        $program_realisasi_rp_kolom_14_7 = [];
                                                        if($cek_program_target_satuan_rp_realisasi_kolom_14_7)
                                                        {
                                                            $cek_program_tw_realisasi_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                            if($cek_program_tw_realisasi_kolom_14_7)
                                                            {
                                                                $program_tw_realisasies_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                foreach ($program_tw_realisasies_kolom_14_7 as $program_tw_realisasi_kolom_14_7) {
                                                                    $program_realisasi_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi;
                                                                    $program_realisasi_rp_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                }
                                                            } else {
                                                                $program_realisasi_kolom_14_7[] = 0;
                                                                $program_realisasi_rp_kolom_14_7[] = 0;
                                                            }
                                                        } else {
                                                            $program_realisasi_kolom_14_7[] = 0;
                                                            $program_realisasi_rp_kolom_14_7[] = 0;
                                                        }

                                                        $program_tw_realisasi_realisasi_kolom_14_13 = [];
                                                        $program_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi_kolom_14_13 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                            }
                                                        } else {
                                                            $program_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                            $program_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                        }
                                                        $realisasi_kolom_14 = array_sum($program_realisasi_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_kolom_14_13);
                                                        $realisasi_rp_kolom_14 = array_sum($program_realisasi_rp_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_rp_kolom_14_13);
                                                        $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                        // Kolom 14 End
                                                        // Kolom 15 Start
                                                        $program_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();

                                                            $program_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_program_target_satuan_rp_realisasi_15_6 ? $cek_program_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();
                                                        if($last_program_target_satuan_rp_realisasi_15_6)
                                                        {
                                                            if($last_program_target_satuan_rp_realisasi_15_6->target)
                                                            {
                                                                if((int) $last_program_target_satuan_rp_realisasi_15_6->target != 0)
                                                                {
                                                                    $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_program_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }
                                                        } else {
                                                            $target_kolom_15 = 0;
                                                        }

                                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                        {
                                                            $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($program_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                        } else {
                                                            $target_rp_kolom_15 = 0;
                                                        }

                                                        $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                        $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                        // Kolom 15 End
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
                                                        // Kolom 6 Start
                                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();
                                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();

                                                        if($last_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        }
                                                        $e_79 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                        // Kolom 6 End

                                                        // Kolom 7 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->first();
                                                            $program_realisasi = [];
                                                            $program_realisasi_rp = [];
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->get();
                                                                foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                                                    $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                                    $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                        // Kolom 7 End

                                                        // Kolom 8 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 8 End
                                                        // Kolom 9 - 12 Start
                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal)->first();

                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                if($cek_program_tw_realisasi)
                                                                {
                                                                    $e_79 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0,00</td>';
                                                                }
                                                            }
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 9 - 12 End
                                                        // Kolom 13 Start
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            $program_tw_realisasi_realisasi = [];
                                                            $program_tw_realisasi_realisasi_rp = [];
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                                            }
                                                            $e_79 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_79 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. 0,00</td>';
                                                        }
                                                        // Kolom 13 End
                                                        // Kolom 14 Start
                                                        $cek_program_target_satuan_rp_realisasi_kolom_14_7 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', $tahun_awal - 1)->first();
                                                        $program_realisasi_kolom_14_7 = [];
                                                        $program_realisasi_rp_kolom_14_7 = [];
                                                        if($cek_program_target_satuan_rp_realisasi_kolom_14_7)
                                                        {
                                                            $cek_program_tw_realisasi_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                            if($cek_program_tw_realisasi_kolom_14_7)
                                                            {
                                                                $program_tw_realisasies_kolom_14_7 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                foreach ($program_tw_realisasies_kolom_14_7 as $program_tw_realisasi_kolom_14_7) {
                                                                    $program_realisasi_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi;
                                                                    $program_realisasi_rp_kolom_14_7[] = $program_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                }
                                                            } else {
                                                                $program_realisasi_kolom_14_7[] = 0;
                                                                $program_realisasi_rp_kolom_14_7[] = 0;
                                                            }
                                                        } else {
                                                            $program_realisasi_kolom_14_7[] = 0;
                                                            $program_realisasi_rp_kolom_14_7[] = 0;
                                                        }

                                                        $program_tw_realisasi_realisasi_kolom_14_13 = [];
                                                        $program_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                        if($cek_program_target_satuan_rp_realisasi)
                                                        {
                                                            foreach ($tws as $tw) {
                                                                $cek_program_tw_realisasi_kolom_14_13 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                $program_tw_realisasi_realisasi_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                $program_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_program_tw_realisasi_kolom_14_13?$cek_program_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                            }
                                                        } else {
                                                            $program_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                            $program_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                        }
                                                        $realisasi_kolom_14 = array_sum($program_realisasi_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_kolom_14_13);
                                                        $realisasi_rp_kolom_14 = array_sum($program_realisasi_rp_kolom_14_7) + array_sum($program_tw_realisasi_realisasi_rp_kolom_14_13);
                                                        $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                        // Kolom 14 End
                                                        // Kolom 15 Start
                                                        $program_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                        foreach ($tahuns as $item) {
                                                            $cek_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                    $q->where('id', $program_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $item)->first();

                                                            $program_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_program_target_satuan_rp_realisasi_15_6 ? $cek_program_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                        }

                                                        $last_program_target_satuan_rp_realisasi_15_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                $q->where('id', $program_indikator_kinerja->id);
                                                            });
                                                        })->where('tahun', end($tahuns))->first();
                                                        if($last_program_target_satuan_rp_realisasi_15_6)
                                                        {
                                                            if($last_program_target_satuan_rp_realisasi_15_6->target)
                                                            {
                                                                if((int) $last_program_target_satuan_rp_realisasi_15_6->target != 0)
                                                                {
                                                                    $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_program_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }
                                                        } else {
                                                            $target_kolom_15 = 0;
                                                        }

                                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                        {
                                                            $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($program_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                        } else {
                                                            $target_rp_kolom_15 = 0;
                                                        }

                                                        $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                        $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                        // Kolom 15 End
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

                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                        $q->whereHas('kegiatan_target_satuan_rp_realisasi');
                                    })->get();
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
                                                            // Kolom 6 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();
                                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();

                                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            }
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                            // Kolom 6 End

                                                            // Kolom 7 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                $kegiatan_realisasi = [];
                                                                $kegiatan_realisasi_rp = [];
                                                                if($cek_kegiatan_tw_realisasi)
                                                                {
                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                    }
                                                                    $e_79 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0, 00</td>';
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                            // Kolom 7 End

                                                            // Kolom 8 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 8 End
                                                            // Kolom 9 - 12 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    if($cek_kegiatan_tw_realisasi)
                                                                    {
                                                                        $e_79 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                    } else {
                                                                        $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. 0,00</td>';
                                                                    }
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 9 - 12 End
                                                            // Kolom 13 Start
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $kegiatan_tw_realisasi_realisasi = [];
                                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 13 End
                                                            // Kolom 14 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();
                                                            $kegiatan_realisasi_kolom_14_7 = [];
                                                            $kegiatan_realisasi_rp_kolom_14_7 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7)
                                                            {
                                                                $cek_kegiatan_tw_realisasi_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                                if($cek_kegiatan_tw_realisasi_kolom_14_7)
                                                                {
                                                                    $kegiatan_tw_realisasies_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies_kolom_14_7 as $kegiatan_tw_realisasi_kolom_14_7) {
                                                                        $kegiatan_realisasi_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi;
                                                                        $kegiatan_realisasi_rp_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                    }
                                                                } else {
                                                                    $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                    $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                                }
                                                            } else {
                                                                $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                            }

                                                            $kegiatan_tw_realisasi_realisasi_kolom_14_13 = [];
                                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi_kolom_14_13 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                                }
                                                            } else {
                                                                $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                            }
                                                            $realisasi_kolom_14 = array_sum($kegiatan_realisasi_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_14_13);
                                                            $realisasi_rp_kolom_14 = array_sum($kegiatan_realisasi_rp_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_14_13);
                                                            $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                            // Kolom 14 End
                                                            // Kolom 15 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();

                                                                $kegiatan_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_kegiatan_target_satuan_rp_realisasi_15_6 ? $cek_kegiatan_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();
                                                            if($last_kegiatan_target_satuan_rp_realisasi_15_6)
                                                            {
                                                                if($last_kegiatan_target_satuan_rp_realisasi_15_6->target)
                                                                {
                                                                    if((int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target != 0)
                                                                    {
                                                                        $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                    } else {
                                                                        $target_kolom_15 = 0;
                                                                    }
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }

                                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                            {
                                                                $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                            } else {
                                                                $target_rp_kolom_15 = 0;
                                                            }

                                                            $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                            $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                            // Kolom 15 End
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
                                                            // Kolom 6 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();
                                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();

                                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            }
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                            // Kolom 6 End

                                                            // Kolom 7 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                $kegiatan_realisasi = [];
                                                                $kegiatan_realisasi_rp = [];
                                                                if($cek_kegiatan_tw_realisasi)
                                                                {
                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                    }
                                                                    $e_79 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0, 00</td>';
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                            // Kolom 7 End

                                                            // Kolom 8 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 8 End
                                                            // Kolom 9 - 12 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    if($cek_kegiatan_tw_realisasi)
                                                                    {
                                                                        $e_79 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                    } else {
                                                                        $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. 0,00</td>';
                                                                    }
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 9 - 12 End
                                                            // Kolom 13 Start
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $kegiatan_tw_realisasi_realisasi = [];
                                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 13 End
                                                            // Kolom 14 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();
                                                            $kegiatan_realisasi_kolom_14_7 = [];
                                                            $kegiatan_realisasi_rp_kolom_14_7 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7)
                                                            {
                                                                $cek_kegiatan_tw_realisasi_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                                if($cek_kegiatan_tw_realisasi_kolom_14_7)
                                                                {
                                                                    $kegiatan_tw_realisasies_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies_kolom_14_7 as $kegiatan_tw_realisasi_kolom_14_7) {
                                                                        $kegiatan_realisasi_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi;
                                                                        $kegiatan_realisasi_rp_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                    }
                                                                } else {
                                                                    $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                    $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                                }
                                                            } else {
                                                                $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                            }

                                                            $kegiatan_tw_realisasi_realisasi_kolom_14_13 = [];
                                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi_kolom_14_13 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                                }
                                                            } else {
                                                                $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                            }
                                                            $realisasi_kolom_14 = array_sum($kegiatan_realisasi_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_14_13);
                                                            $realisasi_rp_kolom_14 = array_sum($kegiatan_realisasi_rp_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_14_13);
                                                            $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                            // Kolom 14 End
                                                            // Kolom 15 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();

                                                                $kegiatan_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_kegiatan_target_satuan_rp_realisasi_15_6 ? $cek_kegiatan_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();
                                                            if($last_kegiatan_target_satuan_rp_realisasi_15_6)
                                                            {
                                                                if($last_kegiatan_target_satuan_rp_realisasi_15_6->target)
                                                                {
                                                                    if((int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target != 0)
                                                                    {
                                                                        $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                    } else {
                                                                        $target_kolom_15 = 0;
                                                                    }
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }

                                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                            {
                                                                $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                            } else {
                                                                $target_rp_kolom_15 = 0;
                                                            }

                                                            $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                            $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                            // Kolom 15 End
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
                                                            // Kolom 6 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();
                                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();

                                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            }
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                            // Kolom 6 End

                                                            // Kolom 7 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                $kegiatan_realisasi = [];
                                                                $kegiatan_realisasi_rp = [];
                                                                if($cek_kegiatan_tw_realisasi)
                                                                {
                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                    }
                                                                    $e_79 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0, 00</td>';
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                            // Kolom 7 End

                                                            // Kolom 8 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 8 End
                                                            // Kolom 9 - 12 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    if($cek_kegiatan_tw_realisasi)
                                                                    {
                                                                        $e_79 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                    } else {
                                                                        $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. 0,00</td>';
                                                                    }
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 9 - 12 End
                                                            // Kolom 13 Start
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $kegiatan_tw_realisasi_realisasi = [];
                                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 13 End
                                                            // Kolom 14 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();
                                                            $kegiatan_realisasi_kolom_14_7 = [];
                                                            $kegiatan_realisasi_rp_kolom_14_7 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7)
                                                            {
                                                                $cek_kegiatan_tw_realisasi_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                                if($cek_kegiatan_tw_realisasi_kolom_14_7)
                                                                {
                                                                    $kegiatan_tw_realisasies_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies_kolom_14_7 as $kegiatan_tw_realisasi_kolom_14_7) {
                                                                        $kegiatan_realisasi_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi;
                                                                        $kegiatan_realisasi_rp_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                    }
                                                                } else {
                                                                    $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                    $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                                }
                                                            } else {
                                                                $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                            }

                                                            $kegiatan_tw_realisasi_realisasi_kolom_14_13 = [];
                                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi_kolom_14_13 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                                }
                                                            } else {
                                                                $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                            }
                                                            $realisasi_kolom_14 = array_sum($kegiatan_realisasi_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_14_13);
                                                            $realisasi_rp_kolom_14 = array_sum($kegiatan_realisasi_rp_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_14_13);
                                                            $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                            // Kolom 14 End
                                                            // Kolom 15 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();

                                                                $kegiatan_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_kegiatan_target_satuan_rp_realisasi_15_6 ? $cek_kegiatan_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();
                                                            if($last_kegiatan_target_satuan_rp_realisasi_15_6)
                                                            {
                                                                if($last_kegiatan_target_satuan_rp_realisasi_15_6->target)
                                                                {
                                                                    if((int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target != 0)
                                                                    {
                                                                        $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                    } else {
                                                                        $target_kolom_15 = 0;
                                                                    }
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }

                                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                            {
                                                                $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                            } else {
                                                                $target_rp_kolom_15 = 0;
                                                            }

                                                            $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                            $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                            // Kolom 15 End
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
                                                            // Kolom 6 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();
                                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();

                                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            }
                                                            $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                            // Kolom 6 End

                                                            // Kolom 7 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                $kegiatan_realisasi = [];
                                                                $kegiatan_realisasi_rp = [];
                                                                if($cek_kegiatan_tw_realisasi)
                                                                {
                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                    }
                                                                    $e_79 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                                } else {
                                                                    $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                    $e_79 .= '<td>Rp. 0, 00</td>';
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0, 00</td>';
                                                            }
                                                            // Kolom 7 End

                                                            // Kolom 8 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $e_79 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 8 End
                                                            // Kolom 9 - 12 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal)->first();

                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    if($cek_kegiatan_tw_realisasi)
                                                                    {
                                                                        $e_79 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                                    } else {
                                                                        $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                        $e_79 .= '<td>Rp. 0,00</td>';
                                                                    }
                                                                }
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 9 - 12 End
                                                            // Kolom 13 Start
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                $kegiatan_tw_realisasi_realisasi = [];
                                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                                }
                                                                $e_79 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                            } else {
                                                                $e_79 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                $e_79 .= '<td>Rp. 0,00</td>';
                                                            }
                                                            // Kolom 13 End
                                                            // Kolom 14 Start
                                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', $tahun_awal - 1)->first();
                                                            $kegiatan_realisasi_kolom_14_7 = [];
                                                            $kegiatan_realisasi_rp_kolom_14_7 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7)
                                                            {
                                                                $cek_kegiatan_tw_realisasi_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->first();

                                                                if($cek_kegiatan_tw_realisasi_kolom_14_7)
                                                                {
                                                                    $kegiatan_tw_realisasies_kolom_14_7 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_14_7->id)->get();
                                                                    foreach ($kegiatan_tw_realisasies_kolom_14_7 as $kegiatan_tw_realisasi_kolom_14_7) {
                                                                        $kegiatan_realisasi_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi;
                                                                        $kegiatan_realisasi_rp_kolom_14_7[] = $kegiatan_tw_realisasi_kolom_14_7->realisasi_rp;
                                                                    }
                                                                } else {
                                                                    $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                    $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                                }
                                                            } else {
                                                                $kegiatan_realisasi_kolom_14_7[] = 0;
                                                                $kegiatan_realisasi_rp_kolom_14_7[] = 0;
                                                            }

                                                            $kegiatan_tw_realisasi_realisasi_kolom_14_13 = [];
                                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13 = [];
                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                            {
                                                                foreach ($tws as $tw) {
                                                                    $cek_kegiatan_tw_realisasi_kolom_14_13 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                                    $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi : 0;
                                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = $cek_kegiatan_tw_realisasi_kolom_14_13?$cek_kegiatan_tw_realisasi_kolom_14_13->realisasi_rp : 0;
                                                                }
                                                            } else {
                                                                $kegiatan_tw_realisasi_realisasi_kolom_14_13[] = 0;
                                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_14_13[] = 0;
                                                            }
                                                            $realisasi_kolom_14 = array_sum($kegiatan_realisasi_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_14_13);
                                                            $realisasi_rp_kolom_14 = array_sum($kegiatan_realisasi_rp_kolom_14_7) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_14_13);
                                                            $e_79 .= '<td>'.$realisasi_kolom_14.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($realisasi_rp_kolom_14, 2, ',', '.').'</td>';
                                                            // Kolom 14 End
                                                            // Kolom 15 Start
                                                            $kegiatan_target_satuan_rp_realisasi_target_rp_15_6 = [];
                                                            foreach ($tahuns as $item) {
                                                                $cek_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                    });
                                                                })->where('tahun', $item)->first();

                                                                $kegiatan_target_satuan_rp_realisasi_target_rp_15_6[] = $cek_kegiatan_target_satuan_rp_realisasi_15_6 ? $cek_kegiatan_target_satuan_rp_realisasi_15_6->target_rp : 0;
                                                            }

                                                            $last_kegiatan_target_satuan_rp_realisasi_15_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                });
                                                            })->where('tahun', end($tahuns))->first();
                                                            if($last_kegiatan_target_satuan_rp_realisasi_15_6)
                                                            {
                                                                if($last_kegiatan_target_satuan_rp_realisasi_15_6->target)
                                                                {
                                                                    if((int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target != 0)
                                                                    {
                                                                        $target_kolom_15 = ($realisasi_kolom_14 / (int) $last_kegiatan_target_satuan_rp_realisasi_15_6->target) * 100;
                                                                    } else {
                                                                        $target_kolom_15 = 0;
                                                                    }
                                                                } else {
                                                                    $target_kolom_15 = 0;
                                                                }
                                                            } else {
                                                                $target_kolom_15 = 0;
                                                            }

                                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6) != 0)
                                                            {
                                                                $target_rp_kolom_15 = ($realisasi_rp_kolom_14 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_15_6)) * 100;
                                                            } else {
                                                                $target_rp_kolom_15 = 0;
                                                            }

                                                            $e_79 .= '<td>'.number_format($target_kolom_15, 2).'</td>';
                                                            $e_79 .= '<td>'.number_format($target_rp_kolom_15, 2, ',', '.').'</td>';
                                                            // Kolom 15 End
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

        return view('admin.laporan.e-79', [
            'e_79' => $e_79,
            'tahun' => $this->tahun
        ]);
    }
}
