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

class E80Ekspor implements FromView
{
    protected $opd_id;

    public function __construct($opd_id)
    {
        $this->opd_id = $opd_id;
    }

    public function view(): view
    {
        $opd = MasterOpd::find($this->opd_id);

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
        })->where('tahun_periode_id', $get_periode->id)->get();

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
                        $e_80 .= '<td>s.'.$a.'</td>';
                        $e_80 .= '<td style="text-align:left">'.$get_sasaran_pd->deskripsi.'</td>';
                        $e_80 .= '<td></td>';
                        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $get_sasaran_pd->id)->get();
                        $b = 1;
                        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                            if($b == 1)
                            {
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    // Kolom 6 Start
                                    $last_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                    ->where('tahun', end($tahuns))->first();
                                    if($last_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $e_80 .= '<td>'.$last_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    }
                                    $e_80 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 6 End
                                    // Kolom 7 - 11 Start
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $e_80 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                        } else {
                                            $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }
                                    // Kolom 7 - 11 End

                                    // Kolom 12 - 16 Start
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                $e_80 .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td>Rp. 0, 00</td>';
                                            } else {
                                                $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td>Rp. 0, 00</td>';
                                            }
                                        } else {
                                            $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }
                                    // Kolom 12 - 16 End

                                    // Kolom 17 - 21 Start
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                if($cek_sasaran_pd_target_satuan_rp_realisasi->target)
                                                {
                                                    $rasio_target = ($cek_sasaran_pd_realisasi_renja->realisasi/$cek_sasaran_pd_target_satuan_rp_realisasi->target) * 100;
                                                } else {
                                                    $rasio_target = 0;
                                                }
                                                $e_80 .= '<td>'.number_format($rasio_target,2,',', '.').'</td>';
                                                $e_80 .= '<td>Rp. 0, 00</td>';
                                            } else {
                                                $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td>Rp. 0, 00</td>';
                                            }
                                        } else {
                                            $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }
                                    // Kolom 17 - 21 End
                                    $e_80 .= '<td>'.$opd->nama.'</td>';
                                $e_80 .= '</tr>';
                            } else {
                                $e_80 .= '<tr>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td></td>';
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $e_80 .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    // Kolom 6 Start
                                    $last_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                    ->where('tahun', end($tahuns))->first();
                                    if($last_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $e_80 .= '<td>'.$last_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    }
                                    $e_80 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 6 End
                                    // Kolom 7 - 11 Start
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $e_80 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                        } else {
                                            $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }
                                    // Kolom 7 - 11 End

                                    // Kolom 12 - 16 Start
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                $e_80 .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td>Rp. 0, 00</td>';
                                            } else {
                                                $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td>Rp. 0, 00</td>';
                                            }
                                        } else {
                                            $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }
                                    // Kolom 12 - 16 End

                                    // Kolom 17 - 21 Start
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                            if($cek_sasaran_pd_realisasi_renja)
                                            {
                                                if($cek_sasaran_pd_target_satuan_rp_realisasi->target)
                                                {
                                                    $rasio_target = ($cek_sasaran_pd_realisasi_renja->realisasi/$cek_sasaran_pd_target_satuan_rp_realisasi->target) * 100;
                                                } else {
                                                    $rasio_target = 0;
                                                }
                                                $e_80 .= '<td>'.number_format($rasio_target,2,',', '.').'</td>';
                                                $e_80 .= '<td>Rp. 0, 00</td>';
                                            } else {
                                                $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $e_80 .= '<td>Rp. 0, 00</td>';
                                            }
                                        } else {
                                            $e_80 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                        }
                                    }
                                    // Kolom 17 - 21 End
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
                                $e_80 .= '<td>p.'.$a.'</td>';
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
                                            // Kolom 6 Start
                                            $target_rp_kolom_6 = [];
                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();
                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $target_rp_kolom_6[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                                } else {
                                                    $target_rp_kolom_6[] = 0;
                                                }
                                            }

                                            $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                $q->where('opd_id', $opd->id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', end($tahuns))->first();
                                            $e_80 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td>Rp. '.number_format(array_sum($target_rp_kolom_6), 2, ',', '.').'</td>';
                                            // Kolom 6 End
                                            // Kolom 7 - 11 Start
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
                                                    $e_80 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $e_80 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_80 .= '<td>Rp. 0, 00</td>';
                                                }
                                            }
                                            // Kolom 7 - 11 End

                                            // Kolom 12 - 16 Start
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
                                                        $program_tw_target_realisasi_rp = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_tw_target_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_80 .= '<td>'.array_sum($program_tw_target_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. '.number_format(array_sum($program_tw_target_realisasi_rp), 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_80 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. 0, 00</td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_80 .= '<td>Rp. 0, 00</td>';
                                                }
                                            }
                                            // Kolom 12 - 16 End

                                            // Kolom 17 - 21 Start
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
                                                        $program_tw_target_realisasi_rp = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_tw_target_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        if($cek_program_target_satuan_rp_realisasi->target)
                                                        {
                                                            $program_target_rasio = ((array_sum($program_tw_target_realisasi)) / $cek_program_target_satuan_rp_realisasi->target) * 100;
                                                            $program_target_rp_rasio = ((array_sum($program_tw_target_realisasi_rp)) / $cek_program_target_satuan_rp_realisasi->target_rp) * 100;
                                                        } else {
                                                            $program_target_rasio = 0;
                                                            $program_target_rp_rasio = 0;
                                                        }
                                                        $e_80 .= '<td>'.number_format($program_target_rasio, 2, ',', '.').'</td>';
                                                        $e_80 .= '<td>Rp. '.number_format($program_target_rp_rasio, 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_80 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. 0, 00</td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_80 .= '<td>Rp. 0, 00</td>';
                                                }
                                            }
                                            // Kolom 17 - 21 End
                                            $e_80 .= '<td>'.$opd->nama.'</td>';
                                        $e_80 .= '</tr>';
                                    } else {
                                        $e_80 .= '<tr>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td></td>';
                                            $e_80 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                            $e_80 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            // Kolom 6 Start
                                            $target_rp_kolom_6 = [];
                                            foreach ($tahuns as $tahun) {
                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                        $q->where('id', $program_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();
                                                if($cek_program_target_satuan_rp_realisasi)
                                                {
                                                    $target_rp_kolom_6[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                                } else {
                                                    $target_rp_kolom_6[] = 0;
                                                }
                                            }

                                            $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                $q->where('opd_id', $opd->id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', end($tahuns))->first();
                                            $e_80 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_80 .= '<td>Rp. '.number_format(array_sum($target_rp_kolom_6), 2, ',', '.').'</td>';
                                            // Kolom 6 End
                                            // Kolom 7 - 11 Start
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
                                                    $e_80 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $e_80 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_80 .= '<td>Rp. 0, 00</td>';
                                                }
                                            }
                                            // Kolom 7 - 11 End

                                            // Kolom 12 - 16 Start
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
                                                        $program_tw_target_realisasi_rp = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_tw_target_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_80 .= '<td>'.array_sum($program_tw_target_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. '.number_format(array_sum($program_tw_target_realisasi_rp), 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_80 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. 0, 00</td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_80 .= '<td>Rp. 0, 00</td>';
                                                }
                                            }
                                            // Kolom 12 - 16 End

                                            // Kolom 17 - 21 Start
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
                                                        $program_tw_target_realisasi_rp = [];
                                                        $program_tw_realisasis = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($program_tw_realisasis as $program_tw_realisasi) {
                                                            $program_tw_target_realisasi[] = $program_tw_realisasi->realisasi;
                                                            $program_tw_target_realisasi_rp[] = $program_tw_realisasi->realisasi_rp;
                                                        }
                                                        if($cek_program_target_satuan_rp_realisasi->target)
                                                        {
                                                            $program_target_rasio = ((array_sum($program_tw_target_realisasi)) / $cek_program_target_satuan_rp_realisasi->target) * 100;
                                                            $program_target_rp_rasio = ((array_sum($program_tw_target_realisasi_rp)) / $cek_program_target_satuan_rp_realisasi->target_rp) * 100;
                                                        } else {
                                                            $program_target_rasio = 0;
                                                            $program_target_rp_rasio = 0;
                                                        }
                                                        $e_80 .= '<td>'.number_format($program_target_rasio, 2, ',', '.').'</td>';
                                                        $e_80 .= '<td>Rp. '.number_format($program_target_rp_rasio, 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_80 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. 0, 00</td>';
                                                    }
                                                } else {
                                                    $e_80 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_80 .= '<td>Rp. 0, 00</td>';
                                                }
                                            }
                                            // Kolom 17 - 21 End
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
                                    $e_80 .= '<td>k.'.$a.'</td>';
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
                                                // Kolom 6 Start
                                                $target_rp_kolom_6 = [];
                                                foreach ($tahuns as $tahun) {
                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();
                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $target_rp_kolom_6[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                    } else {
                                                        $target_rp_kolom_6[] = 0;
                                                    }
                                                }

                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', end($tahuns))->first();
                                                if($kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $e_80 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                } else {
                                                    $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                }
                                                $e_80 .= '<td>Rp. '.number_format(array_sum($target_rp_kolom_6), 2, ',', '.').'</td>';
                                                // Kolom 6 End
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
                                                        $e_80 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. 0, 00</td>';
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
                                                        $kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_target_realisasi = [];
                                                            $kegiatan_tw_target_realisasi_rp = [];
                                                            $kegiatan_tw_realisasis = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                $kegiatan_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                $kegiatan_tw_target_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                            }
                                                            $e_80 .= '<td>'.array_sum($kegiatan_tw_target_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_80 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_target_realisasi_rp), 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. 0, 00</td>';
                                                    }
                                                }
                                                // Kolom 12 - 16 End

                                                // Kolom 17 - 21 Start
                                                foreach ($tahuns as $tahun) {
                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();

                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_target_realisasi = [];
                                                            $kegiatan_tw_target_realisasi_rp = [];
                                                            $kegiatan_tw_realisasis = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                $kegiatan_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                $kegiatan_tw_target_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                            }
                                                            if($kegiatan_target_satuan_rp_realisasi->target)
                                                            {
                                                                $kegiatan_target_rasio = ((array_sum($kegiatan_tw_target_realisasi)) / $kegiatan_target_satuan_rp_realisasi->target) * 100;
                                                                $kegiatan_target_rp_rasio = ((array_sum($kegiatan_tw_target_realisasi_rp)) / $kegiatan_target_satuan_rp_realisasi->target_rp) * 100;
                                                            } else {
                                                                $kegiatan_target_rasio = 0;
                                                                $kegiatan_target_rp_rasio = 0;
                                                            }
                                                            $e_80 .= '<td>'.number_format($kegiatan_target_rasio, 2, ',', '.').'</td>';
                                                            $e_80 .= '<td>Rp. '.number_format($kegiatan_target_rp_rasio, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. 0, 00</td>';
                                                    }
                                                }
                                                // Kolom 17 - 21 End
                                                $e_80 .= '<td>'.$opd->nama.'</td>';
                                            $e_80 .= '</tr>';
                                        } else {
                                            $e_80 .= '<tr>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td></td>';
                                                $e_80 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                $e_80 .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                // Kolom 6 Start
                                                $target_rp_kolom_6 = [];
                                                foreach ($tahuns as $tahun) {
                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();
                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $target_rp_kolom_6[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                    } else {
                                                        $target_rp_kolom_6[] = 0;
                                                    }
                                                }

                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', end($tahuns))->first();
                                                if($kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $e_80 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                } else {
                                                    $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                }
                                                $e_80 .= '<td>Rp. '.number_format(array_sum($target_rp_kolom_6), 2, ',', '.').'</td>';
                                                // Kolom 6 End
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
                                                        $e_80 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. 0, 00</td>';
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
                                                        $kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_target_realisasi = [];
                                                            $kegiatan_tw_target_realisasi_rp = [];
                                                            $kegiatan_tw_realisasis = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                $kegiatan_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                $kegiatan_tw_target_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                            }
                                                            $e_80 .= '<td>'.array_sum($kegiatan_tw_target_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_80 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_target_realisasi_rp), 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. 0, 00</td>';
                                                    }
                                                }
                                                // Kolom 12 - 16 End

                                                // Kolom 17 - 21 Start
                                                foreach ($tahuns as $tahun) {
                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                            $q->where('id', $kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $tahun)->first();

                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                    {
                                                        $kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->first();
                                                        if($kegiatan_tw_realisasi)
                                                        {
                                                            $kegiatan_tw_target_realisasi = [];
                                                            $kegiatan_tw_target_realisasi_rp = [];
                                                            $kegiatan_tw_realisasis = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->get();
                                                            foreach ($kegiatan_tw_realisasis as $kegiatan_tw_realisasi) {
                                                                $kegiatan_tw_target_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                $kegiatan_tw_target_realisasi_rp[] = $kegiatan_tw_realisasi->realisasi_rp;
                                                            }
                                                            if($kegiatan_target_satuan_rp_realisasi->target)
                                                            {
                                                                $kegiatan_target_rasio = ((array_sum($kegiatan_tw_target_realisasi)) / $kegiatan_target_satuan_rp_realisasi->target) * 100;
                                                                $kegiatan_target_rp_rasio = ((array_sum($kegiatan_tw_target_realisasi_rp)) / $kegiatan_target_satuan_rp_realisasi->target_rp) * 100;
                                                            } else {
                                                                $kegiatan_target_rasio = 0;
                                                                $kegiatan_target_rp_rasio = 0;
                                                            }
                                                            $e_80 .= '<td>'.number_format($kegiatan_target_rasio, 2, ',', '.').'</td>';
                                                            $e_80 .= '<td>Rp. '.number_format($kegiatan_target_rp_rasio, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_80 .= '<td>Rp. 0, 00</td>';
                                                        }
                                                    } else {
                                                        $e_80 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_80 .= '<td>Rp. 0, 00</td>';
                                                    }
                                                }
                                                // Kolom 17 - 21 End
                                                $e_80 .= '<td>'.$opd->nama.'</td>';
                                            $e_80 .= '</tr>';
                                        }
                                        $c++;
                                    }
                            }
                        }
                    $a++;
                }
            }
        }
        return view('admin.laporan.e-80', ['e_80' => $e_80]);
    }
}
