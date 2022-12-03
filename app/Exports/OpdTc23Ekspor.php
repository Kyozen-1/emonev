<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;
use Auth;
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

class OpdTc23Ekspor implements FromView
{
    public function view():View
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $tahun_akhir = $get_periode->tahun_akhir;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tc_23 = '';
        $a = 1;
        $get_visis = Visi::whereHas('misi', function($q){
            $q->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
                    $q->whereHas('sasaran_indikator_kinerja', function($q){
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
                    });
                });
            });
        })->get();

        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $visis[] = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'deskripsi' => $cek_perubahan_visi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_visi->tahun_perubahan
                ];
            } else {
                $visis[] = [
                    'id' => $get_visi->id,
                    'deskripsi' => $get_visi->deskripsi,
                    'tahun_perubahan' => $get_visi->tahun_perubahan
                ];
            }
        }

        foreach ($visis as $visi) {
            $get_misis = Misi::where('visi_id', $visi['id'])->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
                    $q->whereHas('sasaran_indikator_kinerja', function($q){
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
                    });
                });
            })->get();
            $misis = [];
            foreach ($get_misis as $get_misi) {
                $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                        ->latest()
                                        ->first();
                if($cek_perubahan_misi)
                {
                    $misis[] = [
                        'id' => $cek_perubahan_misi->misi_id,
                        'kode' => $cek_perubahan_misi->kode,
                        'deskripsi' => $cek_perubahan_misi->deskripsi,
                        'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan
                    ];
                } else {
                    $misis[] = [
                        'id' => $get_misi->id,
                        'kode' => $get_misi->kode,
                        'deskripsi' => $get_misi->deskripsi,
                        'tahun_perubahan' => $get_misi->tahun_perubahan
                    ];
                }
            }

            foreach ($misis as $misi) {
                $get_tujuans = Tujuan::where('misi_id', $misi['id'])->whereHas('sasaran', function($q){
                    $q->whereHas('sasaran_indikator_kinerja', function($q){
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
                    });
                })->orderBy('kode', 'asc')->get();
                $tujuans = [];
                foreach ($get_tujuans as $get_tujuan) {
                    $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                                ->latest()
                                                ->first();
                    if($cek_perubahan_tujuan)
                    {
                        $tujuans[] = [
                            'id' => $cek_perubahan_tujuan->tujuan_id,
                            'kode' => $cek_perubahan_tujuan->kode,
                            'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                            'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan,
                        ];
                    } else {
                        $tujuans[] = [
                            'id' => $get_tujuan->id,
                            'kode' => $get_tujuan->kode,
                            'deskripsi' => $get_tujuan->deskripsi,
                            'tahun_perubahan' => $get_tujuan->tahun_perubahan,
                        ];
                    }
                }
                foreach ($tujuans as $tujuan) {
                    $get_tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
                    foreach ($get_tujuan_pds as $get_tujuan_pd) {
                        $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $get_tujuan_pd->id)->get();
                        foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                            $tc_23 .= '<tr>';
                                $tc_23 .= '<td style="text-align:left;">'.$a++.'</td>';
                                $tc_23 .= '<td style="text-align:left;">'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $tc_23 .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $tc_23 .= '<td></td>';
                                    $tc_23 .= '<td></td>';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $tc_23 .= '<td></td>';
                                    $tc_23 .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $tc_23 .= '<td></td>';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $tc_23 .= '<td></td>';
                                    $tc_23 .= '<td></td>';
                                    $tc_23 .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                }
                                foreach ($tahuns as $tahun) {
                                    $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun)->first();
                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                    {
                                        $tc_23 .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'/'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $tc_23 .= '<td></td>';
                                    }
                                }
                                foreach ($tahuns as $tahun) {
                                    $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun)->first();
                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                    {
                                        $tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                        if($tujuan_pd_realisasi_renja)
                                        {
                                            $tc_23 .= '<td>'.$tujuan_pd_realisasi_renja->realisasi.'/'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                        } else {
                                            $tc_23 .= '<td></td>';
                                        }
                                    } else {
                                        $tc_23 .= '<td></td>';
                                    }
                                }
                                foreach ($tahuns as $tahun) {
                                    $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun)->first();
                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                    {
                                        $tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                        if($tujuan_pd_realisasi_renja)
                                        {
                                            $rasio = $tujuan_pd_realisasi_renja->realisasi / $tujuan_pd_target_satuan_rp_realisasi->target;
                                            $tc_23 .= '<td>'.number_format($rasio, 2, ',').'</td>';
                                        } else {
                                            $tc_23 .= '<td></td>';
                                        }
                                    } else {
                                        $tc_23 .= '<td></td>';
                                    }
                                }
                            $tc_23 .= '</tr>';
                        }
                    }

                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->whereHas('sasaran_indikator_kinerja', function($q){
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
                        $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
                        foreach ($get_sasaran_pds as $get_sasaran_pd) {
                            $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $get_sasaran_pd->id)->get();
                            foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                $tc_23 .= '<tr>';
                                    $tc_23 .= '<td style="text-align:left;">'.$a++.'</td>';
                                    $tc_23 .= '<td style="text-align:left;">'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    if($sasaran_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                    {
                                        $tc_23 .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                        $tc_23 .= '<td></td>';
                                        $tc_23 .= '<td></td>';
                                    }
                                    if($sasaran_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                    {
                                        $tc_23 .= '<td></td>';
                                        $tc_23 .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                        $tc_23 .= '<td></td>';
                                    }
                                    if($sasaran_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                    {
                                        $tc_23 .= '<td></td>';
                                        $tc_23 .= '<td></td>';
                                        $tc_23 .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    }
                                    foreach ($tahuns as $tahun) {
                                        $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun)->first();
                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $tc_23 .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        } else {
                                            $tc_23 .= '<td></td>';
                                        }
                                    }
                                    foreach ($tahuns as $tahun) {
                                        $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun)->first();
                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($sasaran_pd_realisasi_renja)
                                            {
                                                $tc_23 .= '<td>'.$sasaran_pd_realisasi_renja->realisasi.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            } else {
                                                $tc_23 .= '<td></td>';
                                            }
                                        } else {
                                            $tc_23 .= '<td></td>';
                                        }
                                    }
                                    foreach ($tahuns as $tahun) {
                                        $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun)->first();
                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($sasaran_pd_realisasi_renja)
                                            {
                                                $rasio = $sasaran_pd_realisasi_renja->realisasi / $sasaran_pd_target_satuan_rp_realisasi->target;
                                                $tc_23 .= '<td>'.number_format($rasio, 2, ',').'</td>';
                                            } else {
                                                $tc_23 .= '<td></td>';
                                            }
                                        } else {
                                            $tc_23 .= '<td></td>';
                                        }
                                    }
                                $tc_23 .= '</tr>';
                            }
                        }

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
                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])
                                                            ->whereHas('opd_program_indikator_kinerja', function($q){
                                                                $q->where('opd_id', Auth::user()->opd->opd_id);
                                                            })->get();
                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                $tc_23 .= '<tr>';
                                    $tc_23 .= '<td style="text-align:left;">'.$a++.'</td>';
                                    $tc_23 .= '<td style="text-align:left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                    $tc_23 .= '<td></td>';
                                    $tc_23 .= '<td></td>';
                                    $tc_23 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    foreach ($tahuns as $tahun) {
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->where('tahun', $tahun)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $tc_23 .= '<td>'.$program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                        } else {
                                            $tc_23 .= '<td></td>';
                                        }
                                    }
                                    foreach ($tahuns as $tahun) {
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                            if($cek_program_tw_realisasi_renja)
                                            {
                                                $program_tw_realisasi_renjas = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->get();
                                                $realisasi = [];
                                                foreach ($program_tw_realisasi_renjas as $program_tw_realisasi_renja) {
                                                    $realisasi[] = $program_tw_realisasi_renja->realisasi;
                                                }
                                                $tc_23 .= '<td>'.array_sum($realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            } else {
                                                $tc_23 .= '<td></td>';
                                            }
                                        } else {
                                            $tc_23 .= '<td></td>';
                                        }
                                    }
                                    foreach ($tahuns as $tahun) {
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->first();
                                            if($cek_program_tw_realisasi_renja)
                                            {
                                                $program_tw_realisasi_renjas = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->get();
                                                $realisasi = [];
                                                foreach ($program_tw_realisasi_renjas as $program_tw_realisasi_renja) {
                                                    $realisasi[] = $program_tw_realisasi_renja->realisasi;
                                                }
                                                $rasio = (array_sum($realisasi))/$program_target_satuan_rp_realisasi->target;
                                                $tc_23 .= '<td>'.number_format($rasio, 2, ',').'</td>';
                                            } else {
                                                $tc_23 .= '<td></td>';
                                            }
                                        } else {
                                            $tc_23 .= '<td></td>';
                                        }
                                    }
                                $tc_23 .= '</tr>';
                            }

                            $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q){
                                $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                    $q->where('opd_id', Auth::user()->opd->opd_id);
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
                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                                    ->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                    })->get();
                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                    $tc_23 .= '<tr>';
                                        $tc_23 .= '<td style="text-align:left;">'.$a++.'</td>';
                                        $tc_23 .= '<td style="text-align:left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                        if($kegiatan_indikator_kinerja->status_indikator == 'Target NSPK')
                                        {
                                            $tc_23 .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                            $tc_23 .= '<td></td>';
                                            $tc_23 .= '<td></td>';
                                        }
                                        if($kegiatan_indikator_kinerja->status_indikator == 'Target IKK')
                                        {
                                            $tc_23 .= '<td></td>';
                                            $tc_23 .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                            $tc_23 .= '<td></td>';
                                        }
                                        if($kegiatan_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                        {
                                            $tc_23 .= '<td></td>';
                                            $tc_23 .= '<td></td>';
                                            $tc_23 .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                        }
                                        foreach ($tahuns as $tahun) {
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                            $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                                                $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                                            });
                                                                                        })->where('tahun', $tahun)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $tc_23 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            } else {
                                                $tc_23 .= '<td></td>';
                                            }
                                        }
                                        foreach ($tahuns as $tahun) {
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                $q->where('opd_id', Auth::user()->opd->opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->first();
                                                if($cek_kegiatan_tw_realisasi_renja)
                                                {
                                                    $kegiatan_tw_realisasi_renjas = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->get();
                                                    $realisasi = [];
                                                    foreach ($kegiatan_tw_realisasi_renjas as $kegiatan_tw_realisasi_renja) {
                                                        $realisasi[] = $kegiatan_tw_realisasi_renja->realisasi;
                                                    }
                                                    $tc_23 .= '<td>'.array_sum($realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                } else {
                                                    $tc_23 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_23 .= '<td></td>';
                                            }
                                        }
                                        foreach ($tahuns as $tahun) {
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                $q->where('opd_id', Auth::user()->opd->opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->first();
                                                if($cek_kegiatan_tw_realisasi_renja)
                                                {
                                                    $kegiatan_tw_realisasi_renjas = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->get();
                                                    $realisasi = [];
                                                    foreach ($kegiatan_tw_realisasi_renjas as $kegiatan_tw_realisasi_renja) {
                                                        $realisasi[] = $kegiatan_tw_realisasi_renja->realisasi;
                                                    }
                                                    $rasio = (array_sum($realisasi))/$kegiatan_target_satuan_rp_realisasi->target;
                                                    $tc_23 .= '<td>'.number_format($rasio, 2, ',').'</td>';
                                                } else {
                                                    $tc_23 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_23 .= '<td></td>';
                                            }
                                        }
                                    $tc_23 .= '</tr>';
                                }
                            }
                        }
                    }
                }
            }
        }

        return view('opd.laporan.tc-23', ['tc_23' => $tc_23]);
    }
}
