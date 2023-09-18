<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
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

class Tc24Ekspor implements FromView
{
    protected $opd_id;

    public function __construct($opd_id)
    {
        $this->opd_id = $opd_id;
    }

    public function view(): View
    {
        $opd_id = $this->opd_id;
        $tc_24 = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $tahun_akhir = $get_periode->tahun_akhir;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tws = MasterTw::all();

        $opd = MasterOpd::find($opd_id);

        $a = 1;
        $get_visis = Visi::whereHas('misi', function($q) use ($opd){
            $q->whereHas('tujuan', function($q) use ($opd){
                $q->whereHas('sasaran', function($q) use ($opd){
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
                });
            });
        })->where('tahun_periode_id', $get_periode->id)->get();

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

        foreach ($visis as $visi)
        {
            $get_misis = Misi::where('visi_id', $visi['id'])->whereHas('tujuan', function($q) use ($opd){
                $q->whereHas('sasaran', function($q) use ($opd){
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

            foreach ($misis as $misi)
            {
                $get_tujuans = Tujuan::where('misi_id', $misi['id'])->whereHas('sasaran', function($q) use ($opd){
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
                        $get_programs = Program::whereHas('program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                                $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                    $q->whereHas('sasaran', function($q) use ($sasaran) {
                                        $q->where('id', $sasaran['id']);
                                    });
                                });
                            });
                            })->whereHas('program_indikator_kinerja', function($q) use ($opd){
                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                    $q->where('opd_id', $opd->id);
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
                                                            ->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                                                $q->where('opd_id', $opd->id);
                                                            })->get();
                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja)
                            {
                                $tc_24 .= '<tr>';
                                    $tc_24 .= '<td style="text-align:left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                    // Kolom 2 - 6 Start
                                    foreach ($tahuns as $tahun) {
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                                                        $q->where('opd_id', $opd->id);
                                                                                        $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                                                            $q->where('id', $program_indikator_kinerja->id);
                                                                                        });
                                                                                    })->where('tahun', $tahun)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $tc_24 .= '<td>Rp.'.number_format($program_target_satuan_rp_realisasi->target_rp, 2, ',','.').'</td>';
                                        } else {
                                            $tc_24 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }
                                    // Kolom 2 - 6 End

                                    // Kolom 7 - 11 Start
                                    foreach ($tahuns as $tahun) {
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                            $q->where('opd_id', $opd->id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $realisasi_rp_kolom_7_11 = [];
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $realisasi_rp_kolom_7_11[] = $cek_program_tw_realisasi->realisasi_rp;
                                                } else {
                                                    $realisasi_rp_kolom_7_11[] = 0;
                                                }
                                            }
                                            $tc_24 .= '<td>Rp. '.number_format(array_sum($realisasi_rp_kolom_7_11), 2, ',', '.').'</td>';
                                        } else {
                                            $tc_24 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }
                                    // Kolom 7 - 11 End

                                    // Kolom 12 - 16 Start
                                    foreach ($tahuns as $tahun) {
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                            $q->where('opd_id', $opd->id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $target_rp_kolom_12_6 = $program_target_satuan_rp_realisasi->target_rp;
                                            $realisasi_rp_kolom_12_16 = [];
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $realisasi_rp_kolom_12_16[] = $cek_program_tw_realisasi->realisasi_rp;
                                                } else {
                                                    $realisasi_rp_kolom_12_16[] = 0;
                                                }
                                            }
                                            if($target_rp_kolom_12_6 != 0)
                                            {
                                                $rasio_kolom_12_16 = (array_sum($realisasi_rp_kolom_12_16) / $target_rp_kolom_12_6) * 100;
                                            } else {
                                                $rasio_kolom_12_16 = 0;
                                            }
                                            $tc_24 .= '<td>'.number_format($rasio_kolom_12_16, 2, ',', '.').'</td>';
                                        } else {
                                            $tc_24 .= '<td>0, 00</td>';
                                        }
                                    }
                                    // Kolom 12 - 16 End
                                $tc_24 .= '</tr>';
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
                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                                    ->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd){
                                                                        $q->where('opd_id', $opd->id);
                                                                    })->get();
                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja)
                                {
                                    $tc_24 .= '<tr>';
                                        $tc_24 .= '<td style="text-align:left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                        foreach ($tahuns as $tahun) {
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                                                            $q->where('opd_id', $opd->id);
                                                                                            $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                                                $q->where('id', $kegiatan_indikator_kinerja->id);
                                                                                            });
                                                                                        })->where('tahun', $tahun)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $tc_24 .= '<td>Rp.'.number_format((int)$kegiatan_target_satuan_rp_realisasi->target_rp,2, ',', '.').'</td>';
                                            } else {
                                                $tc_24 .= '<td>Rp. 0, 00</td>';
                                            }
                                        }
                                        // Kolom 7 - 11 Start
                                        foreach ($tahuns as $tahun) {
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                $q->where('opd_id', $opd->id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $realisasi_rp_kolom_7_11 = [];
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $realisasi_rp_kolom_7_11[] = $cek_kegiatan_tw_realisasi->realisasi_rp;
                                                    } else {
                                                        $realisasi_rp_kolom_7_11[] = 0;
                                                    }
                                                }
                                                $tc_24 .= '<td>Rp. '.number_format(array_sum($realisasi_rp_kolom_7_11), 2, ',', '.').'</td>';
                                            } else {
                                                $tc_24 .= '<td>Rp. 0, 00</td>';
                                            }
                                        }
                                        // Kolom 7 - 11 End

                                        // Kolom 12 - 16 Start
                                        foreach ($tahuns as $tahun) {
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                $q->where('opd_id', $opd->id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $target_rp_kolom_12_6 = (int)$kegiatan_target_satuan_rp_realisasi->target_rp;
                                                $realisasi_rp_kolom_12_16 = [];
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $realisasi_rp_kolom_12_16[] = (int)$cek_kegiatan_tw_realisasi->realisasi_rp;
                                                    } else {
                                                        $realisasi_rp_kolom_12_16[] = 0;
                                                    }
                                                }
                                                if($target_rp_kolom_12_6 != 0)
                                                {
                                                    $rasio_kolom_12_16 = (array_sum($realisasi_rp_kolom_12_16) / $target_rp_kolom_12_6) * 100;
                                                } else {
                                                    $rasio_kolom_12_16 = 0;
                                                }
                                                $tc_24 .= '<td>'.number_format($rasio_kolom_12_16, 2, ',', '.').'</td>';
                                            } else {
                                                $tc_24 .= '<td>0, 00</td>';
                                            }
                                        }
                                        // Kolom 12 - 16 End
                                    $tc_24 .= '</tr>';
                                }

                                $sub_kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                                    ->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd){
                                                                        $q->where('opd_id', $opd->id);
                                                                    })->get();
                                foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                    $tc_24 .= '<tr>';
                                        $tc_24 .= '<td style="text-align:left;">'.$sub_kegiatan_indikator_kinerja->deskripsi.'</td>';
                                        // Kolom 2 - 6 Start
                                        foreach ($tahuns as $tahun) {
                                            $sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd){
                                                                                            $q->where('opd_id', $opd->id);
                                                                                            $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                                                                $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                                                            });
                                                                                        })->where('tahun', $tahun)->first();
                                            if($sub_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $tc_24 .= '<td>Rp.'.number_format($sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_awal, 2, ',', '.').'</td>';
                                            } else {
                                                $tc_24 .= '<td>Rp. 0, 00</td>';
                                            }
                                        }
                                        // Kolom 2 - 6 End

                                        // Kolom 7 - 11 Start
                                        foreach ($tahuns as $tahun) {
                                            $sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd){
                                                                                            $q->where('opd_id', $opd->id);
                                                                                            $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                                                                $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                                                            });
                                                                                        })->where('tahun', $tahun)->first();
                                            if($sub_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                if($cek_sub_kegiatan_tw_realisasi_renja)
                                                {
                                                    $sub_kegiatan_tw_realisasi_renjas = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                    $realisasi_rp = [];
                                                    foreach ($sub_kegiatan_tw_realisasi_renjas as $sub_kegiatan_tw_realisasi_renja) {
                                                        $realisasi_rp[] = $sub_kegiatan_tw_realisasi_renja->realisasi_rp;
                                                    }
                                                    $tc_23 .= '<td>Rp.'.number_format(array_sum($realisasi_rp), 2, ',', '.').'</td>';
                                                } else {
                                                    $tc_24 .= '<td>Rp. 0, 00</td>';
                                                }
                                            } else {
                                                $tc_24 .= '<td>Rp. 0, 00</td>';
                                            }
                                        }
                                        // Kolom 7 - 11 End

                                        // Kolom 12 - 16 Start
                                        foreach ($tahuns as $tahun) {
                                            $sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd){
                                                                                            $q->where('opd_id', $opd->id);
                                                                                            $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                                                                $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                                                            });
                                                                                        })->where('tahun', $tahun)->first();
                                            if($sub_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                if($cek_sub_kegiatan_tw_realisasi_renja)
                                                {
                                                    $sub_kegiatan_tw_realisasi_renjas = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                    $realisasi_rp = [];
                                                    foreach ($sub_kegiatan_tw_realisasi_renjas as $sub_kegiatan_tw_realisasi_renja) {
                                                        $realisasi_rp[] = $sub_kegiatan_tw_realisasi_renja->realisasi_rp;
                                                    }
                                                    $rasio = (array_sum($realisasi_rp) / $sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_awal) * 100;
                                                    $tc_23 .= '<td>'.$rasio.'</td>';
                                                } else {
                                                    $tc_24 .= '<td>0,00</td>';
                                                }
                                            } else {
                                                $tc_24 .= '<td>0,00</td>';
                                            }
                                        }
                                        // Kolom 12 - 16 End
                                        $tc_24 .= '<td></td>';
                                        $tc_24 .= '<td></td>';
                                    $tc_24 .= '</tr>';
                                }
                            }
                        }
                    }
                }
            }
        }

        return view('admin.laporan.tc-24', [
            'tc_24' => $tc_24
        ]);
    }
}
