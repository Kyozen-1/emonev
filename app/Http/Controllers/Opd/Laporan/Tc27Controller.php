<?php

namespace App\Http\Controllers\Opd\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use Excel;
use PDF;
use App\Exports\OpdTc27Ekspor;

class Tc27Controller extends Controller
{
    public function tc_27_ekspor_excel()
    {
        return Excel::download(new OpdTc27Ekspor, 'Laporan TC-27.xlsx');
    }

    public function tc_27_ekspor_pdf()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $tahun_akhir = $get_periode->tahun_akhir;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_tujuans = Tujuan::whereHas('sasaran', function($q){
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
            $tc_27 .= '<tr>';
                $tc_27 .= '<td style="text-align: left;">'.$tujuan['deskripsi'].'</td>';

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

                $a = 1;
                foreach ($sasarans as $sasaran) {
                    if($a == 1)
                    {
                            $tc_27 .= '<td style="text-align: left;">'.$sasaran['deskripsi'].'</td>';
                            // Program
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

                            $b = 1;
                            foreach ($programs as $program) {
                                if($b == 1)
                                {
                                        $tc_27 .= '<td style="text-align: left;">'.$program['kode'].'</td>';
                                        $tc_27 .= '<td style="text-align: left;">'.$program['deskripsi'].'</td>';
                                        // Indikator Kinerja Program
                                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                        })->get();
                                        $c = 1;
                                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                            if($c == 1)
                                            {
                                                    $tc_27 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                    // Opd Program Indikator
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                    $d = 1;
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                            // Nilai Target
                                                            $data_target = [];
                                                            $data_satuan = '';
                                                            $data_target_rp = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                        ->where('tahun', $tahun)->first();
                                                                if($program_target_satuan_rp_realisasi)
                                                                {
                                                                    $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                    $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                    $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                    $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                    $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                } else {
                                                                    $tc_27 .= '<td></td>';
                                                                    $tc_27 .= '<td></td>';
                                                                }
                                                            }
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                            $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_akhir)->first();
                                                            if($kondisi_akhir_tahun)
                                                            {
                                                                $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                            $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '</tr>';

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
                                                                $tc_27 .= '<tr>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                    })->get();
                                                                    $e = 1;
                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                        if($e == 1)
                                                                        {
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // Opd Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        } else {
                                                                            $tc_27 .= '<tr>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // OPD Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        }
                                                                        $e++;
                                                                    }
                                                            }
                                                        } else {
                                                            $tc_27 .= '<tr>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            // Belum Nilai Target
                                                            $data_target = [];
                                                            $data_satuan = '';
                                                            $data_target_rp = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                        ->where('tahun', $tahun)->first();
                                                                if($program_target_satuan_rp_realisasi)
                                                                {
                                                                    $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                    $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                    $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                    $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                    $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                } else {
                                                                    $tc_27 .= '<td></td>';
                                                                    $tc_27 .= '<td></td>';
                                                                }
                                                            }
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                            $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_akhir)->first();
                                                            if($kondisi_akhir_tahun)
                                                            {
                                                                $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                            $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '</tr>';

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
                                                                $tc_27 .= '<tr>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                    })->get();
                                                                    $e = 1;
                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                        if($e == 1)
                                                                        {
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // Opd Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        } else {
                                                                            $tc_27 .= '<tr>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // OPD Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
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
                                                $tc_27 .= '<tr>';
                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                    $tc_27 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                    // Belum Opd Program Indikator
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                    $d = 1;
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                            // Nilai Target
                                                            $data_target = [];
                                                            $data_satuan = '';
                                                            $data_target_rp = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                        ->where('tahun', $tahun)->first();
                                                                if($program_target_satuan_rp_realisasi)
                                                                {
                                                                    $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                    $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                    $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                    $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                    $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                } else {
                                                                    $tc_27 .= '<td></td>';
                                                                    $tc_27 .= '<td></td>';
                                                                }
                                                            }
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                            $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_akhir)->first();
                                                            if($kondisi_akhir_tahun)
                                                            {
                                                                $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                            $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '</tr>';

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
                                                                $tc_27 .= '<tr>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                    })->get();
                                                                    $e = 1;
                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                        if($e == 1)
                                                                        {
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // Opd Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        } else {
                                                                            $tc_27 .= '<tr>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // OPD Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        }
                                                                        $e++;
                                                                    }
                                                            }
                                                        } else {
                                                            $tc_27 .= '<tr>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            // Belum Nilai Target
                                                            $data_target = [];
                                                            $data_satuan = '';
                                                            $data_target_rp = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                        ->where('tahun', $tahun)->first();
                                                                if($program_target_satuan_rp_realisasi)
                                                                {
                                                                    $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                    $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                    $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                    $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                    $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                } else {
                                                                    $tc_27 .= '<td></td>';
                                                                    $tc_27 .= '<td></td>';
                                                                }
                                                            }
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                            $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_akhir)->first();
                                                            if($kondisi_akhir_tahun)
                                                            {
                                                                $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                            $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '</tr>';

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
                                                                $tc_27 .= '<tr>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                    })->get();
                                                                    $e = 1;
                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                        if($e == 1)
                                                                        {
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // Opd Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        } else {
                                                                            $tc_27 .= '<tr>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // OPD Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
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
                                    $tc_27 .= '<tr>';
                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                        $tc_27 .= '<td style="text-align: left;">'.$program['kode'].'</td>';
                                        $tc_27 .= '<td style="text-align: left;">'.$program['deskripsi'].'</td>';
                                        // Belum Indikator Kinerja Program
                                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                        })->get();
                                        $c = 1;
                                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                            if($c == 1)
                                            {
                                                    $tc_27 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                    // Opd Program Indikator
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                    $d = 1;
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                            // Nilai Target
                                                            $data_target = [];
                                                            $data_satuan = '';
                                                            $data_target_rp = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                        ->where('tahun', $tahun)->first();
                                                                if($program_target_satuan_rp_realisasi)
                                                                {
                                                                    $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                    $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                    $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                    $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                    $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                } else {
                                                                    $tc_27 .= '<td></td>';
                                                                    $tc_27 .= '<td></td>';
                                                                }
                                                            }
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                            $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_akhir)->first();
                                                            if($kondisi_akhir_tahun)
                                                            {
                                                                $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                            $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '</tr>';

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
                                                                $tc_27 .= '<tr>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                    })->get();
                                                                    $e = 1;
                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                        if($e == 1)
                                                                        {
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // Opd Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        } else {
                                                                            $tc_27 .= '<tr>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // OPD Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        }
                                                                        $e++;
                                                                    }
                                                            }
                                                        } else {
                                                            $tc_27 .= '<tr>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            // Belum Nilai Target
                                                            $data_target = [];
                                                            $data_satuan = '';
                                                            $data_target_rp = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                        ->where('tahun', $tahun)->first();
                                                                if($program_target_satuan_rp_realisasi)
                                                                {
                                                                    $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                    $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                    $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                    $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                    $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                } else {
                                                                    $tc_27 .= '<td></td>';
                                                                    $tc_27 .= '<td></td>';
                                                                }
                                                            }
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                            $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_akhir)->first();
                                                            if($kondisi_akhir_tahun)
                                                            {
                                                                $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                            $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '</tr>';

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
                                                                $tc_27 .= '<tr>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                    })->get();
                                                                    $e = 1;
                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                        if($e == 1)
                                                                        {
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // Opd Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        } else {
                                                                            $tc_27 .= '<tr>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // OPD Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
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
                                                $tc_27 .= '<tr>';
                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                    $tc_27 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                    // Belum Opd Program Indikator
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                    ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                    $d = 1;
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                            // Nilai Target
                                                            $data_target = [];
                                                            $data_satuan = '';
                                                            $data_target_rp = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                        ->where('tahun', $tahun)->first();
                                                                if($program_target_satuan_rp_realisasi)
                                                                {
                                                                    $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                    $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                    $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                    $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                    $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                } else {
                                                                    $tc_27 .= '<td></td>';
                                                                    $tc_27 .= '<td></td>';
                                                                }
                                                            }
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                            $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_akhir)->first();
                                                            if($kondisi_akhir_tahun)
                                                            {
                                                                $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                            $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '</tr>';

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
                                                                $tc_27 .= '<tr>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                    })->get();
                                                                    $e = 1;
                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                        if($e == 1)
                                                                        {
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // Opd Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        } else {
                                                                            $tc_27 .= '<tr>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // OPD Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        }
                                                                        $e++;
                                                                    }
                                                            }
                                                        } else {
                                                            $tc_27 .= '<tr>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                            // Belum Nilai Target
                                                            $data_target = [];
                                                            $data_satuan = '';
                                                            $data_target_rp = [];
                                                            foreach ($tahuns as $tahun) {
                                                                $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                        ->where('tahun', $tahun)->first();
                                                                if($program_target_satuan_rp_realisasi)
                                                                {
                                                                    $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                    $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                    $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                    $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                    $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                } else {
                                                                    $tc_27 .= '<td></td>';
                                                                    $tc_27 .= '<td></td>';
                                                                }
                                                            }
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                            $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $tahun_akhir)->first();
                                                            if($kondisi_akhir_tahun)
                                                            {
                                                                $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                            $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '</tr>';

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
                                                                $tc_27 .= '<tr>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                    $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                    })->get();
                                                                    $e = 1;
                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                        if($e == 1)
                                                                        {
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // Opd Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                }
                                                                                $f++;
                                                                            }
                                                                        } else {
                                                                            $tc_27 .= '<tr>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;"></td>';
                                                                            $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            // OPD Kegiatan Indikator
                                                                            $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                            ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                            $f = 1;
                                                                            foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                                if(f == 1)
                                                                                {
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
                                                                                } else {
                                                                                    $tc_27 .= '<tr>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                        // Nilai Target
                                                                                        $kegiatan_data_target = [];
                                                                                        $kegiatan_data_satuan = '';
                                                                                        $kegiatan_data_target_rp = [];
                                                                                        foreach ($tahuns as $tahun) {
                                                                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                            if($kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                                $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                                $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                                $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                            } else {
                                                                                                $tc_27 .= '<td></td>';
                                                                                                $tc_27 .= '<td></td>';
                                                                                            }
                                                                                        }
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                                                        if($kondisi_akhir_tahun)
                                                                                        {
                                                                                            $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    $tc_27 .='</tr>';
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
                    } else {
                        $tc_27 .= '<tr>';
                            $tc_27 .= '<td></td>';
                            $tc_27 .= '<td style="text-align: left;">'.$sasaran['deskripsi'].'</td>';
                            // Belum Program
                        // Program
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

                        $b = 1;
                        foreach ($programs as $program) {
                            if($b == 1)
                            {
                                    $tc_27 .= '<td style="text-align: left;">'.$program['kode'].'</td>';
                                    $tc_27 .= '<td style="text-align: left;">'.$program['deskripsi'].'</td>';
                                    // Indikator Kinerja Program
                                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q){
                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                    })->get();
                                    $c = 1;
                                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                        if($c == 1)
                                        {
                                                $tc_27 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                // Opd Program Indikator
                                                $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                $d = 1;
                                                foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                    if($d == 1)
                                                    {
                                                        // Nilai Target
                                                        $data_target = [];
                                                        $data_satuan = '';
                                                        $data_target_rp = [];
                                                        foreach ($tahuns as $tahun) {
                                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                            if($program_target_satuan_rp_realisasi)
                                                            {
                                                                $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                        }
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '<td></td>';
                                                        $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                        if($kondisi_akhir_tahun)
                                                        {
                                                            $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                        } else {
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                        }
                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '</tr>';

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
                                                            $tc_27 .= '<tr>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                })->get();
                                                                $e = 1;
                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                    if($e == 1)
                                                                    {
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // Opd Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    } else {
                                                                        $tc_27 .= '<tr>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // OPD Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    }
                                                                    $e++;
                                                                }
                                                        }
                                                    } else {
                                                        $tc_27 .= '<tr>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        // Belum Nilai Target
                                                        $data_target = [];
                                                        $data_satuan = '';
                                                        $data_target_rp = [];
                                                        foreach ($tahuns as $tahun) {
                                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                            if($program_target_satuan_rp_realisasi)
                                                            {
                                                                $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                        }
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '<td></td>';
                                                        $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                        if($kondisi_akhir_tahun)
                                                        {
                                                            $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                        } else {
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                        }
                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '</tr>';

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
                                                            $tc_27 .= '<tr>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                })->get();
                                                                $e = 1;
                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                    if($e == 1)
                                                                    {
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // Opd Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    } else {
                                                                        $tc_27 .= '<tr>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // OPD Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
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
                                            $tc_27 .= '<tr>';
                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                $tc_27 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                // Belum Opd Program Indikator
                                                $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                $d = 1;
                                                foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                    if($d == 1)
                                                    {
                                                        // Nilai Target
                                                        $data_target = [];
                                                        $data_satuan = '';
                                                        $data_target_rp = [];
                                                        foreach ($tahuns as $tahun) {
                                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                            if($program_target_satuan_rp_realisasi)
                                                            {
                                                                $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                        }
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '<td></td>';
                                                        $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                        if($kondisi_akhir_tahun)
                                                        {
                                                            $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                        } else {
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                        }
                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '</tr>';

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
                                                            $tc_27 .= '<tr>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                })->get();
                                                                $e = 1;
                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                    if($e == 1)
                                                                    {
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // Opd Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    } else {
                                                                        $tc_27 .= '<tr>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // OPD Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    }
                                                                    $e++;
                                                                }
                                                        }
                                                    } else {
                                                        $tc_27 .= '<tr>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        // Belum Nilai Target
                                                        $data_target = [];
                                                        $data_satuan = '';
                                                        $data_target_rp = [];
                                                        foreach ($tahuns as $tahun) {
                                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                            if($program_target_satuan_rp_realisasi)
                                                            {
                                                                $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                        }
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '<td></td>';
                                                        $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                        if($kondisi_akhir_tahun)
                                                        {
                                                            $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                        } else {
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                        }
                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '</tr>';

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
                                                            $tc_27 .= '<tr>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                })->get();
                                                                $e = 1;
                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                    if($e == 1)
                                                                    {
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // Opd Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    } else {
                                                                        $tc_27 .= '<tr>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // OPD Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
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
                                $tc_27 .= '<tr>';
                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                    $tc_27 .= '<td style="text-align: left;">'.$program['kode'].'</td>';
                                    $tc_27 .= '<td style="text-align: left;">'.$program['deskripsi'].'</td>';
                                    // Belum Indikator Kinerja Program
                                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q){
                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                    })->get();
                                    $c = 1;
                                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                        if($c == 1)
                                        {
                                                $tc_27 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                // Opd Program Indikator
                                                $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                $d = 1;
                                                foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                    if($d == 1)
                                                    {
                                                        // Nilai Target
                                                        $data_target = [];
                                                        $data_satuan = '';
                                                        $data_target_rp = [];
                                                        foreach ($tahuns as $tahun) {
                                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                            if($program_target_satuan_rp_realisasi)
                                                            {
                                                                $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                        }
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '<td></td>';
                                                        $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                        if($kondisi_akhir_tahun)
                                                        {
                                                            $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                        } else {
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                        }
                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '</tr>';

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
                                                            $tc_27 .= '<tr>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                })->get();
                                                                $e = 1;
                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                    if($e == 1)
                                                                    {
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // Opd Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    } else {
                                                                        $tc_27 .= '<tr>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // OPD Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    }
                                                                    $e++;
                                                                }
                                                        }
                                                    } else {
                                                        $tc_27 .= '<tr>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        // Belum Nilai Target
                                                        $data_target = [];
                                                        $data_satuan = '';
                                                        $data_target_rp = [];
                                                        foreach ($tahuns as $tahun) {
                                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                            if($program_target_satuan_rp_realisasi)
                                                            {
                                                                $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                        }
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '<td></td>';
                                                        $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                        if($kondisi_akhir_tahun)
                                                        {
                                                            $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                        } else {
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                        }
                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '</tr>';

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
                                                            $tc_27 .= '<tr>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                })->get();
                                                                $e = 1;
                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                    if($e == 1)
                                                                    {
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // Opd Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    } else {
                                                                        $tc_27 .= '<tr>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // OPD Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
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
                                            $tc_27 .= '<tr>';
                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                $tc_27 .= '<td style="text-align: left;">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                // Belum Opd Program Indikator
                                                $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                $d = 1;
                                                foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                    if($d == 1)
                                                    {
                                                        // Nilai Target
                                                        $data_target = [];
                                                        $data_satuan = '';
                                                        $data_target_rp = [];
                                                        foreach ($tahuns as $tahun) {
                                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                            if($program_target_satuan_rp_realisasi)
                                                            {
                                                                $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                        }
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '<td></td>';
                                                        $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                        if($kondisi_akhir_tahun)
                                                        {
                                                            $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                        } else {
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                        }
                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '</tr>';

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
                                                            $tc_27 .= '<tr>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                })->get();
                                                                $e = 1;
                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                    if($e == 1)
                                                                    {
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // Opd Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    } else {
                                                                        $tc_27 .= '<tr>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // OPD Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    }
                                                                    $e++;
                                                                }
                                                        }
                                                    } else {
                                                        $tc_27 .= '<tr>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                        // Belum Nilai Target
                                                        $data_target = [];
                                                        $data_satuan = '';
                                                        $data_target_rp = [];
                                                        foreach ($tahuns as $tahun) {
                                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                            if($program_target_satuan_rp_realisasi)
                                                            {
                                                                $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                $data_satuan = $program_target_satuan_rp_realisasi->satuan;
                                                                $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                $tc_27 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                $tc_27 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                            } else {
                                                                $tc_27 .= '<td></td>';
                                                                $tc_27 .= '<td></td>';
                                                            }
                                                        }
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '<td></td>';
                                                        $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_akhir)->first();
                                                        if($kondisi_akhir_tahun)
                                                        {
                                                            $tc_27 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                            $tc_27 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                        } else {
                                                            $tc_27 .= '<td></td>';
                                                            $tc_27 .= '<td></td>';
                                                        }
                                                        $tc_27 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                        $tc_27 .= '<td></td>';
                                                        $tc_27 .= '</tr>';

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
                                                            $tc_27 .= '<tr>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;"></td>';
                                                                $tc_27 .= '<td style="text-align: left;">'.$kegiatan['deskripsi'].'</td>';
                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $program['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                })->get();
                                                                $e = 1;
                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                    if($e == 1)
                                                                    {
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // Opd Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            }
                                                                            $f++;
                                                                        }
                                                                    } else {
                                                                        $tc_27 .= '<tr>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;"></td>';
                                                                        $tc_27 .= '<td style="text-align: left;">'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                        // OPD Kegiatan Indikator
                                                                        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                        ->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                        $f = 1;
                                                                        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
                                                                            if(f == 1)
                                                                            {
                                                                                // Nilai Target
                                                                                $kegiatan_data_target = [];
                                                                                $kegiatan_data_satuan = '';
                                                                                $kegiatan_data_target_rp = [];
                                                                                foreach ($tahuns as $tahun) {
                                                                                    $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                    if($kegiatan_target_satuan_rp_realisasi)
                                                                                    {
                                                                                        $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                        $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                        $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                        $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                        $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                }
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun_akhir)->first();
                                                                                if($kondisi_akhir_tahun)
                                                                                {
                                                                                    $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                    $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                } else {
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                }
                                                                                $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
                                                                            } else {
                                                                                $tc_27 .= '<tr>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    $tc_27 .= '<td style="text-align: left;"></td>';
                                                                                    // Nilai Target
                                                                                    $kegiatan_data_target = [];
                                                                                    $kegiatan_data_satuan = '';
                                                                                    $kegiatan_data_target_rp = [];
                                                                                    foreach ($tahuns as $tahun) {
                                                                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                        if($kegiatan_target_satuan_rp_realisasi)
                                                                                        {
                                                                                            $kegiatan_data_target[] =  $kegiatan_target_satuan_rp_realisasi->target;
                                                                                            $kegiatan_data_satuan = $kegiatan_target_satuan_rp_realisasi->satuan;
                                                                                            $kegiatan_data_target_rp[] = $kegiatan_target_satuan_rp_realisasi->target_rp;
                                                                                            $tc_27 .= '<td style="text-align:left">'.$kegiatan_target_satuan_rp_realisasi->target.' / '.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                            $tc_27 .= '<td style="text-align:left">Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                        } else {
                                                                                            $tc_27 .= '<td></td>';
                                                                                            $tc_27 .= '<td></td>';
                                                                                        }
                                                                                    }
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                    $kondisi_akhir_tahun = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun_akhir)->first();
                                                                                    if($kondisi_akhir_tahun)
                                                                                    {
                                                                                        $tc_27 .= '<td>'.array_sum($kegiatan_data_target).'/'.$kegiatan_data_satuan.'</td>';
                                                                                        $tc_27 .= '<td>Rp. '.number_format(array_sum($kegiatan_data_target_rp), 2).'</td>';
                                                                                    } else {
                                                                                        $tc_27 .= '<td></td>';
                                                                                        $tc_27 .= '<td></td>';
                                                                                    }
                                                                                    $tc_27 .= '<td style="text-align:left">'.$get_opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                    $tc_27 .= '<td></td>';
                                                                                $tc_27 .='</tr>';
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
                    $a++;
                }
        }
        $pdf = PDF::loadView('opd.laporan.tc-27', [
            'tc_27' => $tc_27
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan TC-27.pdf');
    }
}
