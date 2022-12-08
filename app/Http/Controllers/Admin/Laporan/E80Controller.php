<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use App\Models\PivotOpdProgramRpjmd;
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Models\MasterOpd;
use App\Models\PivotSasaranIndikatorProgramRpjmd;
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotProgramKegiatanRenstra;
use App\Models\TargetRpPertahunProgram;
use App\Models\RenstraKegiatan;
use App\Models\PivotOpdRentraKegiatan;
use App\Models\Kegiatan;
use App\Models\PivotPerubahanKegiatan;
use App\Models\TargetRpPertahunRenstraKegiatan;
use App\Models\SasaranIndikatorKinerja;
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
use App\Models\MasterTw;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\TujuanPdRealisasiRenja;
use App\Models\SasaranPdRealisasiRenja;
use App\Models\SasaranPdProgramRpjmd;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\SubKegiatanIndikatorKinerja;
use App\Models\OpdSubKegiatanIndikatorKinerja;
use App\Models\SubKegiatanTargetSatuanRpRealisasi;
use App\Models\SubKegiatanTwRealisasi;
use Excel;
use PDF;
use App\Exports\E80Ekspor;

class E80Controller extends Controller
{
    public function laporan_e_80(Request $request)
    {
        $opd = MasterOpd::find($request->opd_id);

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $tahun_akhir = $get_periode->tahun_akhir;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_tujuans = Tujuan::whereHas('sasaran', function($q) use ($opd){
            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($opd){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd){
                    $q->whereHas('program_rpjmd', function($q) use ($opd){
                        $q->whereHas('program', function($q) use ($opd){
                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd){
                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                    $q->where('opd_id', $opd->id);
                                });
                            });
                        });
                    });
                });
            });
        })->get();

        $tujuans = [];

        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->latest()->first();
            if($cek_perubahan_tujuan)
            {
                $tujuans[] = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'kode' => $cek_perubahan_tujuan->kode,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi
                ];
            } else {
                $tujuans[] = [
                    'id' => $get_tujuan->id,
                    'kode' => $get_tujuan->kode,
                    'deskripsi' => $get_tujuan->deskripsi
                ];
            }
        }

        $e_80 = '';
        $a = 1;
        foreach ($tujuans as $tujuan)
        {
            $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->whereHas('sasaran_indikator_kinerja', function($q) use ($opd){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd){
                    $q->whereHas('program_rpjmd', function($q) use ($opd){
                        $q->whereHas('program', function($q) use ($opd){
                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd){
                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                    $q->where('opd_id', $opd->id);
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
                        'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                        'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan,
                    ];
                } else {
                    $sasarans[] = [
                        'id' => $get_sasaran->id,
                        'kode' => $get_sasaran->kode,
                        'deskripsi' => $get_sasaran->deskripsi,
                        'tahun_perubahan' => $get_sasaran->tahun_perubahan,
                    ];
                }
            }

            foreach ($sasarans as $sasaran)
            {
                $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', $opd->id)->get();
                foreach ($get_sasaran_pds as $get_sasaran_pd)
                {
                    $e_80 .= '<tr>';
                        $e_80 .= '<td>'.$a++.'</td>';
                        $e_80 .= '<td style="text-align:left">'.$get_sasaran_pd->deskripsi.'</td>';
                        $e_80 .= '<td></td>';
                        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $get_sasaran_pd->id)->get();
                        $b = 1;
                        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                            if($b == 1)
                            {
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td></td>';
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $e_80 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td></td>';
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                $e_80 .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td></td>';
                                            } else {
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                            }
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                $rasio_target = $cek_sasaran_pd_realisasi_renja->realisasi/$cek_sasaran_pd_target_satuan_rp_realisasi->target;
                                                $e_80 .= '<td>'.number_format($rasio_target,2,',').'</td>';
                                                $e_80 .= '<td></td>';
                                            } else {
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                            }
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }
                                    $e_80 .= '<td>'.$opd->nama.'</td>';
                                $e_80 .= '</tr>';
                            } else {
                                $e_80 .= '<tr>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td></td>';
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $e_80 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td></td>';
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                $e_80 .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td></td>';
                                            } else {
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                            }
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                $rasio_target = $cek_sasaran_pd_realisasi_renja->realisasi/$cek_sasaran_pd_target_satuan_rp_realisasi->target;
                                                $e_80 .= '<td>'.number_format($rasio_target,2,',').'</td>';
                                                $e_80 .= '<td></td>';
                                            } else {
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                            }
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }
                                    $e_80 .= '<td>'.$opd->nama.'</td>';
                                $e_80 .= '</tr>';
                            }
                            $b++;
                        }

                        $get_programs = Program::whereHas('program_indikator_kinerja', function($q) use ($opd){
                            $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                $q->where('opd_id', $opd->id);
                            });
                        })->whereHas('program_rpjmd', function($q) use ($get_sasaran_pd){
                            $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($get_sasaran_pd){
                                $q->where('sasaran_pd_id', $get_sasaran_pd->id);
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
                                    'deskripsi' => $cek_perubahan_program->deskripsi,
                                    'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan
                                ];
                            } else {
                                $programs[] = [
                                    'id' => $get_program->id,
                                    'kode' => $get_program->kode,
                                    'deskripsi' => $get_program->deskripsi,
                                    'tahun_perubahan' => $get_program->tahun_perubahan
                                ];
                            }
                        }
                        foreach($programs as $program)
                        {
                            $e_80 .= '<tr>';
                                $e_80 .= '<td>'.$a++.'</td>';
                                $e_80 .= '<td></td>';
                                $e_80 .= '<td>'.$program['deskripsi'].'</td>';
                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                    $q->where('opd_id', $opd->id);
                                })->get();
                                $b = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($b == 1)
                                    {
                                            $e_80 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                            $e_80 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();
                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $e_80 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_80 .= '<td></td>';
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }

                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_target_realisasi = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                        }
                                                        $e_80 .= '<td>'.array_sum($program_tw_target_realisasi).'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }

                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_target_realisasi = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                        }
                                                        $program_rasio = (array_sum($program_tw_target_realisasi)) / $cek_program_target_satuan_rp_realisasi->target;
                                                        $e_80 .= '<td>'.$program_rasio.'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }
                                            $e_80 .= '<td>'.$opd->nama.'</td>';
                                        $e_80 .= '</tr>';
                                    } else {
                                        $e_80 .= '<tr>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                            $e_80 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();
                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $e_80 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_80 .= '<td></td>';
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }

                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_target_realisasi = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                        }
                                                        $e_80 .= '<td>'.array_sum($program_tw_target_realisasi).'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }

                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_target_realisasi = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                        }
                                                        $program_rasio = (array_sum($program_tw_target_realisasi)) / $cek_program_target_satuan_rp_realisasi->target;
                                                        $e_80 .= '<td>'.$program_rasio.'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }
                                            $e_80 .= '<td>'.$opd->nama.'</td>';
                                        $e_80 .= '</tr>';
                                    }
                                    $b++;
                                }

                            $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd){
                                $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd){
                                    $q->where('opd_id', $opd->id);
                                });
                            })->get();
                            $kegiatans = [];
                            foreach ($get_kegiatans as $get_kegiatan) {
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                                            ->latest()->first();
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatans[] = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                        'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                                    ];
                                } else {
                                    $kegiatans[] = [
                                        'id' => $get_kegiatan->id,
                                        'kode' => $get_kegiatan->kode,
                                        'deskripsi' => $get_kegiatan->deskripsi,
                                        'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                                    ];
                                }
                            }
                            foreach($kegiatans as $kegiatan)
                            {
                                $e_80 .= '<tr>';
                                    $e_80 .= '<td>'.$a++.'</td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td>'.$kegiatan['deskripsi'].'</td>';
                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                                        ->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd){
                                                                            $q->where('opd_id', $opd->id);
                                                                        })->get();
                                    $c = 1;
                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                        if($c == 1)
                                        {
                                                $e_80 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                $e_80 .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->first();
                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $e_80 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }

                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();

                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($cek_kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            $program_tw_target_realisasi = [];
                                                            foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                $program_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                            }
                                                            $e_80 .= '<td>'.array_sum($program_tw_target_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_80 .= '<td></td>';
                                                        } else {
                                                            $e_80 .= '<td></td>';
                                                            $e_80 .= '<td></td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }
                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();

                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($cek_kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            $program_tw_target_realisasi = [];
                                                            foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                $program_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                            }
                                                            $kegiatan_rasio = (array_sum($program_tw_target_realisasi)) / $cek_kegiatan_target_satuan_rp_realisasi->target;
                                                            $e_80 .= '<td>'.$kegiatan_rasio.'</td>';
                                                            $e_80 .= '<td></td>';
                                                        } else {
                                                            $e_80 .= '<td></td>';
                                                            $e_80 .= '<td></td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }
                                                $e_80 .= '<td>'.$opd->nama.'</td>';
                                            $e_80 .= '</tr>';
                                        } else {
                                            $e_80 .= '<tr>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                $e_80 .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->first();
                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $e_80 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }

                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();

                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($cek_kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            $program_tw_target_realisasi = [];
                                                            foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                $program_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                            }
                                                            $e_80 .= '<td>'.array_sum($program_tw_target_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_80 .= '<td></td>';
                                                        } else {
                                                            $e_80 .= '<td></td>';
                                                            $e_80 .= '<td></td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }
                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();

                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($cek_kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            $program_tw_target_realisasi = [];
                                                            foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                $program_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                            }
                                                            $kegiatan_rasio = (array_sum($program_tw_target_realisasi)) / $cek_kegiatan_target_satuan_rp_realisasi->target;
                                                            $e_80 .= '<td>'.$kegiatan_rasio.'</td>';
                                                            $e_80 .= '<td></td>';
                                                        } else {
                                                            $e_80 .= '<td></td>';
                                                            $e_80 .= '<td></td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }
                                                $e_80 .= '<td>'.$opd->nama.'</td>';
                                            $e_80 .= '</tr>';
                                        }
                                        $c++;
                                    }
                            }
                        }
                }
            }
        }

        return response()->json([
            'e_80' => $e_80
        ]);
    }

    public function e_80_ekspor_pdf($opd_id)
    {
        $opd = MasterOpd::find($opd_id);

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $tahun_akhir = $get_periode->tahun_akhir;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_tujuans = Tujuan::whereHas('sasaran', function($q) use ($opd){
            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($opd){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd){
                    $q->whereHas('program_rpjmd', function($q) use ($opd){
                        $q->whereHas('program', function($q) use ($opd){
                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd){
                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                    $q->where('opd_id', $opd->id);
                                });
                            });
                        });
                    });
                });
            });
        })->get();

        $tujuans = [];

        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->latest()->first();
            if($cek_perubahan_tujuan)
            {
                $tujuans[] = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'kode' => $cek_perubahan_tujuan->kode,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi
                ];
            } else {
                $tujuans[] = [
                    'id' => $get_tujuan->id,
                    'kode' => $get_tujuan->kode,
                    'deskripsi' => $get_tujuan->deskripsi
                ];
            }
        }

        $e_80 = '';
        $a = 1;
        foreach ($tujuans as $tujuan)
        {
            $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->whereHas('sasaran_indikator_kinerja', function($q) use ($opd){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd){
                    $q->whereHas('program_rpjmd', function($q) use ($opd){
                        $q->whereHas('program', function($q) use ($opd){
                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd){
                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                    $q->where('opd_id', $opd->id);
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
                        'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                        'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan,
                    ];
                } else {
                    $sasarans[] = [
                        'id' => $get_sasaran->id,
                        'kode' => $get_sasaran->kode,
                        'deskripsi' => $get_sasaran->deskripsi,
                        'tahun_perubahan' => $get_sasaran->tahun_perubahan,
                    ];
                }
            }

            foreach ($sasarans as $sasaran)
            {
                $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', $opd->id)->get();
                foreach ($get_sasaran_pds as $get_sasaran_pd)
                {
                    $e_80 .= '<tr>';
                        $e_80 .= '<td>'.$a++.'</td>';
                        $e_80 .= '<td style="text-align:left">'.$get_sasaran_pd->deskripsi.'</td>';
                        $e_80 .= '<td></td>';
                        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $get_sasaran_pd->id)->get();
                        $b = 1;
                        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                            if($b == 1)
                            {
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td></td>';
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $e_80 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td></td>';
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                $e_80 .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td></td>';
                                            } else {
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                            }
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                $rasio_target = $cek_sasaran_pd_realisasi_renja->realisasi/$cek_sasaran_pd_target_satuan_rp_realisasi->target;
                                                $e_80 .= '<td>'.number_format($rasio_target,2,',').'</td>';
                                                $e_80 .= '<td></td>';
                                            } else {
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                            }
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }
                                    $e_80 .= '<td>'.$opd->nama.'</td>';
                                $e_80 .= '</tr>';
                            } else {
                                $e_80 .= '<tr>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td></td>';
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $e_80 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td></td>';
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                $e_80 .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td></td>';
                                            } else {
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                            }
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }

                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                $rasio_target = $cek_sasaran_pd_realisasi_renja->realisasi/$cek_sasaran_pd_target_satuan_rp_realisasi->target;
                                                $e_80 .= '<td>'.number_format($rasio_target,2,',').'</td>';
                                                $e_80 .= '<td></td>';
                                            } else {
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                            }
                                        } else {
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                        }
                                    }
                                    $e_80 .= '<td>'.$opd->nama.'</td>';
                                $e_80 .= '</tr>';
                            }
                            $b++;
                        }

                        $get_programs = Program::whereHas('program_indikator_kinerja', function($q) use ($opd){
                            $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                $q->where('opd_id', $opd->id);
                            });
                        })->whereHas('program_rpjmd', function($q) use ($get_sasaran_pd){
                            $q->whereHas('sasaran_pd_program_rpjmd', function($q) use ($get_sasaran_pd){
                                $q->where('sasaran_pd_id', $get_sasaran_pd->id);
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
                                    'deskripsi' => $cek_perubahan_program->deskripsi,
                                    'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan
                                ];
                            } else {
                                $programs[] = [
                                    'id' => $get_program->id,
                                    'kode' => $get_program->kode,
                                    'deskripsi' => $get_program->deskripsi,
                                    'tahun_perubahan' => $get_program->tahun_perubahan
                                ];
                            }
                        }
                        foreach($programs as $program)
                        {
                            $e_80 .= '<tr>';
                                $e_80 .= '<td>'.$a++.'</td>';
                                $e_80 .= '<td></td>';
                                $e_80 .= '<td>'.$program['deskripsi'].'</td>';
                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                    $q->where('opd_id', $opd->id);
                                })->get();
                                $b = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($b == 1)
                                    {
                                            $e_80 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                            $e_80 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();
                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $e_80 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_80 .= '<td></td>';
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }

                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_target_realisasi = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                        }
                                                        $e_80 .= '<td>'.array_sum($program_tw_target_realisasi).'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }

                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_target_realisasi = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                        }
                                                        $program_rasio = (array_sum($program_tw_target_realisasi)) / $cek_program_target_satuan_rp_realisasi->target;
                                                        $e_80 .= '<td>'.$program_rasio.'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }
                                            $e_80 .= '<td>'.$opd->nama.'</td>';
                                        $e_80 .= '</tr>';
                                    } else {
                                        $e_80 .= '<tr>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                            $e_80 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();
                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $e_80 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_80 .= '<td></td>';
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }

                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_target_realisasi = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                        }
                                                        $e_80 .= '<td>'.array_sum($program_tw_target_realisasi).'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }

                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_program_tw_realisasi)
                                                    {
                                                        $program_tw_target_realisasi = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                        }
                                                        $program_rasio = (array_sum($program_tw_target_realisasi)) / $cek_program_target_satuan_rp_realisasi->target;
                                                        $e_80 .= '<td>'.$program_rasio.'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td></td>';
                                                    $e_80 .= '<td></td>';
                                                }
                                            }
                                            $e_80 .= '<td>'.$opd->nama.'</td>';
                                        $e_80 .= '</tr>';
                                    }
                                    $b++;
                                }

                            $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd){
                                $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd){
                                    $q->where('opd_id', $opd->id);
                                });
                            })->get();
                            $kegiatans = [];
                            foreach ($get_kegiatans as $get_kegiatan) {
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                                            ->latest()->first();
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatans[] = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                        'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                                    ];
                                } else {
                                    $kegiatans[] = [
                                        'id' => $get_kegiatan->id,
                                        'kode' => $get_kegiatan->kode,
                                        'deskripsi' => $get_kegiatan->deskripsi,
                                        'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                                    ];
                                }
                            }
                            foreach($kegiatans as $kegiatan)
                            {
                                $e_80 .= '<tr>';
                                    $e_80 .= '<td>'.$a++.'</td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td>'.$kegiatan['deskripsi'].'</td>';
                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                                        ->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd){
                                                                            $q->where('opd_id', $opd->id);
                                                                        })->get();
                                    $c = 1;
                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                        if($c == 1)
                                        {
                                                $e_80 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                $e_80 .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->first();
                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $e_80 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }

                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();

                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($cek_kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            $program_tw_target_realisasi = [];
                                                            foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                $program_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                            }
                                                            $e_80 .= '<td>'.array_sum($program_tw_target_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_80 .= '<td></td>';
                                                        } else {
                                                            $e_80 .= '<td></td>';
                                                            $e_80 .= '<td></td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }
                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();

                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($cek_kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            $program_tw_target_realisasi = [];
                                                            foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                $program_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                            }
                                                            $kegiatan_rasio = (array_sum($program_tw_target_realisasi)) / $cek_kegiatan_target_satuan_rp_realisasi->target;
                                                            $e_80 .= '<td>'.$kegiatan_rasio.'</td>';
                                                            $e_80 .= '<td></td>';
                                                        } else {
                                                            $e_80 .= '<td></td>';
                                                            $e_80 .= '<td></td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }
                                                $e_80 .= '<td>'.$opd->nama.'</td>';
                                            $e_80 .= '</tr>';
                                        } else {
                                            $e_80 .= '<tr>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                $e_80 .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->first();
                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $e_80 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td></td>';
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }

                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();

                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($cek_kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            $program_tw_target_realisasi = [];
                                                            foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                $program_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                            }
                                                            $e_80 .= '<td>'.array_sum($program_tw_target_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $e_80 .= '<td></td>';
                                                        } else {
                                                            $e_80 .= '<td></td>';
                                                            $e_80 .= '<td></td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }
                                                foreach ($tahuns as $tahun) {
                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();

                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($cek_kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            $program_tw_target_realisasi = [];
                                                            foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                $program_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                            }
                                                            $kegiatan_rasio = (array_sum($program_tw_target_realisasi)) / $cek_kegiatan_target_satuan_rp_realisasi->target;
                                                            $e_80 .= '<td>'.$kegiatan_rasio.'</td>';
                                                            $e_80 .= '<td></td>';
                                                        } else {
                                                            $e_80 .= '<td></td>';
                                                            $e_80 .= '<td></td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td></td>';
                                                        $e_80 .= '<td></td>';
                                                    }
                                                }
                                                $e_80 .= '<td>'.$opd->nama.'</td>';
                                            $e_80 .= '</tr>';
                                        }
                                        $c++;
                                    }
                            }
                        }
                }
            }
        }

        $pdf = PDF::loadView('admin.laporan.e-80', [
            'e_80' => $e_80,
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan E-80.pdf');
    }

    public function e_80_ekspor_excel($opd_id)
    {
        return Excel::download(new E80Ekspor($opd_id), 'Laporan E-80.xlsx');
    }
}
