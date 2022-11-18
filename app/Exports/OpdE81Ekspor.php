<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Auth;
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
use App\Models\MasterTw;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanTwRealisasi;

class OpdE81Ekspor implements FromView
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function view():View
    {
        $tahun = $this->tahun;
        $tws = MasterTw::all();

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q){
                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                $q->whereHas('program_rpjmd', function($q){
                                    $q->whereHas('program', function($q){
                                        $q->whereHas('program_indikator_kinerja', function($q){
                                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                $q->where('opd_id', Auth::user()->opd->opd_id);
                                            });
                                        });
                                    });
                                });
                            });
                        })->get();
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

        $e_81 = '';
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_81 .= '<tr>';
                $e_81 .= '<td style="text-align: left;">'.$a++.'</td>';
                $e_81 .= '<td style="text-align: left;">'.$sasaran['deskripsi'].'</td>';

                $get_programs = Program::whereHas('program_rpjmd', function($q) use ($sasaran){
                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                        $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                            $q->whereHas('sasaran', function($q) use ($sasaran) {
                                $q->where('id', $sasaran['id']);
                            });
                        });
                    });
                })->whereHas('program_indikator_kinerja', function($q){
                    $q->whereHas('opd_program_indikator_kinerja', function($q){
                        $q->where('opd_id', Auth::user()->opd->opd_id);
                    });
                })->get();
                $programs = [];
                foreach($get_programs as $get_program)
                {
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
                            $e_81 .= '<td style="text-align: left;">'.$program['deskripsi'].'</td>';
                            // Indikator Program
                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q){
                                $q->where('opd_id', Auth::user()->opd->opd_id);
                            })->get();
                            $c = 1;
                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                if($c == 1)
                                {
                                        $e_81 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                        // Opd Program Indikator
                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                        $d = 1;
                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                            if($d == 1)
                                            {
                                                    // Target Program
                                                    $target_renstra_berdasarkan_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                    if($target_renstra_berdasarkan_tahun)
                                                    {
                                                        $e_81 .= '<td style="text-align: left;">'.$target_renstra_berdasarkan_tahun->target.'/'.$target_renstra_berdasarkan_tahun->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_berdasarkan_tahun->target_rp,2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }

                                                    $target_renstra_berdasarkan_tahun_lalu = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun - 1)->first();
                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        $program_tw_realisasis =  ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)->get();
                                                        $program_realisasi = [];
                                                        $program_realisasi_rp = [];
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td style="text-align: left;">'.array_sum($program_realisasi).'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($program_realisasi_rp),2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';

                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        foreach ($tws as $tw) {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)
                                                                                            ->where('tw_id', $tw->id)->first();
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $e_81 .= '<td style="text-align: left;">'.$cek_program_tw_realisasi->realisasi.'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp,2).'</td>';
                                                            } else {
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                            }
                                                        }
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                $e_81 .= '</tr>';

                                                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q){
                                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                    });
                                                })->get();
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
                                                foreach ($kegiatans as $kegiatan) {
                                                    $e_81 .= '<tr>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';

                                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                        })->get();
                                                        $e = 1;
                                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                            if($e == 1)
                                                            {
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            } else {
                                                                $e_81 .= '<tr>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            }
                                                            $e++;
                                                        }
                                                }
                                            } else {
                                                $e_81 .= '<tr>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    // Target Program
                                                    $target_renstra_berdasarkan_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                    if($target_renstra_berdasarkan_tahun)
                                                    {
                                                        $e_81 .= '<td style="text-align: left;">'.$target_renstra_berdasarkan_tahun->target.'/'.$target_renstra_berdasarkan_tahun->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_berdasarkan_tahun->target_rp,2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }

                                                    $target_renstra_berdasarkan_tahun_lalu = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun - 1)->first();
                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        $program_tw_realisasis =  ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)->get();
                                                        $program_realisasi = [];
                                                        $program_realisasi_rp = [];
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td style="text-align: left;">'.array_sum($program_realisasi).'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($program_realisasi_rp),2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';

                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        foreach ($tws as $tw) {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)
                                                                                            ->where('tw_id', $tw->id)->first();
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $e_81 .= '<td style="text-align: left;">'.$cek_program_tw_realisasi->realisasi.'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp,2).'</td>';
                                                            } else {
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                            }
                                                        }
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                $e_81 .= '</tr>';

                                                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q){
                                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                    });
                                                })->get();
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
                                                foreach ($kegiatans as $kegiatan) {
                                                    $e_81 .= '<tr>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';

                                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                        })->get();
                                                        $e = 1;
                                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                            if($e == 1)
                                                            {
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            } else {
                                                                $e_81 .= '<tr>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            }
                                                            $e++;
                                                        }
                                                }
                                            }
                                            $d++;
                                        }
                                } else {
                                    $e_81 .= '<tr>';
                                        $e_81 .= '<td style="text-align: left;"></td>';
                                        $e_81 .= '<td style="text-align: left;"></td>';
                                        $e_81 .= '<td style="text-align: left;"></td>';
                                        $e_81 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                        // Opd Program Indikator
                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                        $d = 1;
                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                            if($d == 1)
                                            {
                                                    // Target Program
                                                    $target_renstra_berdasarkan_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                    if($target_renstra_berdasarkan_tahun)
                                                    {
                                                        $e_81 .= '<td style="text-align: left;">'.$target_renstra_berdasarkan_tahun->target.'/'.$target_renstra_berdasarkan_tahun->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_berdasarkan_tahun->target_rp,2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }

                                                    $target_renstra_berdasarkan_tahun_lalu = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun - 1)->first();
                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        $program_tw_realisasis =  ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)->get();
                                                        $program_realisasi = [];
                                                        $program_realisasi_rp = [];
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td style="text-align: left;">'.array_sum($program_realisasi).'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($program_realisasi_rp),2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';

                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        foreach ($tws as $tw) {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)
                                                                                            ->where('tw_id', $tw->id)->first();
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $e_81 .= '<td style="text-align: left;">'.$cek_program_tw_realisasi->realisasi.'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp,2).'</td>';
                                                            } else {
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                            }
                                                        }
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                $e_81 .= '</tr>';

                                                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q){
                                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                    });
                                                })->get();
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
                                                foreach ($kegiatans as $kegiatan) {
                                                    $e_81 .= '<tr>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';

                                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                        })->get();
                                                        $e = 1;
                                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                            if($e == 1)
                                                            {
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            } else {
                                                                $e_81 .= '<tr>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            }
                                                            $e++;
                                                        }
                                                }
                                            } else {
                                                $e_81 .= '<tr>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    // Target Program
                                                    $target_renstra_berdasarkan_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                    if($target_renstra_berdasarkan_tahun)
                                                    {
                                                        $e_81 .= '<td style="text-align: left;">'.$target_renstra_berdasarkan_tahun->target.'/'.$target_renstra_berdasarkan_tahun->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_berdasarkan_tahun->target_rp,2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }

                                                    $target_renstra_berdasarkan_tahun_lalu = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun - 1)->first();
                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        $program_tw_realisasis =  ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)->get();
                                                        $program_realisasi = [];
                                                        $program_realisasi_rp = [];
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td style="text-align: left;">'.array_sum($program_realisasi).'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($program_realisasi_rp),2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';

                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        foreach ($tws as $tw) {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)
                                                                                            ->where('tw_id', $tw->id)->first();
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $e_81 .= '<td style="text-align: left;">'.$cek_program_tw_realisasi->realisasi.'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp,2).'</td>';
                                                            } else {
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                            }
                                                        }
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                $e_81 .= '</tr>';

                                                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q){
                                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                    });
                                                })->get();
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
                                                foreach ($kegiatans as $kegiatan) {
                                                    $e_81 .= '<tr>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';

                                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                        })->get();
                                                        $e = 1;
                                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                            if($e == 1)
                                                            {
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            } else {
                                                                $e_81 .= '<tr>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            }
                                                            $e++;
                                                        }
                                                }
                                            }
                                            $d++;
                                        }
                                }
                                $c++;
                            }
                    } else {
                        $e_81 .= '<tr>';
                            $e_81 .= '<td style="text-align: left;"></td>';
                            $e_81 .= '<td style="text-align: left;"></td>';
                            $e_81 .= '<td style="text-align: left;">'.$program['deskripsi'].'</td>';
                            // Indikator Program
                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q){
                                $q->where('opd_id', Auth::user()->opd->opd_id);
                            })->get();
                            $c = 1;
                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                if($c == 1)
                                {
                                        $e_81 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                        // Opd Program Indikator
                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                        $d = 1;
                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                            if($d == 1)
                                            {
                                                    // Target Program
                                                    $target_renstra_berdasarkan_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                    if($target_renstra_berdasarkan_tahun)
                                                    {
                                                        $e_81 .= '<td style="text-align: left;">'.$target_renstra_berdasarkan_tahun->target.'/'.$target_renstra_berdasarkan_tahun->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_berdasarkan_tahun->target_rp,2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }

                                                    $target_renstra_berdasarkan_tahun_lalu = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun - 1)->first();
                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        $program_tw_realisasis =  ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)->get();
                                                        $program_realisasi = [];
                                                        $program_realisasi_rp = [];
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td style="text-align: left;">'.array_sum($program_realisasi).'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($program_realisasi_rp),2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';

                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        foreach ($tws as $tw) {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)
                                                                                            ->where('tw_id', $tw->id)->first();
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $e_81 .= '<td style="text-align: left;">'.$cek_program_tw_realisasi->realisasi.'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp,2).'</td>';
                                                            } else {
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                            }
                                                        }
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                $e_81 .= '</tr>';

                                                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q){
                                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                    });
                                                })->get();
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
                                                foreach ($kegiatans as $kegiatan) {
                                                    $e_81 .= '<tr>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';

                                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                        })->get();
                                                        $e = 1;
                                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                            if($e == 1)
                                                            {
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            } else {
                                                                $e_81 .= '<tr>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            }
                                                            $e++;
                                                        }
                                                }
                                            } else {
                                                $e_81 .= '<tr>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    // Target Program
                                                    $target_renstra_berdasarkan_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                    if($target_renstra_berdasarkan_tahun)
                                                    {
                                                        $e_81 .= '<td style="text-align: left;">'.$target_renstra_berdasarkan_tahun->target.'/'.$target_renstra_berdasarkan_tahun->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_berdasarkan_tahun->target_rp,2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }

                                                    $target_renstra_berdasarkan_tahun_lalu = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun - 1)->first();
                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        $program_tw_realisasis =  ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)->get();
                                                        $program_realisasi = [];
                                                        $program_realisasi_rp = [];
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td style="text-align: left;">'.array_sum($program_realisasi).'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($program_realisasi_rp),2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';

                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        foreach ($tws as $tw) {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)
                                                                                            ->where('tw_id', $tw->id)->first();
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $e_81 .= '<td style="text-align: left;">'.$cek_program_tw_realisasi->realisasi.'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp,2).'</td>';
                                                            } else {
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                            }
                                                        }
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                $e_81 .= '</tr>';

                                                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q){
                                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                    });
                                                })->get();
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
                                                foreach ($kegiatans as $kegiatan) {
                                                    $e_81 .= '<tr>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';

                                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                        })->get();
                                                        $e = 1;
                                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                            if($e == 1)
                                                            {
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            } else {
                                                                $e_81 .= '<tr>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            }
                                                            $e++;
                                                        }
                                                }
                                            }
                                            $d++;
                                        }
                                } else {
                                    $e_81 .= '<tr>';
                                        $e_81 .= '<td style="text-align: left;"></td>';
                                        $e_81 .= '<td style="text-align: left;"></td>';
                                        $e_81 .= '<td style="text-align: left;"></td>';
                                        $e_81 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                        // Opd Program Indikator
                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                        $d = 1;
                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                            if($d == 1)
                                            {
                                                    // Target Program
                                                    $target_renstra_berdasarkan_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                    if($target_renstra_berdasarkan_tahun)
                                                    {
                                                        $e_81 .= '<td style="text-align: left;">'.$target_renstra_berdasarkan_tahun->target.'/'.$target_renstra_berdasarkan_tahun->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_berdasarkan_tahun->target_rp,2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }

                                                    $target_renstra_berdasarkan_tahun_lalu = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun - 1)->first();
                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        $program_tw_realisasis =  ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)->get();
                                                        $program_realisasi = [];
                                                        $program_realisasi_rp = [];
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td style="text-align: left;">'.array_sum($program_realisasi).'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($program_realisasi_rp),2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';

                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        foreach ($tws as $tw) {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)
                                                                                            ->where('tw_id', $tw->id)->first();
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $e_81 .= '<td style="text-align: left;">'.$cek_program_tw_realisasi->realisasi.'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp,2).'</td>';
                                                            } else {
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                            }
                                                        }
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                $e_81 .= '</tr>';

                                                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q){
                                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                    });
                                                })->get();
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
                                                foreach ($kegiatans as $kegiatan) {
                                                    $e_81 .= '<tr>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';

                                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                        })->get();
                                                        $e = 1;
                                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                            if($e == 1)
                                                            {
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            } else {
                                                                $e_81 .= '<tr>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            }
                                                            $e++;
                                                        }
                                                }
                                            } else {
                                                $e_81 .= '<tr>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    // Target Program
                                                    $target_renstra_berdasarkan_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                    if($target_renstra_berdasarkan_tahun)
                                                    {
                                                        $e_81 .= '<td style="text-align: left;">'.$target_renstra_berdasarkan_tahun->target.'/'.$target_renstra_berdasarkan_tahun->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_berdasarkan_tahun->target_rp,2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }

                                                    $target_renstra_berdasarkan_tahun_lalu = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun - 1)->first();
                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        $program_tw_realisasis =  ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)->get();
                                                        $program_realisasi = [];
                                                        $program_realisasi_rp = [];
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td style="text-align: left;">'.array_sum($program_realisasi).'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                        $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($program_realisasi_rp),2).'</td>';
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';

                                                    if($target_renstra_berdasarkan_tahun_lalu)
                                                    {
                                                        foreach ($tws as $tw) {
                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $target_renstra_berdasarkan_tahun_lalu->id)
                                                                                            ->where('tw_id', $tw->id)->first();
                                                            if($cek_program_tw_realisasi)
                                                            {
                                                                $e_81 .= '<td style="text-align: left;">'.$cek_program_tw_realisasi->realisasi.'/'.$target_renstra_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp,2).'</td>';
                                                            } else {
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                            }
                                                        }
                                                    } else {
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                    }
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                    $e_81 .= '<td style="text-align: left;">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                $e_81 .= '</tr>';

                                                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q){
                                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                    });
                                                })->get();
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
                                                foreach ($kegiatans as $kegiatan) {
                                                    $e_81 .= '<tr>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;"></td>';
                                                        $e_81 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';

                                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                        })->get();
                                                        $e = 1;
                                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                            if($e == 1)
                                                            {
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            } else {
                                                                $e_81 .= '<tr>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                    $e_81 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                    // Opd Kegiatan Indikator
                                                                    $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                    $f = 1;
                                                                    foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                        if($f == 1)
                                                                        {
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        } else {
                                                                            $e_81 .= '<tr>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                // Target Kegiatan
                                                                                $target_renstra_kegiatan_berdasarkan_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun)
                                                                                {
                                                                                    $e_81 .= '<td style="text-align: left;">'.$target_renstra_kegiatan_berdasarkan_tahun->target.'/'.$target_renstra_kegiatan_berdasarkan_tahun->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format($target_renstra_kegiatan_berdasarkan_tahun->target_rp,2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }

                                                                                $target_renstra_kegiatan_berdasarkan_tahun_lalu = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                        ->where('tahun', $tahun - 1)->first();
                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    $kegiatan_tw_realisasis =  KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)->get();
                                                                                    $kegiatan_realisasi = [];
                                                                                    $kegiatan_realisasi_rp = [];
                                                                                    foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                        $kegiatan_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                                                    }
                                                                                    $e_81 .= '<td style="text-align: left;">'.array_sum($kegiatan_realisasi).'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                    $e_81 .= '<td style="text-align: left;">Rp. '.number_format(array_sum($kegiatan_realisasi_rp),2).'</td>';
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';

                                                                                if($target_renstra_kegiatan_berdasarkan_tahun_lalu)
                                                                                {
                                                                                    foreach ($tws as $tw) {
                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $target_renstra_kegiatan_berdasarkan_tahun_lalu->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                        {
                                                                                            $e_81 .= '<td style="text-align: left;">'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$target_renstra_kegiatan_berdasarkan_tahun_lalu->satuan.'</td>';
                                                                                            $e_81 .= '<td style="text-align: left;">Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp,2).'</td>';
                                                                                        } else {
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                            $e_81 .= '<td style="text-align: left;"></td>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                    $e_81 .= '<td style="text-align: left;"></td>';
                                                                                }
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;"></td>';
                                                                                $e_81 .= '<td style="text-align: left;">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                            $e_81 .= '</tr>';
                                                                        }
                                                                        $f++;
                                                                    }
                                                            }
                                                            $e++;
                                                        }
                                                }
                                            }
                                            $d++;
                                        }
                                }
                                $c++;
                            }
                    }
                    $b++;
                }
        }

        return view('opd.laporan.e-81', [
            'e_81' => $e_81,
            'tahun' => $tahun
        ]);
    }
}
