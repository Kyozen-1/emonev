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

class Tc14Ekspor implements FromView
{
    public function view():View
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $new_get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $new_tahun_awal = $new_get_periode->tahun_awal-1;
        $new_jarak_tahun = $new_get_periode->tahun_akhir - $new_tahun_awal;
        $new_tahun_akhir = $new_get_periode->tahun_akhir;
        $new_tahuns = [];
        for ($i=0; $i < $new_jarak_tahun + 1; $i++) {
            $new_tahuns[] = $new_tahun_awal + $i;
        }

        $get_visis = Visi::where('tahun_periode_id', $get_periode->id)->whereHas('misi', function($q){
            $q->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
                    $q->whereHas('sasaran_indikator_kinerja', function($q){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                            $q->whereHas('program_rpjmd');
                        });
                    });
                });
            });
        })->get();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)
                                    ->orderBy('tahun_perubahan', 'desc')
                                    ->latest()->first();
            if($cek_perubahan_visi)
            {
                $visis[] = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'deskripsi' => $cek_perubahan_visi->deskripsi
                ];
            } else {
                $visis[] = [
                    'id' => $get_visi->id,
                    'deskripsi' => $get_visi->deskripsi
                ];
            }
        }

        // TC 14 Start
        $tc_14 = '';
        foreach ($visis as $visi) {
            $get_misis = Misi::where('visi_id', $visi['id'])->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
                    $q->whereHas('sasaran_indikator_kinerja', function($q){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                            $q->whereHas('program_rpjmd', function($q){
                                $q->whereHas('program', function($q){
                                    $q->whereHas('program_indikator_kinerja', function($q){
                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                            $q->whereHas('opd');
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
                                        ->orderBy('tahun_perubahan', 'desc')
                                        ->latest()->first();
                if($cek_perubahan_misi)
                {
                    $misis[] = [
                        'id' => $cek_perubahan_misi->misi_id,
                        'kode' => $cek_perubahan_misi->kode,
                        'deskripsi' => $cek_perubahan_misi->deskripsi
                    ];
                } else {
                    $misis[] = [
                        'id' => $get_misi->id,
                        'kode' => $get_misi->kode,
                        'deskripsi' => $get_misi->deskripsi
                    ];
                }
            }
            // Misi
            foreach ($misis as $misi) {
                $tc_14 .= '<tr>';
                    $tc_14 .= '<td>'.$misi['kode'].'</td>';
                    $tc_14 .= '<td></td>';
                    $tc_14 .= '<td></td>';
                    $tc_14 .= '<td style="text-align:left">'.$misi['deskripsi'].'</td>';
                    $tc_14 .= '<td colspan="15"></td>';
                $tc_14 .= '</tr>';

                $get_tujuans = Tujuan::where('misi_id', $misi['id'])->whereHas('sasaran', function($q){
                                    $q->whereHas('sasaran_indikator_kinerja', function($q){
                                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                            $q->whereHas('program_rpjmd', function($q){
                                                $q->whereHas('program', function($q){
                                                    $q->whereHas('program_indikator_kinerja', function($q){
                                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                            $q->whereHas('opd');
                                                        });
                                                    });
                                                });
                                            });
                                        });
                                    });
                                })->get();
                $tujuans = [];
                foreach ($get_tujuans as $get_tujuan) {
                    $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                        ->orderBy('tahun_perubahan', 'desc')
                                        ->latest()->first();
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

                foreach ($tujuans as $tujuan) {
                    $tc_14 .= '<tr>';
                        $tc_14 .= '<td>'.$misi['kode'].'</td>';
                        $tc_14 .= '<td>'.$tujuan['kode'].'</td>';
                        $tc_14 .= '<td></td>';
                        $tc_14 .= '<td style="text-align:left">'.$tujuan['deskripsi'].'</td>';

                        $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();

                        $a = 1;
                        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                            if($a == 1)
                            {
                                    $tc_14 .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    // Kolom 4 Start
                                    $tc_14 .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    // Kolom 4 End

                                    // Kolom 5 - 14 Start
                                    foreach ($tahuns as $tahun) {
                                        $tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                            ->where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                            ->first();
                                        if($tujuan_target_satuan_rp_realisasi)
                                        {
                                            $tc_14 .= '<td>'.$tujuan_target_satuan_rp_realisasi->target.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                        } else {
                                            $tc_14 .= '<td>0/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                        }
                                        $tc_14 .= '<td>Rp. 0, 00</td>';
                                    }
                                    // Kolom 5 - 14 End

                                    // Kolom 15 - 16 Start
                                    $tujuan_target_satuan_rp_realisasi_kolom_15_16 = TujuanTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                            ->where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                            ->first();
                                    if($tujuan_target_satuan_rp_realisasi_kolom_15_16)
                                    {
                                        $tc_14 .= '<td>'.$tujuan_target_satuan_rp_realisasi_kolom_15_16->target.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $tc_14 .= '<td>0/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                    }

                                    $tc_14 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 15 - 16 End
                                    $opd_tujuans = OpdProgramIndikatorKinerja::whereHas('program_indikator_kinerja', function($q) use ($tujuan_indikator_kinerja){
                                        $q->whereHas('program', function($q) use ($tujuan_indikator_kinerja){
                                            $q->whereHas('program_rpjmd', function($q) use ($tujuan_indikator_kinerja){
                                                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($tujuan_indikator_kinerja){
                                                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($tujuan_indikator_kinerja){
                                                        $q->whereHas('sasaran', function($q) use ($tujuan_indikator_kinerja){
                                                            $q->whereHas('tujuan', function($q) use ($tujuan_indikator_kinerja){
                                                                $q->whereHas('tujuan_indikator_kinerja', function($q) use ($tujuan_indikator_kinerja){
                                                                    $q->where('id', $tujuan_indikator_kinerja->id);
                                                                });
                                                            });
                                                        });
                                                    });
                                                });
                                            });
                                        });
                                    })->get();

                                    $nama_opd_tujuan = [];
                                    foreach ($opd_tujuans as $opd_tujuan) {
                                        $nama_opd_tujuan[] = $opd_tujuan->opd->nama;
                                    }
                                    $tc_14 .= '<td>';
                                        $tc_14 .= '<ul>';
                                        foreach (array_unique($nama_opd_tujuan) as $item) {
                                            $tc_14 .= '<li>'.$item.'</li>';
                                        }
                                        $tc_14 .= '</ul>';
                                    $tc_14 .= '</td>';
                                $tc_14 .= '</tr>';
                            } else {
                                $tc_14 .= '<tr>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    // Kolom 4 Start
                                    $tc_14 .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    // Kolom 4 End
                                    // Kolom 5 - 14 Start
                                    foreach ($tahuns as $tahun) {
                                        $tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                            ->where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                            ->first();

                                        if($tujuan_target_satuan_rp_realisasi)
                                        {
                                            $tc_14 .= '<td>'.$tujuan_target_satuan_rp_realisasi->target.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                        } else {
                                            $tc_14 .= '<td>0/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                        }
                                        $tc_14 .= '<td>Rp. 0, 00</td>';
                                    }
                                    // Kolom 5 - 14 End

                                    // Kolom 15 - 16 Start
                                    $tujuan_target_satuan_rp_realisasi_kolom_15_16 = TujuanTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                            ->where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                            ->first();
                                    if($tujuan_target_satuan_rp_realisasi_kolom_15_16)
                                    {
                                        $tc_14 .= '<td>'.$tujuan_target_satuan_rp_realisasi_kolom_15_16->target.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $tc_14 .= '<td>0/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                    }

                                    $tc_14 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 15 - 16 End

                                    $opd_tujuans = OpdProgramIndikatorKinerja::whereHas('program_indikator_kinerja', function($q) use ($tujuan_indikator_kinerja){
                                        $q->whereHas('program', function($q) use ($tujuan_indikator_kinerja){
                                            $q->whereHas('program_rpjmd', function($q) use ($tujuan_indikator_kinerja){
                                                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($tujuan_indikator_kinerja){
                                                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($tujuan_indikator_kinerja){
                                                        $q->whereHas('sasaran', function($q) use ($tujuan_indikator_kinerja){
                                                            $q->whereHas('tujuan', function($q) use ($tujuan_indikator_kinerja){
                                                                $q->whereHas('tujuan_indikator_kinerja', function($q) use ($tujuan_indikator_kinerja){
                                                                    $q->where('id', $tujuan_indikator_kinerja->id);
                                                                });
                                                            });
                                                        });
                                                    });
                                                });
                                            });
                                        });
                                    })->get();

                                    $nama_opd_tujuan = [];
                                    foreach ($opd_tujuans as $opd_tujuan) {
                                        $nama_opd_tujuan[] = $opd_tujuan->opd->nama;
                                    }
                                    $tc_14 .= '<td>';
                                        $tc_14 .= '<ul>';
                                        foreach (array_unique($nama_opd_tujuan) as $item) {
                                            $tc_14 .= '<li>'.$item.'</li>';
                                        }
                                        $tc_14 .= '</ul>';
                                    $tc_14 .= '</td>';
                                $tc_14 .= '</tr>';
                            }
                            $a++;
                        }

                        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q){
                                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                $q->whereHas('program_rpjmd', function($q){
                                                    $q->whereHas('program', function($q){
                                                        $q->whereHas('program_indikator_kinerja', function($q){
                                                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                $q->whereHas('opd');
                                                            });
                                                        });
                                                    });
                                                });
                                            });
                                        })->get();
                        $sasarans = [];
                        foreach ($get_sasarans as $get_sasaran) {
                            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
                                                ->orderBy('tahun_perubahan', 'desc')
                                                ->latest()->first();
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

                        foreach ($sasarans as $sasaran) {
                            $tc_14 .= '<tr>';
                                $tc_14 .= '<td>'.$misi['kode'].'</td>';
                                $tc_14 .= '<td>'.$tujuan['kode'].'</td>';
                                $tc_14 .= '<td>'.$sasaran['kode'].'</td>';
                                $tc_14 .= '<td style="text-align:left">'.$sasaran['deskripsi'].'</td>';

                                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                $b = 1;
                                foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                    if($b == 1)
                                    {
                                            $tc_14 .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                            // Kolom 4 Start
                                            $tc_14 .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                            // Kolom 4 End
                                            // Kolom 5 - 14 Start
                                            foreach ($tahuns as $tahun) {
                                                $sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                    ->first();
                                                if($sasaran_target_satuan_rp_realisasi)
                                                {
                                                    $tc_14 .= '<td>'.$sasaran_target_satuan_rp_realisasi->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                } else {
                                                    $tc_14 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                }
                                                $tc_14 .= '<td>Rp. 0, 00</td>';
                                            }
                                            // Kolom 5 - 14 End
                                            // Kolom 15 - 16 Start
                                            $sasaran_target_satuan_rp_realisasi_kolom_15_16 = SasaranTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                            ->where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                            ->first();
                                            if($sasaran_target_satuan_rp_realisasi_kolom_15_16)
                                            {
                                                $tc_14 .= '<td>'.$sasaran_target_satuan_rp_realisasi_kolom_15_16->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                            } else {
                                                $tc_14 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                            }
                                            $tc_14 .= '<td>Rp. 0, 00</td>';
                                            // Kolom 15 - 16 End
                                            $opd_sasarans = OpdProgramIndikatorKinerja::whereHas('program_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                $q->whereHas('program', function($q) use ($sasaran_indikator_kinerja){
                                                    $q->whereHas('program_rpjmd', function($q) use ($sasaran_indikator_kinerja){
                                                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran_indikator_kinerja){
                                                            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                $q->whereHas('sasaran', function($q) use ($sasaran_indikator_kinerja){
                                                                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                        $q->where('id', $sasaran_indikator_kinerja->id);
                                                                    });
                                                                });
                                                            });
                                                        });
                                                    });
                                                });
                                            })->get();

                                            $nama_opd_sasaran = [];
                                            foreach ($opd_sasarans as $opd_sasaran) {
                                                $nama_opd_sasaran[] = $opd_sasaran->opd->nama;
                                            }
                                            $tc_14 .= '<td>';
                                                $tc_14 .= '<ul>';
                                                foreach (array_unique($nama_opd_sasaran) as $item) {
                                                    $tc_14 .= '<li>'.$item.'</li>';
                                                }
                                                $tc_14 .= '</ul>';
                                            $tc_14 .= '</td>';
                                        $tc_14 .= '</tr>';
                                    } else {
                                        $tc_14 .= '<tr>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                            // Kolom 4 Start
                                            $tc_14 .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                            // Kolom 4 End
                                            // Kolom 5 - 14 Start
                                            foreach ($tahuns as $tahun) {
                                                $sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('tahun', $tahun)
                                                                                    ->where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                    ->first();
                                                if($sasaran_target_satuan_rp_realisasi)
                                                {
                                                    $tc_14 .= '<td>'.$sasaran_target_satuan_rp_realisasi->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                } else {
                                                    $tc_14 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                }
                                                $tc_14 .= '<td>Rp. 0, 00</td>';
                                            }
                                            // Kolom 5 - 14 End
                                            // Kolom 15 - 16 Start
                                            $sasaran_target_satuan_rp_realisasi_kolom_15_16 = SasaranTargetSatuanRpRealisasi::where('tahun', end($tahuns))
                                                                                            ->where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                            ->first();
                                            if($sasaran_target_satuan_rp_realisasi_kolom_15_16)
                                            {
                                                $tc_14 .= '<td>'.$sasaran_target_satuan_rp_realisasi_kolom_15_16->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                            } else {
                                                $tc_14 .= '<td>0/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                            }
                                            $tc_14 .= '<td>Rp. 0, 00</td>';
                                            // Kolom 15 - 16 End
                                            $opd_sasarans = OpdProgramIndikatorKinerja::whereHas('program_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                $q->whereHas('program', function($q) use ($sasaran_indikator_kinerja){
                                                    $q->whereHas('program_rpjmd', function($q) use ($sasaran_indikator_kinerja){
                                                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran_indikator_kinerja){
                                                            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                $q->whereHas('sasaran', function($q) use ($sasaran_indikator_kinerja){
                                                                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran_indikator_kinerja){
                                                                        $q->where('id', $sasaran_indikator_kinerja->id);
                                                                    });
                                                                });
                                                            });
                                                        });
                                                    });
                                                });
                                            })->get();

                                            $nama_opd_sasaran = [];
                                            foreach ($opd_sasarans as $opd_sasaran) {
                                                $nama_opd_sasaran[] = $opd_sasaran->opd->nama;
                                            }
                                            $tc_14 .= '<td>';
                                                $tc_14 .= '<ul>';
                                                foreach (array_unique($nama_opd_sasaran) as $item) {
                                                    $tc_14 .= '<li>'.$item.'</li>';
                                                }
                                                $tc_14 .= '</ul>';
                                            $tc_14 .= '</td>';
                                        $tc_14 .= '</tr>';
                                    }
                                    $b++;
                                }

                            $get_programs = Program::whereHas('program_rpjmd', function($q) use ($sasaran){
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
                                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)
                                                    ->orderBy('tahun_perubahan', 'desc')
                                                    ->latest()->first();
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
                                $tc_14 .= '<tr>';
                                    $tc_14 .= '<td>'.$misi['kode'].'</td>';
                                    $tc_14 .= '<td>'.$tujuan['kode'].'</td>';
                                    $tc_14 .= '<td>'.$sasaran['kode'].'</td>';
                                    $tc_14 .= '<td style="text-align:left">'.$program['deskripsi'].'</td>';
                                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                    $c = 1;
                                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                        if($c == 1)
                                        {
                                                $tc_14 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                                // Kolom 4 Start
                                                $tc_14 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                // Kolom 4 End
                                                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                $d = 1;
                                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                    if($d == 1)
                                                    {
                                                            // Kolom 5 - 14 Start
                                                            $target_rp_kolom_16 = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                if($cek_program_target_satuan_rp_realisasi)
                                                                {
                                                                    $tc_14 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                    $target_rp_kolom_16[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                                                } else {
                                                                    $tc_14 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. 0,00</td>';
                                                                    $target_rp_kolom_16[] = 0;
                                                                }
                                                            }
                                                            // Kolom 5 - 14 End
                                                            // Kolom 15 - 16 Start
                                                            $program_target_satuan_rp_realisasi_kolom_15 = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', end($tahuns))->first();
                                                            $tc_14 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_15->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $tc_14 .= '<td>Rp. '.number_format(array_sum($target_rp_kolom_16), 2, ',', '.').'</td>';
                                                            // Kolom 15 - 16 End
                                                            $tc_14 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_14 .= '</tr>';
                                                    } else {
                                                        $tc_14 .= '<tr>';
                                                            $tc_14 .= '<td></td>';
                                                            $tc_14 .= '<td></td>';
                                                            $tc_14 .= '<td></td>';
                                                            $tc_14 .= '<td></td>';
                                                            $tc_14 .= '<td></td>';
                                                            // Kolom 5 - 14 Start
                                                            $target_rp_kolom_16 = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                if($cek_program_target_satuan_rp_realisasi)
                                                                {
                                                                    $tc_14 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                    $target_rp_kolom_16[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                                                } else {
                                                                    $tc_14 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. 0,00</td>';
                                                                    $target_rp_kolom_16[] = 0;
                                                                }
                                                            }
                                                            // Kolom 5 - 14 End
                                                            // Kolom 15 - 16 Start
                                                            $program_target_satuan_rp_realisasi_kolom_15 = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', end($tahuns))->first();
                                                            $tc_14 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_15->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $tc_14 .= '<td>Rp. '.number_format(array_sum($target_rp_kolom_16), 2, ',', '.').'</td>';
                                                            // Kolom 15 - 16 End
                                                            $tc_14 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_14 .= '</tr>';
                                                    }
                                                    $d++;
                                                }
                                        } else {
                                            $tc_14 .= '<tr>';
                                                $tc_14 .= '<td>'.$misi['kode'].'</td>';
                                                $tc_14 .= '<td>'.$tujuan['kode'].'</td>';
                                                $tc_14 .= '<td>'.$sasaran['kode'].'</td>';
                                                $tc_14 .= '<td style="text-align:left">'.$program['deskripsi'].'</td>';
                                                $tc_14 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                                // Kolom 4 Start
                                                $tc_14 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                // Kolom 4 End
                                                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                $d = 1;
                                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                    if($d == 1)
                                                    {
                                                            // Kolom 5 - 14 Start
                                                            $target_rp_kolom_16 = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                if($cek_program_target_satuan_rp_realisasi)
                                                                {
                                                                    $tc_14 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                    $target_rp_kolom_16[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                                                } else {
                                                                    $tc_14 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. 0,00</td>';
                                                                    $target_rp_kolom_16[] = 0;
                                                                }
                                                            }
                                                            // Kolom 5 - 14 End
                                                            // Kolom 15 - 16 Start
                                                            $program_target_satuan_rp_realisasi_kolom_15 = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', end($tahuns))->first();
                                                            $tc_14 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_15->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $tc_14 .= '<td>Rp. '.number_format(array_sum($target_rp_kolom_16), 2, ',', '.').'</td>';
                                                            // Kolom 15 - 16 End
                                                            $tc_14 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_14 .= '</tr>';
                                                    } else {
                                                        $tc_14 .= '<tr>';
                                                            $tc_14 .= '<td></td>';
                                                            $tc_14 .= '<td></td>';
                                                            $tc_14 .= '<td></td>';
                                                            $tc_14 .= '<td></td>';
                                                            $tc_14 .= '<td></td>';
                                                            // Kolom 5 - 14 Start
                                                            $target_rp_kolom_16 = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                if($cek_program_target_satuan_rp_realisasi)
                                                                {
                                                                    $tc_14 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                    $target_rp_kolom_16[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                                                } else {
                                                                    $tc_14 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. 0,00</td>';
                                                                    $target_rp_kolom_16[] = 0;
                                                                }
                                                            }
                                                            // Kolom 5 - 14 End
                                                            // Kolom 15 - 16 Start
                                                            $program_target_satuan_rp_realisasi_kolom_15 = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', end($tahuns))->first();
                                                            $tc_14 .= '<td>'.$program_target_satuan_rp_realisasi_kolom_15->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                            $tc_14 .= '<td>Rp. '.number_format(array_sum($target_rp_kolom_16), 2, ',', '.').'</td>';
                                                            // Kolom 15 - 16 End
                                                            $tc_14 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_14 .= '</tr>';
                                                    }
                                                    $d++;
                                                }
                                        }
                                        $c++;
                                    }
                            }
                        }
                }
            }
        }
        // TC 14 End

        return view('admin.laporan.tc-14', compact('tc_14'));
    }
}
