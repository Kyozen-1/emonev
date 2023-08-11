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

class Tc27Ekspor implements FromView
{
    protected $opd_id;

    public function __construct($opd_id)
    {
        $this->opd_id = $opd_id;
    }

    public function view(): View
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

        $get_tujuans = Tujuan::where('tahun_periode_id', $get_periode->id)->whereHas('sasaran', function($q) use ($opd){
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

        $tc_27 = '';
        foreach ($tujuans as $tujuan) {
            $get_tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])->get();
            foreach ($get_tujuan_pds as $get_tujuan_pd) {
                $tc_27 .= '<tr>';
                    $tc_27 .= '<td>'.$get_tujuan_pd->deskripsi.'</td>';
                    $tc_27 .= '<td></td>';
                    $tc_27 .= '<td>'.$get_tujuan_pd->kode.'</td>';
                    $tc_27 .= '<td></td>';
                    $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $get_tujuan_pd->id)->get();
                    $a = 1;
                    foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                        if($a == 1)
                        {
                                $tc_27 .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                $tc_27 .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $indikator_a = 0;
                                $len_a = count($tahuns);
                                foreach ($tahuns as $tahun) {
                                    $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                    if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                    {
                                        $tc_27 .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'/'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                        $tc_27 .= '<td></td>';
                                        if($indikator_a == $len_a - 1)
                                        {
                                            $tc_27 .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'/'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                            $tc_27 .= '<td></td>';
                                        }
                                    } else {
                                        $tc_27 .= '<td></td>';
                                        $tc_27 .= '<td></td>';
                                        if($indikator_a == $len_a - 1)
                                        {
                                            $tc_27 .= '<td></td>';
                                            $tc_27 .= '<td></td>';
                                        }
                                    }
                                    $indikator_a++;
                                }
                                $tc_27 .= '<td style="text-align:left">'.$opd->nama.'</td>';
                                $tc_27 .= '<td style="text-align:left">Kabupaten Madiun</td>';
                            $tc_27 .= '</tr>';
                        } else {
                            $tc_27 .= '<tr>';
                                $tc_27 .= '<td></td>';
                                $tc_27 .= '<td></td>';
                                $tc_27 .= '<td></td>';
                                $tc_27 .= '<td></td>';
                                $tc_27 .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                $tc_27 .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $indikator_a = 0;
                                $len_a = count($tahuns);
                                foreach ($tahuns as $tahun) {
                                    $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                    if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                    {
                                        $tc_27 .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'/'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                        $tc_27 .= '<td></td>';
                                        if($indikator_a == $len_a - 1)
                                        {
                                            $tc_27 .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'/'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                            $tc_27 .= '<td></td>';
                                        }
                                    } else {
                                        $tc_27 .= '<td></td>';
                                        $tc_27 .= '<td></td>';
                                        if($indikator_a == $len_a - 1)
                                        {
                                            $tc_27 .= '<td></td>';
                                            $tc_27 .= '<td></td>';
                                        }
                                    }
                                    $indikator_a++;
                                }
                                $tc_27 .= '<td style="text-align:left">'.$opd->nama.'</td>';
                                $tc_27 .= '<td style="text-align:left">Kabupaten Madiun</td>';
                            $tc_27 .= '</tr>';
                        }
                        $a++;
                    }
            }

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

                foreach ($get_sasaran_pds as $get_sasaran_pd) {
                    $tc_27 .= '<tr>';
                        $tc_27 .= '<td></td>';
                        $tc_27 .= '<td>'.$get_sasaran_pd->deskripsi.'</td>';
                        $tc_27 .= '<td>'.$get_sasaran_pd->kode.'</td>';
                        $tc_27 .= '<td></td>';
                        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $get_sasaran_pd->id)->get();
                        $b = 1;
                        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                            if($b == 1)
                            {
                                    $tc_27 .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $tc_27 .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $indikator_b = 0;
                                    $len_b = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $tc_27 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $tc_27 .= '<td></td>';
                                            if($indikator_b == $len_b - 1)
                                            {
                                                $tc_27 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $tc_27 .= '<td></td>';
                                            }
                                        } else {
                                            $tc_27 .= '<td></td>';
                                            $tc_27 .= '<td></td>';
                                            if($indikator_b == $len_b - 1)
                                            {
                                                $tc_27 .= '<td></td>';
                                                $tc_27 .= '<td></td>';
                                            }
                                        }
                                        $indikator_b++;
                                    }
                                    $tc_27 .= '<td style="text-align:left">'.$opd->nama.'</td>';
                                    $tc_27 .= '<td style="text-align:left">Kabupaten Madiun</td>';
                                $tc_27 .= '</tr>';
                            } else {
                                $tc_27 .= '<tr>';
                                    $tc_27 .= '<td></td>';
                                    $tc_27 .= '<td></td>';
                                    $tc_27 .= '<td></td>';
                                    $tc_27 .= '<td></td>';
                                    $tc_27 .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $tc_27 .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $indikator_b = 0;
                                    $len_b = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            $tc_27 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $tc_27 .= '<td></td>';
                                            if($indikator_b == $len_b - 1)
                                            {
                                                $tc_27 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                $tc_27 .= '<td></td>';
                                            }
                                        } else {
                                            $tc_27 .= '<td></td>';
                                            $tc_27 .= '<td></td>';
                                            if($indikator_b == $len_b - 1)
                                            {
                                                $tc_27 .= '<td></td>';
                                                $tc_27 .= '<td></td>';
                                            }
                                        }
                                        $indikator_b++;
                                    }
                                    $tc_27 .= '<td style="text-align:left">'.$opd->nama.'</td>';
                                    $tc_27 .= '<td style="text-align:left">Kabupaten Madiun</td>';
                                $tc_27 .= '</tr>';
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
                        $urusan = Urusan::whereHas('program', function($q) use ($program){
                            $q->where('id', $program['id']);
                        })->first();
                        $tc_27 .= '<tr>';
                            $tc_27 .= '<td></td>';
                            $tc_27 .= '<td></td>';
                            $tc_27 .= '<td>'.$urusan->kode.'.'.$program['kode'].'</td>';
                            $tc_27 .= '<td style="text-align:left">'.$program['deskripsi'].'</td>';
                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])
                                                            ->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                                                $q->where('opd_id', $opd->id);
                                                            })->get();
                            $c = 1;
                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                if($c == 1)
                                {
                                        $tc_27 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                        $tc_27 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                        $indikator_c = 0;
                                        $len_c = count($tahuns);
                                        $program_target_rp = [];
                                        foreach ($tahuns as $tahun) {
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                $q->where('opd_id', $opd->id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_target_rp[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                                $tc_27 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$cek_program_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_27 .= '<td>Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_27 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$cek_program_target_satuan_rp_realisasi->satuan.'</td>';
                                                    $tc_27 .= '<td>Rp.'.number_format(array_sum($program_target_rp), 2, ',', '.').'</td>';
                                                }
                                            } else {
                                                $tc_27 .= '<td></td>';
                                                $tc_27 .= '<td></td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_27 .= '<td></td>';
                                                    $tc_27 .= '<td></td>';
                                                }
                                            }
                                            $indikator_c++;
                                        }
                                        $tc_27 .= '<td style="text-align:left">'.$opd->nama.'</td>';
                                        $tc_27 .= '<td style="text-align:left">Kabupaten Madiun</td>';
                                    $tc_27 .='</tr>';
                                } else {
                                    $tc_27 .= '<tr>';
                                        $tc_27 .= '<td></td>';
                                        $tc_27 .= '<td></td>';
                                        $tc_27 .= '<td></td>';
                                        $tc_27 .= '<td></td>';
                                        $tc_27 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                        $indikator_c = 0;
                                        $len_c = count($tahuns);
                                        $program_target_rp = [];
                                        foreach ($tahuns as $tahun) {
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd){
                                                $q->where('opd_id', $opd->id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();

                                            if($cek_program_target_satuan_rp_realisasi)
                                            {
                                                $program_target_rp[] = $cek_program_target_satuan_rp_realisasi->target_rp;
                                                $tc_27 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$cek_program_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_27 .= '<td>Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_27 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$cek_program_target_satuan_rp_realisasi->satuan.'</td>';
                                                    $tc_27 .= '<td>Rp.'.number_format(array_sum($program_target_rp), 2, ',', '.').'</td>';
                                                }
                                            } else {
                                                $tc_27 .= '<td></td>';
                                                $tc_27 .= '<td></td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_27 .= '<td></td>';
                                                    $tc_27 .= '<td></td>';
                                                }
                                            }
                                            $indikator_c++;
                                        }
                                        $tc_27 .= '<td style="text-align:left">'.$opd->nama.'</td>';
                                        $tc_27 .= '<td style="text-align:left">Kabupaten Madiun</td>';
                                    $tc_27 .='</tr>';
                                }
                                $c++;
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
                            $kegiatan_program = Program::whereHas('kegiatan', function($q) use ($kegiatan){
                                $q->where('id', $kegiatan['id']);
                            })->first();

                            $kegiatan_urusan = Urusan::whereHas('program', function($q) use ($kegiatan_program){
                                $q->where('id', $kegiatan_program->id);
                            })->first();
                            $tc_27 .= '<tr>';
                                $tc_27 .= '<td></td>';
                                $tc_27 .= '<td></td>';
                                $tc_27 .= '<td>'.$kegiatan_urusan->kode.'.'.$kegiatan_program->kode.'.'.$kegiatan['kode'].'</td>';
                                $tc_27 .= '<td style="text-align:text-left;">'.$kegiatan['deskripsi'].'</td>';
                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                                    ->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd){
                                                                        $q->where('opd_id', $opd->id);
                                                                    })->get();
                                $d = 1;
                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                    if($d == 1)
                                    {
                                            $tc_27 .= '<td style="text-align:left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                            $tc_27 .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            $indikator_d = 0;
                                            $len_d = count($tahuns);
                                            $kegiatan_target_rp = [];
                                            foreach ($tahuns as $tahun) {
                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();
                                                if($kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                    $tc_27 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_27 .= '<td>Rp.'.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                    if($indikator_d == $len_d - 1)
                                                    {
                                                        $tc_27 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_27 .= '<td>Rp.'.number_format(array_sum($kegiatan_target_rp), 2, ',', '.').'</td>';
                                                    }
                                                } else {
                                                    $tc_27 .= '<td></td>';
                                                    $tc_27 .= '<td></td>';
                                                    if($indikator_d == $len_d - 1)
                                                    {
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '<td></td>';
                                                    }
                                                }
                                                $indikator_d++;
                                            }
                                            $tc_27 .= '<td style="text-align:left">'.$opd->nama.'</td>';
                                            $tc_27 .= '<td style="text-align:left">Kabupaten Madiun</td>';
                                        $tc_27 .= '</tr>';
                                    } else {
                                        $tc_27 .= '<tr>';
                                            $tc_27 .= '<td></td>';
                                            $tc_27 .= '<td></td>';
                                            $tc_27 .= '<td></td>';
                                            $tc_27 .= '<td></td>';
                                            $tc_27 .= '<td style="text-align:left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                            $tc_27 .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            $indikator_d = 0;
                                            $len_d = count($tahuns);
                                            $kegiatan_target_rp = [];
                                            foreach ($tahuns as $tahun) {
                                                $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();
                                                if($kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $kegiatan_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                    $tc_27 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $tc_27 .= '<td>Rp.'.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                    if($indikator_d == $len_d - 1)
                                                    {
                                                        $tc_27 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $tc_27 .= '<td>Rp.'.number_format(array_sum($kegiatan_target_rp), 2, ',', '.').'</td>';
                                                    }
                                                } else {
                                                    $tc_27 .= '<td></td>';
                                                    $tc_27 .= '<td></td>';
                                                    if($indikator_d == $len_d - 1)
                                                    {
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '<td></td>';
                                                    }
                                                }
                                                $indikator_d++;
                                            }
                                            $tc_27 .= '<td style="text-align:left">'.$opd->nama.'</td>';
                                            $tc_27 .= '<td style="text-align:left">Kabupaten Madiun</td>';
                                        $tc_27 .= '</tr>';
                                    }
                                    $d++;
                                }
                        }
                    }
                }
            }
        }

        return view('admin.laporan.tc-27', [
            'tc_27' => $tc_27
        ]);
    }
}
