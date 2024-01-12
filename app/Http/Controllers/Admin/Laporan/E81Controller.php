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
use App\Exports\E81Ekspor;
use App\Models\FaktorTindakLanjutE81;
use App\Models\MasterSkalaNilaiPerangkatKinerja;
use DB;
use Validator;
use RealRashid\SweetAlert\Facades\Alert;

class E81Controller extends Controller
{
    public function laporan_e_81(Request $request)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $tahun_akhir = $get_periode->tahun_akhir;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $e_81 = '';
        $tws = MasterTw::all();
        $tahun = $request->tahun;
        $opd_id = $request->opd_id;
        $a = 1;

        $kolom7ProgramRp = [];
        $kolom8ProgramRp = [];
        $kolom9ProgramRp = [];
        $kolom10ProgramRp = [];
        $kolom11ProgramRp = [];
        $kolom12ProgramRp = [];
        $kolom14ProgramK = [];
        $kolom14ProgramRp = [];

        $getSkalas = MasterSkalaNilaiPerangkatKinerja::whereHas('pivot_tahun_master_skala_nilai_peringkat_kinerja', function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        })->get();

        $opd = MasterOpd::find($opd_id);

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

        foreach ($tujuans as $tujuan)
        {
            $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->whereHas('sasaran_indikator_kinerja', function($q) use ($opd_id){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd_id){
                    $q->whereHas('program_rpjmd', function($q) use ($opd_id){
                        $q->whereHas('program', function($q) use ($opd_id){
                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                    $q->where('opd_id', $opd_id);
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
                $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', $opd_id)->get();

                foreach ($get_sasaran_pds as $get_sasaran_pd)
                {
                    $e_81 .= '<tr>';
                        $e_81 .= '<td>'.$a.'</td>';
                        $e_81 .= '<td style="text-align:left">'.$get_sasaran_pd->deskripsi.'</td>';
                        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $get_sasaran_pd->id)->get();
                        $i_a = 1;
                        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                            if($i_a == 1)
                            {
                                    $e_81 .= '<td></td>';
                                    // Kolom 4 Start
                                    $e_81 .= '<td style="text-align:left">'.$sasaran_pd_indikator_kinerja->deskripsi.' </td>';
                                    // Kolom 4 End
                                    // Kolom 5 Start
                                    $last_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                    ->where('tahun', end($tahuns))->first();

                                    if($last_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $e_81 .= '<td>'.$last_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    }

                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 5 End
                                    // Kolom 6 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun - 1)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $cek_sasaran_pd_tw_realisasi = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                        $sasaran_pd_realisasi = [];
                                        if($cek_sasaran_pd_tw_realisasi)
                                        {
                                            $sasaran_pd_tw_realisasies = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)->get();
                                            foreach ($sasaran_pd_tw_realisasies as $sasaran_pd_tw_realisasi) {
                                                $sasaran_pd_realisasi[] = $sasaran_pd_tw_realisasi->realisasi;
                                            }
                                            $e_81 .= '<td>'.array_sum($sasaran_pd_realisasi).'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        } else {
                                            $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. 0, 00</td>';
                                    }
                                    // Kolom 6 End
                                    // Kolom 7 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $e_81 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format($cek_sasaran_pd_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. 0, 00</td>';
                                    }
                                    // Kolom 7 End
                                    // Kolom 8 - 11 Start
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 8 - 11 End
                                    // Kolom 12 Start
                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)
                                                                        ->whereHas('sasaran_pd_target_satuan_rp_realisasi', function($q) use ($tahun){
                                                                            $q->where('tahun', $tahun);
                                                                        })->first();
                                        if($sasaran_pd_realisasi_renja)
                                        {
                                            $e_81 .= '<td>'.$sasaran_pd_realisasi_renja->realisasi.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        } else {
                                            $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td></td>';
                                        $e_81 .= '<td></td>';
                                    }
                                    // Kolom 12 Start
                                    // Kolom 13 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6 = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun - 1)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6)
                                    {
                                        $cek_sasaran_pd_tw_realisasi_kolom_13_6 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6->id)->first();
                                        $sasaran_pd_realisasi_kolom_13_6 = [];
                                        if($cek_sasaran_pd_tw_realisasi_kolom_13_6)
                                        {
                                            $sasaran_pd_tw_realisasies_kolom_13_6 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                            foreach ($sasaran_pd_tw_realisasies_kolom_13_6 as $sasaran_pd_tw_realisasi_kolom_13_6) {
                                                $sasaran_pd_realisasi_kolom_13_6[] = $sasaran_pd_tw_realisasi_kolom_13_6->realisasi;
                                            }
                                        } else {
                                            $sasaran_pd_realisasi_kolom_13_6 = [];
                                        }
                                    } else {
                                        $sasaran_pd_realisasi_kolom_13_6 = [];
                                    }

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $cek_sasaran_pd_realisasi_renja_kolom_13_12 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)
                                                                                        ->whereHas('sasaran_pd_target_satuan_rp_realisasi', function($q) use ($tahun){
                                                                                            $q->where('tahun', $tahun);
                                                                                        })->first();
                                        if($cek_sasaran_pd_realisasi_renja_kolom_13_12)
                                        {
                                            $sasaran_pd_realisasi_renja_kolom_13_12 = $cek_sasaran_pd_realisasi_renja_kolom_13_12->realisasi;
                                        } else {
                                            $sasaran_pd_realisasi_renja_kolom_13_12 = 0;
                                        }
                                    } else {
                                        $sasaran_pd_realisasi_renja_kolom_13_12 = 0;
                                    }
                                    $kolom_13 = array_sum($sasaran_pd_realisasi_kolom_13_6) + $sasaran_pd_realisasi_renja_kolom_13_12;
                                    $e_81 .= '<td>'.$kolom_13.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp.0, 00</td>';
                                    // Kolom 13 End

                                    // Kolom 14 Start
                                    $last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5 = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                    ->where('tahun', end($tahuns))->first();
                                    if($last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5)
                                    {
                                        if($last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5->target)
                                        {
                                            $realisasi_kolom_14 = ((array_sum($sasaran_pd_realisasi_kolom_13_6) + $sasaran_pd_realisasi_renja_kolom_13_12) / $last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                            $e_81 .= '<td>'. $realisasi_kolom_14 .'</td>';
                                        } else {
                                            $e_81 .= '<td>0</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td>0</td>';
                                    }
                                    $e_81 .= '<td>0,00</td>';
                                    // Kolom 14 End
                                    $e_81 .= '<td>'.$opd->nama.'</td>';
                                $e_81 .= '</tr>';
                            } else {
                                $e_81 .= '<tr>';
                                    $e_81 .= '<td></td>';
                                    $e_81 .= '<td></td>';
                                    $e_81 .= '<td></td>';
                                    // Kolom 4 Start
                                    $e_81 .= '<td style="text-align:left">'.$sasaran_pd_indikator_kinerja->deskripsi.' </td>';
                                    // Kolom 4 End
                                    // Kolom 5 Start
                                    $last_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                    ->where('tahun', end($tahuns))->first();

                                    if($last_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $e_81 .= '<td>'.$last_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    }

                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 5 End
                                    // Kolom 6 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun - 1)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $cek_sasaran_pd_tw_realisasi = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                        $sasaran_pd_realisasi = [];
                                        if($cek_sasaran_pd_tw_realisasi)
                                        {
                                            $sasaran_pd_tw_realisasies = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)->get();
                                            foreach ($sasaran_pd_tw_realisasies as $sasaran_pd_tw_realisasi) {
                                                $sasaran_pd_realisasi[] = $sasaran_pd_tw_realisasi->realisasi;
                                            }
                                            $e_81 .= '<td>'.array_sum($sasaran_pd_realisasi).'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        } else {
                                            $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. 0, 00</td>';
                                    }
                                    // Kolom 6 End
                                    // Kolom 7 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $e_81 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format($cek_sasaran_pd_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. 0, 00</td>';
                                    }
                                    // Kolom 7 End
                                    // Kolom 8 - 11 Start
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 8 - 11 End
                                    // Kolom 12 Start
                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)
                                                                    ->whereHas('sasaran_pd_target_satuan_rp_realisasi', function($q) use ($tahun){
                                                                        $q->where('tahun', $tahun);
                                                                    })->first();
                                        if($sasaran_pd_realisasi_renja)
                                        {
                                            $e_81 .= '<td>'.$sasaran_pd_realisasi_renja->realisasi.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        } else {
                                            $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td></td>';
                                        $e_81 .= '<td></td>';
                                    }
                                    // Kolom 12 Start
                                    // Kolom 13 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6 = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun - 1)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6)
                                    {
                                        $cek_sasaran_pd_tw_realisasi_kolom_13_6 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6->id)->first();
                                        $sasaran_pd_realisasi_kolom_13_6 = [];
                                        if($cek_sasaran_pd_tw_realisasi_kolom_13_6)
                                        {
                                            $sasaran_pd_tw_realisasies_kolom_13_6 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                            foreach ($sasaran_pd_tw_realisasies_kolom_13_6 as $sasaran_pd_tw_realisasi_kolom_13_6) {
                                                $sasaran_pd_realisasi_kolom_13_6[] = $sasaran_pd_tw_realisasi_kolom_13_6->realisasi;
                                            }
                                        } else {
                                            $sasaran_pd_realisasi_kolom_13_6 = [];
                                        }
                                    } else {
                                        $sasaran_pd_realisasi_kolom_13_6 = [];
                                    }

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $cek_sasaran_pd_realisasi_renja_kolom_13_12 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)
                                                                        ->whereHas('sasaran_pd_target_satuan_rp_realisasi', function($q) use ($tahun){
                                                                            $q->where('tahun', $tahun);
                                                                        })->first();
                                        if($cek_sasaran_pd_realisasi_renja_kolom_13_12)
                                        {
                                            $sasaran_pd_realisasi_renja_kolom_13_12 = $cek_sasaran_pd_realisasi_renja_kolom_13_12->realisasi;
                                        } else {
                                            $sasaran_pd_realisasi_renja_kolom_13_12 = 0;
                                        }
                                    } else {
                                        $sasaran_pd_realisasi_renja_kolom_13_12 = 0;
                                    }
                                    $kolom_13 = array_sum($sasaran_pd_realisasi_kolom_13_6) + $sasaran_pd_realisasi_renja_kolom_13_12;
                                    $e_81 .= '<td>'.$kolom_13.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp.0, 00</td>';
                                    // Kolom 13 End

                                    // Kolom 14 Start
                                    $last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5 = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                    ->where('tahun', end($tahuns))->first();
                                    if($last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5)
                                    {
                                        if($last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5->target)
                                        {
                                            $realisasi_kolom_14 = ((array_sum($sasaran_pd_realisasi_kolom_13_6) + $sasaran_pd_realisasi_renja_kolom_13_12) / $last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                            $e_81 .= '<td>'. $realisasi_kolom_14 .'</td>';
                                        } else {
                                            $e_81 .= '<td>0</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td>0</td>';
                                    }
                                    $e_81 .= '<td>0,00</td>';
                                    // Kolom 14 End
                                    $e_81 .= '<td>'.$opd->nama.'</td>';
                                $e_81 .= '</tr>';
                            }
                            $i_a++;
                        }

                    $get_programs = Program::whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                        $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                            $q->where('opd_id', $opd_id);
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
                        $e_81 .= '<tr>';
                            $e_81 .= '<td>pr.'.$a.'</td>';
                            $e_81 .= '<td></td>';
                            $e_81 .= '<td style="text-align:left">'.$program['deskripsi'].'</td>';

                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                $q->where('opd_id', $opd_id);
                            })->get();
                            $b = 1;
                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                if($b == 1)
                                {
                                        // Kolom 4 Start
                                        $e_81 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.' </td>';
                                        // Kolom 4 End

                                        // Kolom 5 Start
                                        $program_target_satuan_rp_realisasi_target = [];;
                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                        foreach ($tahuns as $item) {
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $item)->first();
                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                        }
                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', end($tahuns))->first();
                                        $e_81 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                        // Kolom 5 End

                                        // Kolom 6 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun - 1)->first();

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
                                                $e_81 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0, 00</td>';
                                            }
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                        // Kolom 6 End

                                        // Kolom 7 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();

                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_81 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp_renja, 2, ',', '.').'</td>';
                                            $kolom7ProgramRp[] = $cek_program_target_satuan_rp_realisasi->target_rp_renja;
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }

                                        // Kolom 7 End

                                        // Kolom 8 - 11 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();

                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $i_tw = 1;
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                    if($i_tw == 1)
                                                    {
                                                        $kolom8ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 2)
                                                    {
                                                        $kolom9ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 3)
                                                    {
                                                        $kolom10ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 4)
                                                    {
                                                        $kolom11ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                } else {
                                                    $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                $i_tw++;
                                            }
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }
                                        // Kolom 8 - 11 End

                                        // Kolom 12 Start
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_tw_realisasi_realisasi = [];
                                            $program_tw_realisasi_realisasi_rp = [];
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                            }
                                            $e_81 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                            $kolom12ProgramRp[] = array_sum($program_tw_realisasi_realisasi_rp);
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }
                                        // Kolom 12 Start

                                        // Kolom 13 Start
                                        $cek_program_target_satuan_rp_realisasi_kolom_13_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun - 1)->first();
                                        $program_realisasi_kolom_13_6 = [];
                                        $program_realisasi_rp_kolom_13_6 = [];
                                        if($cek_program_target_satuan_rp_realisasi_kolom_13_6)
                                        {
                                            $cek_program_tw_realisasi_kolom_13_6 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                            if($cek_program_tw_realisasi_kolom_13_6)
                                            {
                                                $program_tw_realisasies_kolom_13_6 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                foreach ($program_tw_realisasies_kolom_13_6 as $program_tw_realisasi_kolom_13_6) {
                                                    $program_realisasi_kolom_13_6[] = $program_tw_realisasi_kolom_13_6->realisasi;
                                                    $program_realisasi_rp_kolom_13_6[] = $program_tw_realisasi_kolom_13_6->realisasi_rp;
                                                }
                                            } else {
                                                $program_realisasi_kolom_13_6[] = 0;
                                                $program_realisasi_rp_kolom_13_6[] = 0;
                                            }
                                        } else {
                                            $program_realisasi_kolom_13_6[] = 0;
                                            $program_realisasi_rp_kolom_13_6[] = 0;
                                        }

                                        $program_tw_realisasi_realisasi_kolom_13_12 = [];
                                        $program_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi_kolom_13_12 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                $program_tw_realisasi_realisasi_kolom_13_12[] = $cek_program_tw_realisasi_kolom_13_12?$cek_program_tw_realisasi_kolom_13_12->realisasi : 0;
                                                $program_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_program_tw_realisasi_kolom_13_12?$cek_program_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                            }
                                        } else {
                                            $program_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                            $program_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                        }
                                        $realisasi_kolom_13 = array_sum($program_realisasi_kolom_13_6) + array_sum($program_tw_realisasi_realisasi_kolom_13_12);
                                        $realisasi_rp_kolom_13 = array_sum($program_realisasi_rp_kolom_13_6) + array_sum($program_tw_realisasi_realisasi_rp_kolom_13_12);
                                        $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$program_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'</td>';
                                        // Kolom 13 End
                                        // Kolom 14 Start
                                        $program_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                        foreach ($tahuns as $item) {
                                            $cek_program_target_satuan_rp_realisasi_kolom_14_5 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $item)->first();

                                            $program_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_program_target_satuan_rp_realisasi_kolom_14_5 ? $cek_program_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                        }

                                        $last_program_target_satuan_rp_realisasi_kolom_14_5 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', end($tahuns))->first();
                                        if($last_program_target_satuan_rp_realisasi_kolom_14_5)
                                        {
                                            if($last_program_target_satuan_rp_realisasi_kolom_14_5->target)
                                            {
                                                $target_kolom_14 = ($realisasi_kolom_13 / $last_program_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                            } else {
                                                $target_kolom_14 = 0;
                                            }
                                        } else {
                                            $target_kolom_14 = 0;
                                        }

                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                        {
                                            $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($program_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                        } else {
                                            $target_rp_kolom_14 = 0;
                                        }

                                        $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                        $e_81 .= '<td>'.number_format($target_rp_kolom_14, 2, ',', '.').'</td>';
                                        $kolom14ProgramK[] = $target_kolom_14;
                                        $kolom14ProgramRp[] = $target_rp_kolom_14;
                                        // Kolom 14 End
                                        $e_81 .= '<td>'.$opd->nama.'</td>';
                                    $e_81 .= '</tr>';
                                } else {
                                    $e_81 .= '<tr>';
                                        $e_81 .= '<td></td>';
                                        $e_81 .= '<td></td>';
                                        $e_81 .= '<td></td>';
                                        // Kolom 4 Start
                                        $e_81 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.' </td>';
                                        // Kolom 4 End

                                        // Kolom 5 Start
                                        $program_target_satuan_rp_realisasi_target = [];;
                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                        foreach ($tahuns as $item) {
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $item)->first();
                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                        }
                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', end($tahuns))->first();
                                        $e_81 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                        // Kolom 5 End

                                        // Kolom 6 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun - 1)->first();

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
                                                $e_81 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0, 00</td>';
                                            }
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                        // Kolom 6 End

                                        // Kolom 7 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();

                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_81 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp_renja, 2, ',', '.').'</td>';
                                            $kolom7ProgramRp[] = $cek_program_target_satuan_rp_realisasi->target_rp_renja;
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }

                                        // Kolom 7 End

                                        // Kolom 8 - 11 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();

                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $i_tw = 1;
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                    if($i_tw == 1)
                                                    {
                                                        $kolom8ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 2)
                                                    {
                                                        $kolom9ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 3)
                                                    {
                                                        $kolom10ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 4)
                                                    {
                                                        $kolom11ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    $i_tw++;
                                                } else {
                                                    $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                            }
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }
                                        // Kolom 8 - 11 End

                                        // Kolom 12 Start
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_tw_realisasi_realisasi = [];
                                            $program_tw_realisasi_realisasi_rp = [];
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                            }
                                            $e_81 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                            $kolom12ProgramRp[] = array_sum($program_tw_realisasi_realisasi_rp);
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }
                                        // Kolom 12 Start

                                        // Kolom 13 Start
                                        $cek_program_target_satuan_rp_realisasi_kolom_13_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun - 1)->first();
                                        $program_realisasi_kolom_13_6 = [];
                                        $program_realisasi_rp_kolom_13_6 = [];
                                        if($cek_program_target_satuan_rp_realisasi_kolom_13_6)
                                        {
                                            $cek_program_tw_realisasi_kolom_13_6 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                            if($cek_program_tw_realisasi_kolom_13_6)
                                            {
                                                $program_tw_realisasies_kolom_13_6 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                foreach ($program_tw_realisasies_kolom_13_6 as $program_tw_realisasi_kolom_13_6) {
                                                    $program_realisasi_kolom_13_6[] = $program_tw_realisasi_kolom_13_6->realisasi;
                                                    $program_realisasi_rp_kolom_13_6[] = $program_tw_realisasi_kolom_13_6->realisasi_rp;
                                                }
                                            } else {
                                                $program_realisasi_kolom_13_6[] = 0;
                                                $program_realisasi_rp_kolom_13_6[] = 0;
                                            }
                                        } else {
                                            $program_realisasi_kolom_13_6[] = 0;
                                            $program_realisasi_rp_kolom_13_6[] = 0;
                                        }

                                        $program_tw_realisasi_realisasi_kolom_13_12 = [];
                                        $program_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi_kolom_13_12 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                $program_tw_realisasi_realisasi_kolom_13_12[] = $cek_program_tw_realisasi_kolom_13_12?$cek_program_tw_realisasi_kolom_13_12->realisasi : 0;
                                                $program_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_program_tw_realisasi_kolom_13_12?$cek_program_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                            }
                                        } else {
                                            $program_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                            $program_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                        }
                                        $realisasi_kolom_13 = array_sum($program_realisasi_kolom_13_6) + array_sum($program_tw_realisasi_realisasi_kolom_13_12);
                                        $realisasi_rp_kolom_13 = array_sum($program_realisasi_rp_kolom_13_6) + array_sum($program_tw_realisasi_realisasi_rp_kolom_13_12);
                                        $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$program_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'</td>';
                                        // Kolom 13 End
                                        // Kolom 14 Start
                                        $program_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                        foreach ($tahuns as $item) {
                                            $cek_program_target_satuan_rp_realisasi_kolom_14_5 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $item)->first();

                                            $program_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_program_target_satuan_rp_realisasi_kolom_14_5 ? $cek_program_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                        }

                                        $last_program_target_satuan_rp_realisasi_kolom_14_5 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', end($tahuns))->first();
                                        if($last_program_target_satuan_rp_realisasi_kolom_14_5)
                                        {
                                            if($last_program_target_satuan_rp_realisasi_kolom_14_5->target)
                                            {
                                                $target_kolom_14 = ($realisasi_kolom_13 / $last_program_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                            } else {
                                                $target_kolom_14 = 0;
                                            }
                                        } else {
                                            $target_kolom_14 = 0;
                                        }

                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                        {
                                            $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($program_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                        } else {
                                            $target_rp_kolom_14 = 0;
                                        }

                                        $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                        $e_81 .= '<td>'.number_format($target_rp_kolom_14, 2, ',', '.').'</td>';
                                        $kolom14ProgramK[] = $target_kolom_14;
                                        $kolom14ProgramRp[] = $target_rp_kolom_14;
                                        // Kolom 14 End
                                        $e_81 .= '<td>'.$opd->nama.'</td>';
                                    $e_81 .= '</tr>';
                                }
                                $b++;
                            }

                        $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                $q->where('opd_id', $opd_id);
                            });
                        })->get();
                        $kegiatans = [];

                        foreach ($get_kegiatans as $get_kegiatan)
                        {
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
                            $e_81 .= '<tr>';
                                $e_81 .= '<td>kg.'.$a.'</td>';
                                $e_81 .= '<td></td>';
                                $e_81 .= '<td style="text-align:left">'.$kegiatan['deskripsi'].'</td>';

                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                    $q->where('opd_id', $opd_id);
                                })->get();
                                $c = 1;
                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            // Kolom 4 Start
                                            $e_81 .= '<td style="text-align:left">'.$kegiatan_indikator_kinerja->deskripsi.' </td>';
                                            // Kolom 4 End

                                            // Kolom 5 Start
                                            $kegiatan_target_satuan_rp_realisasi_target = [];;
                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();
                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                            }
                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', end($tahuns))->first();
                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $e_81 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            }
                                            $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End

                                            // Kolom 6 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun - 1)->first();

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
                                                    $e_81 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                                }
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0, 00</td>';
                                            }
                                            // Kolom 6 End

                                            // Kolom 7 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();

                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $e_81 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp_renja, 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }

                                            // Kolom 7 End

                                            // Kolom 8 - 11 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();

                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $e_81 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. 0,00</td>';
                                                    }
                                                }
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }
                                            // Kolom 8 - 11 End

                                            // Kolom 12 Start
                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $kegiatan_tw_realisasi_realisasi = [];
                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                }
                                                $e_81 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }
                                            // Kolom 12 Start

                                            // Kolom 13 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun - 1)->first();
                                            $kegiatan_realisasi_kolom_13_6 = [];
                                            $kegiatan_realisasi_rp_kolom_13_6 = [];
                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6)
                                            {
                                                $cek_kegiatan_tw_realisasi_kolom_13_6 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                                if($cek_kegiatan_tw_realisasi_kolom_13_6)
                                                {
                                                    $kegiatan_tw_realisasies_kolom_13_6 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                    foreach ($kegiatan_tw_realisasies_kolom_13_6 as $kegiatan_tw_realisasi_kolom_13_6) {
                                                        $kegiatan_realisasi_kolom_13_6[] = $kegiatan_tw_realisasi_kolom_13_6->realisasi;
                                                        $kegiatan_realisasi_rp_kolom_13_6[] = $kegiatan_tw_realisasi_kolom_13_6->realisasi_rp;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_kolom_13_6[] = 0;
                                                    $kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                }
                                            } else {
                                                $kegiatan_realisasi_kolom_13_6[] = 0;
                                                $kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                            }

                                            $kegiatan_tw_realisasi_realisasi_kolom_13_12 = [];
                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi_kolom_13_12 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $kegiatan_tw_realisasi_realisasi_kolom_13_12[] = $cek_kegiatan_tw_realisasi_kolom_13_12?$cek_kegiatan_tw_realisasi_kolom_13_12->realisasi : 0;
                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_kegiatan_tw_realisasi_kolom_13_12?$cek_kegiatan_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                                }
                                            } else {
                                                $kegiatan_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                            }
                                            $realisasi_kolom_13 = array_sum($kegiatan_realisasi_kolom_13_6) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_13_12);
                                            $realisasi_rp_kolom_13 = array_sum($kegiatan_realisasi_rp_kolom_13_6) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_13_12);
                                            $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'</td>';
                                            // Kolom 13 End
                                            // Kolom 14 Start
                                            $kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                            foreach ($tahuns as $item) {
                                                $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5 ? $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                            }

                                            $last_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', end($tahuns))->first();
                                            if($last_kegiatan_target_satuan_rp_realisasi_kolom_14_5)
                                            {
                                                if($last_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target)
                                                {
                                                    $target_kolom_14 = ($realisasi_kolom_13 / $last_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                                } else {
                                                    $target_kolom_14 = 0;
                                                }
                                            } else {
                                                $target_kolom_14 = 0;
                                            }

                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                            {
                                                $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                            } else {
                                                $target_rp_kolom_14 = 0;
                                            }

                                            $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                            $e_81 .= '<td>'.number_format($target_rp_kolom_14, 2, ',', '.').'</td>';
                                            // Kolom 14 End
                                            $e_81 .= '<td>'.$opd->nama.'</td>';
                                        $e_81 .= '</tr>';
                                    } else {
                                        $e_81 .= '<tr>';
                                            $e_81 .= '<td></td>';
                                            $e_81 .= '<td></td>';
                                            $e_81 .= '<td></td>';
                                            // Kolom 4 Start
                                            $e_81 .= '<td style="text-align:left">'.$kegiatan_indikator_kinerja->deskripsi.' </td>';
                                            // Kolom 4 End

                                            // Kolom 5 Start
                                            $kegiatan_target_satuan_rp_realisasi_target = [];;
                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();
                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                            }
                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', end($tahuns))->first();
                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $e_81 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            }
                                            $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End

                                            // Kolom 6 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun - 1)->first();

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
                                                    $e_81 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                                }
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0, 00</td>';
                                            }
                                            // Kolom 6 End

                                            // Kolom 7 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();

                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $e_81 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp_renja, 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }

                                            // Kolom 7 End

                                            // Kolom 8 - 11 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();

                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $e_81 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. 0,00</td>';
                                                    }
                                                }
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }
                                            // Kolom 8 - 11 End

                                            // Kolom 12 Start
                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $kegiatan_tw_realisasi_realisasi = [];
                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                }
                                                $e_81 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }
                                            // Kolom 12 Start

                                            // Kolom 13 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun - 1)->first();
                                            $kegiatan_realisasi_kolom_13_6 = [];
                                            $kegiatan_realisasi_rp_kolom_13_6 = [];
                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6)
                                            {
                                                $cek_kegiatan_tw_realisasi_kolom_13_6 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                                if($cek_kegiatan_tw_realisasi_kolom_13_6)
                                                {
                                                    $kegiatan_tw_realisasies_kolom_13_6 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                    foreach ($kegiatan_tw_realisasies_kolom_13_6 as $kegiatan_tw_realisasi_kolom_13_6) {
                                                        $kegiatan_realisasi_kolom_13_6[] = $kegiatan_tw_realisasi_kolom_13_6->realisasi;
                                                        $kegiatan_realisasi_rp_kolom_13_6[] = $kegiatan_tw_realisasi_kolom_13_6->realisasi_rp;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_kolom_13_6[] = 0;
                                                    $kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                }
                                            } else {
                                                $kegiatan_realisasi_kolom_13_6[] = 0;
                                                $kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                            }

                                            $kegiatan_tw_realisasi_realisasi_kolom_13_12 = [];
                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi_kolom_13_12 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $kegiatan_tw_realisasi_realisasi_kolom_13_12[] = $cek_kegiatan_tw_realisasi_kolom_13_12?$cek_kegiatan_tw_realisasi_kolom_13_12->realisasi : 0;
                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_kegiatan_tw_realisasi_kolom_13_12?$cek_kegiatan_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                                }
                                            } else {
                                                $kegiatan_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                            }
                                            $realisasi_kolom_13 = array_sum($kegiatan_realisasi_kolom_13_6) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_13_12);
                                            $realisasi_rp_kolom_13 = array_sum($kegiatan_realisasi_rp_kolom_13_6) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_13_12);
                                            $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'</td>';
                                            // Kolom 13 End
                                            // Kolom 14 Start
                                            $kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                            foreach ($tahuns as $item) {
                                                $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5 ? $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                            }

                                            $last_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', end($tahuns))->first();
                                            if($last_kegiatan_target_satuan_rp_realisasi_kolom_14_5)
                                            {
                                                if($last_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target)
                                                {
                                                    $target_kolom_14 = ($realisasi_kolom_13 / $last_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                                } else {
                                                    $target_kolom_14 = 0;
                                                }
                                            } else {
                                                $target_kolom_14 = 0;
                                            }

                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                            {
                                                $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                            } else {
                                                $target_rp_kolom_14 = 0;
                                            }

                                            $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                            $e_81 .= '<td>'.number_format($target_rp_kolom_14, 2, ',', '.').'</td>';
                                            // Kolom 14 End
                                            $e_81 .= '<td>'.$opd->nama.'</td>';
                                        $e_81 .= '</tr>';
                                    }
                                    $c++;
                                }

                            $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan['id'])->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                    $q->where('opd_id', $opd_id);
                                });
                            })->get();

                            $sub_kegiatans = [];

                            foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)
                                                        ->latest()->first();
                                if($cek_perubahan_sub_kegiatan)
                                {
                                    $sub_kegiatans[] = [
                                        'id' => $cek_perubahan_sub_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_sub_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi,
                                        'tahun_perubahan' => $cek_perubahan_sub_kegiatan->tahun_perubahan
                                    ];
                                } else {
                                    $sub_kegiatans[] = [
                                        'id' => $get_sub_kegiatan->id,
                                        'kode' => $get_sub_kegiatan->kode,
                                        'deskripsi' => $get_sub_kegiatan->deskripsi,
                                        'tahun_perubahan' => $get_sub_kegiatan->tahun_perubahan
                                    ];
                                }
                            }

                            foreach ($sub_kegiatans as $sub_kegiatan) {
                                $e_81 .= '<tr>';
                                    $e_81 .= '<td>skg.'.$a.'</td>';
                                    $e_81 .= '<td></td>';
                                    $e_81 .= '<td style="text-align:left">'.$sub_kegiatan['deskripsi'].'</td>';
                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                        $q->where('opd_id', $opd_id);
                                    })->get();
                                    $d = 1;
                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                        if ($d == 1) {
                                                // Kolom 4 Start
                                                $e_81 .= '<td style="text-align:left">'.$sub_kegiatan_indikator_kinerja->deskripsi.' </td>';
                                                // Kolom 4 End

                                                // Kolom 5 Start
                                                $sub_kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                        $q->where('opd_id', $opd_id);
                                                        $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                            $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();
                                                    $sub_kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_sub_kegiatan_target_satuan_rp_realisasi ? $cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                }
                                                $last_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', end($tahuns))->first();
                                                if($last_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$last_sub_kegiatan_target_satuan_rp_realisasi->target.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                }
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End

                                                // Kolom 6 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun - 1)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $sub_kegiatan_realisasi = [];
                                                    $sub_kegiatan_realisasi_rp = [];
                                                    if($cek_sub_kegiatan_tw_realisasi)
                                                    {
                                                        $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                            $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                            $sub_kegiatan_realisasi_rp[] = $sub_kegiatan_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td>'.array_sum($sub_kegiatan_realisasi).'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. 0,00</td>';
                                                    }
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 6 End

                                                // Kolom 7 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }

                                                // Kolom 7 End

                                                // Kolom 8 - 11 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                        {
                                                            $e_81 .= '<td>'.$cek_sub_kegiatan_tw_realisasi->realisasi.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_81 .= '<td>Rp. '.number_format($cek_sub_kegiatan_tw_realisasi->realiasi_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_81 .= '<td>Rp. 0,00</td>';
                                                        }
                                                    }
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 8 - 11 End

                                                // Kolom 12 Start
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $sub_kegiatan_tw_realisasi_realisasi = [];
                                                    $sub_kegiatan_tw_realisasi_realisasi_rp = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $sub_kegiatan_tw_realisasi_realisasi[] = $cek_sub_kegiatan_tw_realisasi?$cek_sub_kegiatan_tw_realisasi->realisasi : 0;
                                                        $sub_kegiatan_tw_realisasi_realisasi_rp[] = $cek_sub_kegiatan_tw_realisasi?$cek_sub_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                    }
                                                    $e_81 .= '<td>'.array_sum($sub_kegiatan_tw_realisasi_realisasi).'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 12 Start

                                                // Kolom 13 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun - 1)->first();
                                                $sub_kegiatan_realisasi_kolom_13_6 = [];
                                                $sub_kegiatan_realisasi_rp_kolom_13_6 = [];
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6)
                                                {
                                                    $cek_sub_kegiatan_tw_realisasi_kolom_13_6 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                                    if($cek_sub_kegiatan_tw_realisasi_kolom_13_6)
                                                    {
                                                        $sub_kegiatan_tw_realisasies_kolom_13_6 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                        foreach ($sub_kegiatan_tw_realisasies_kolom_13_6 as $sub_kegiatan_tw_realisasi_kolom_13_6) {
                                                            $sub_kegiatan_realisasi_kolom_13_6[] = $sub_kegiatan_tw_realisasi_kolom_13_6->realisasi;
                                                            $sub_kegiatan_realisasi_rp_kolom_13_6[] = $sub_kegiatan_tw_realisasi_kolom_13_6->realisasi_rp;
                                                        }
                                                    } else {
                                                        $sub_kegiatan_realisasi_kolom_13_6[] = 0;
                                                        $sub_kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                    }
                                                } else {
                                                    $sub_kegiatan_realisasi_kolom_13_6[] = 0;
                                                    $sub_kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                }

                                                $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12 = [];
                                                $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi_kolom_13_12 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12[] = $cek_sub_kegiatan_tw_realisasi_kolom_13_12?$cek_sub_kegiatan_tw_realisasi_kolom_13_12->realisasi : 0;
                                                        $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_sub_kegiatan_tw_realisasi_kolom_13_12?$cek_sub_kegiatan_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                                    }
                                                } else {
                                                    $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                                    $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                                }
                                                $realisasi_kolom_13 = array_sum($sub_kegiatan_realisasi_kolom_13_6) + array_sum($sub_kegiatan_tw_realisasi_realisasi_kolom_13_12);
                                                $realisasi_rp_kolom_13 = array_sum($sub_kegiatan_realisasi_rp_kolom_13_6) + array_sum($sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12);
                                                $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'.</td>';
                                                // Kolom 13 End
                                                // Kolom 14 Start
                                                $sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                        $q->where('opd_id', $opd_id);
                                                        $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                            $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 ? $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                                }

                                                $last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', end($tahuns))->first();
                                                if($last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5)
                                                {
                                                    if($last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target)
                                                    {
                                                        $target_kolom_14 = ($realisasi_kolom_13 / $last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                                    } else {
                                                        $target_kolom_14 = 0;
                                                    }
                                                } else {
                                                    $target_kolom_14 = 0;
                                                }
                                                if(array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                                {
                                                    $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                                } else {
                                                    $target_rp_kolom_14 = 0;
                                                }

                                                $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                                $e_81 .= '<td>'.$target_rp_kolom_14.'</td>';
                                                // Kolom 14 End
                                                $e_81 .= '<td>'.$opd->nama.'</td>';
                                            $e_81 .='</tr>';
                                        } else {
                                            $e_81 .= '<tr>';
                                                $e_81 .= '<td></td>';
                                                $e_81 .= '<td></td>';
                                                $e_81 .= '<td></td>';
                                                // Kolom 4 Start
                                                $e_81 .= '<td style="text-align:left">'.$sub_kegiatan_indikator_kinerja->deskripsi.' </td>';
                                                // Kolom 4 End

                                                // Kolom 5 Start
                                                $sub_kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                        $q->where('opd_id', $opd_id);
                                                        $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                            $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();
                                                    $sub_kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_sub_kegiatan_target_satuan_rp_realisasi ? $cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                }
                                                $last_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', end($tahuns))->first();
                                                if($last_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$last_sub_kegiatan_target_satuan_rp_realisasi->target.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                }
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End

                                                // Kolom 6 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun - 1)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $sub_kegiatan_realisasi = [];
                                                    $sub_kegiatan_realisasi_rp = [];
                                                    if($cek_sub_kegiatan_tw_realisasi)
                                                    {
                                                        $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                            $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                            $sub_kegiatan_realisasi_rp[] = $sub_kegiatan_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td>'.array_sum($sub_kegiatan_realisasi).'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. 0,00</td>';
                                                    }
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 6 End

                                                // Kolom 7 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }

                                                // Kolom 7 End

                                                // Kolom 8 - 11 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                        {
                                                            $e_81 .= '<td>'.$cek_sub_kegiatan_tw_realisasi->realisasi.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_81 .= '<td>Rp. '.number_format($cek_sub_kegiatan_tw_realisasi->realiasi_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_81 .= '<td>Rp. 0,00</td>';
                                                        }
                                                    }
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 8 - 11 End

                                                // Kolom 12 Start
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $sub_kegiatan_tw_realisasi_realisasi = [];
                                                    $sub_kegiatan_tw_realisasi_realisasi_rp = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $sub_kegiatan_tw_realisasi_realisasi[] = $cek_sub_kegiatan_tw_realisasi?$cek_sub_kegiatan_tw_realisasi->realisasi : 0;
                                                        $sub_kegiatan_tw_realisasi_realisasi_rp[] = $cek_sub_kegiatan_tw_realisasi?$cek_sub_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                    }
                                                    $e_81 .= '<td>'.array_sum($sub_kegiatan_tw_realisasi_realisasi).'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 12 Start

                                                // Kolom 13 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun - 1)->first();
                                                $sub_kegiatan_realisasi_kolom_13_6 = [];
                                                $sub_kegiatan_realisasi_rp_kolom_13_6 = [];
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6)
                                                {
                                                    $cek_sub_kegiatan_tw_realisasi_kolom_13_6 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                                    if($cek_sub_kegiatan_tw_realisasi_kolom_13_6)
                                                    {
                                                        $sub_kegiatan_tw_realisasies_kolom_13_6 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                        foreach ($sub_kegiatan_tw_realisasies_kolom_13_6 as $sub_kegiatan_tw_realisasi_kolom_13_6) {
                                                            $sub_kegiatan_realisasi_kolom_13_6[] = $sub_kegiatan_tw_realisasi_kolom_13_6->realisasi;
                                                            $sub_kegiatan_realisasi_rp_kolom_13_6[] = $sub_kegiatan_tw_realisasi_kolom_13_6->realisasi_rp;
                                                        }
                                                    } else {
                                                        $sub_kegiatan_realisasi_kolom_13_6[] = 0;
                                                        $sub_kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                    }
                                                } else {
                                                    $sub_kegiatan_realisasi_kolom_13_6[] = 0;
                                                    $sub_kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                }

                                                $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12 = [];
                                                $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi_kolom_13_12 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12[] = $cek_sub_kegiatan_tw_realisasi_kolom_13_12?$cek_sub_kegiatan_tw_realisasi_kolom_13_12->realisasi : 0;
                                                        $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_sub_kegiatan_tw_realisasi_kolom_13_12?$cek_sub_kegiatan_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                                    }
                                                } else {
                                                    $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                                    $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                                }
                                                $realisasi_kolom_13 = array_sum($sub_kegiatan_realisasi_kolom_13_6) + array_sum($sub_kegiatan_tw_realisasi_realisasi_kolom_13_12);
                                                $realisasi_rp_kolom_13 = array_sum($sub_kegiatan_realisasi_rp_kolom_13_6) + array_sum($sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12);
                                                $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'.</td>';
                                                // Kolom 13 End
                                                // Kolom 14 Start
                                                $sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                        $q->where('opd_id', $opd_id);
                                                        $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                            $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 ? $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                                }

                                                $last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', end($tahuns))->first();
                                                if($last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5)
                                                {
                                                    if($last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target)
                                                    {
                                                        $target_kolom_14 = ($realisasi_kolom_13 / $last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                                    } else {
                                                        $target_kolom_14 = 0;
                                                    }
                                                } else {
                                                    $target_kolom_14 = 0;
                                                }
                                                if(array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                                {
                                                    $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                                } else {
                                                    $target_rp_kolom_14 = 0;
                                                }

                                                $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                                $e_81 .= '<td>'.$target_rp_kolom_14.'</td>';
                                                // Kolom 14 End
                                                $e_81 .= '<td>'.$opd->nama.'</td>';
                                            $e_81 .='</tr>';
                                        }
                                        $d++;
                                    }
                            }
                        }
                    }
                    $a++;
                }
            }
        }

        $e_81 .= '<tr><td colspan="25"></td></tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td class="font-weight-bold" align="left" colspan="5">Jumlah Anggaran dan Realisasi dari Seluruh Program</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom7ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom8ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom9ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom10ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom11ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom12ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
        $e_81 .= '</tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td class="font-weight-bold" align="left" colspan="5">Rata - rata capaian kinerja (%)</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            if(count($kolom14ProgramK))
            {
                $e_81 .= '<td>'.number_format(array_sum($kolom14ProgramK)/count($kolom14ProgramK), 2).'</td>';
            } else {
                $e_81 .= '<td>0</td>';
            }
            if(count($kolom14ProgramRp))
            {
                $e_81 .= '<td>'.number_format(array_sum($kolom14ProgramRp)/count($kolom14ProgramRp), 2).'</td>';
            } else {
                $e_81 .= '<td>0</td>';
            }
            $e_81 .= '<td></td>';
        $e_81 .= '</tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td class="font-weight-bold" align="left" colspan="5">Predikat Kinerja</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            if(count($kolom14ProgramK))
            {
                $averageCapaianKinerjaKolom14K = array_sum($kolom14ProgramK)/count($kolom14ProgramK);
            } else {
                $averageCapaianKinerjaKolom14K = 0;
            }
            if(count($kolom14ProgramRp))
            {
                $averageCapaianKinerjaKolom14Rp = array_sum($kolom14ProgramRp)/count($kolom14ProgramRp);
            } else {
                $averageCapaianKinerjaKolom14Rp = 0;
            }

            $kriteriaKolom14K = '';
            $kriteriaKolom14Rp = '';
            foreach ($getSkalas as $getSkala) {
                if($averageCapaianKinerjaKolom14K >= $getSkala->terkecil &&  $averageCapaianKinerjaKolom14K <= $getSkala->terbesar)
                {
                    $kriteriaKolom14K = $getSkala->kriteria;
                }

                if($averageCapaianKinerjaKolom14K > 100)
                {
                    $kriteriaKolom14K = 'Sangat Tinggi';
                }
            }
            foreach ($getSkalas as $getSkala) {
                if($averageCapaianKinerjaKolom14Rp >= $getSkala->terkecil && $averageCapaianKinerjaKolom14Rp <= $getSkala->terbesar)
                {
                    $kriteriaKolom14Rp = $getSkala->kriteria;
                }

                if($averageCapaianKinerjaKolom14Rp > 100)
                {
                    $kriteriaKolom14Rp = 'Sangat Tinggi';
                }
            }
            $e_81 .= '<td>'.$kriteriaKolom14K.'</td>';
            $e_81 .= '<td>'.$kriteriaKolom14Rp.'</td>';
            $e_81 .= '<td></td>';
        $e_81 .= '</tr>';

        $e_81 .= '<tr><td colspan="25"></td></tr>';
        $e_81 .= '<tr><td colspan="25"></td></tr>';
        $e_81 .= '<tr><td colspan="25"></td></tr>';

        $faktorTindakLanjutE81 = FaktorTindakLanjutE81::where('tahun', $tahun)->where('tahun_periode_id', $get_periode->id)->where('opd_id', $opd_id)->first();
        $e_81 .= '<tr>';
            $e_81 .= '<td align="left" colspan="5">Faktor pendorong keberhasilan pencapaian:</td>';
            $e_81 .= '<td align="left" colspan="20">';
                if($faktorTindakLanjutE81){
                    $e_81 .= $faktorTindakLanjutE81->faktor_pendorong;
                }
            $e_81 .='</td>';
        $e_81 .= '</tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td align="left" colspan="5">Faktor penghambat pencapaian kinerja:</td>';
            $e_81 .= '<td align="left" colspan="20">';
                if($faktorTindakLanjutE81){
                    $e_81 .= $faktorTindakLanjutE81->faktor_penghambat;
                }
            $e_81 .='</td>';
        $e_81 .= '</tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td align="left" colspan="5">Tindak lanjut yang diperlukan dalam Triwulan berikutnya:</td>';
            $e_81 .= '<td align="middle"><btn class="btn btn-warning waves-effect waves-light edit-tindak-lanjut-triwulan" data-tahun="'.$tahun.'" data-opd-id="'.$opd_id.'" data-tahun-periode-id="'.$get_periode->id.'" type="button"><i class="fas fa-edit" ></i></btn></td>';
            $e_81 .= '<td align="left" colspan="19">';
                if($faktorTindakLanjutE81){
                    $e_81 .= $faktorTindakLanjutE81->tindak_lanjut_triwulan;
                }
            $e_81 .='</td>';
        $e_81 .= '</tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td align="left" colspan="5">Tindak lanjut yang diperlukan dalam Renja Perangkat Daerah Kabupaten Madiun Berikutnya:</td>';
            $e_81 .= '<td align="middle"><btn class="btn btn-warning waves-effect waves-light edit-tindak-lanjut-renja" data-tahun="'.$tahun.'" data-opd-id="'.$opd_id.'" data-tahun-periode-id="'.$get_periode->id.'" type="button"><i class="fas fa-edit" ></i></btn></td>';
            $e_81 .= '<td align="left" colspan="19">';
                if($faktorTindakLanjutE81){
                    $e_81 .= $faktorTindakLanjutE81->tindak_lanjut_renja;
                }
            $e_81 .='</td>';
        $e_81 .= '</tr>';

        return response()->json(['e_81' => $e_81]);
    }

    public function e_81_ekspor_pdf($opd_id, $tahun){
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $tahun_akhir = $get_periode->tahun_akhir;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $e_81 = '';
        $tws = MasterTw::all();
        $a = 1;

        $kolom7ProgramRp = [];
        $kolom8ProgramRp = [];
        $kolom9ProgramRp = [];
        $kolom10ProgramRp = [];
        $kolom11ProgramRp = [];
        $kolom12ProgramRp = [];
        $kolom14ProgramK = [];
        $kolom14ProgramRp = [];

        $getSkalas = MasterSkalaNilaiPerangkatKinerja::whereHas('pivot_tahun_master_skala_nilai_peringkat_kinerja', function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        })->get();

        $opd = MasterOpd::find($opd_id);

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

        foreach ($tujuans as $tujuan)
        {
            $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->whereHas('sasaran_indikator_kinerja', function($q) use ($opd_id){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd_id){
                    $q->whereHas('program_rpjmd', function($q) use ($opd_id){
                        $q->whereHas('program', function($q) use ($opd_id){
                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                    $q->where('opd_id', $opd_id);
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
                $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', $opd_id)->get();

                foreach ($get_sasaran_pds as $get_sasaran_pd)
                {
                    $e_81 .= '<tr>';
                        $e_81 .= '<td>'.$a.'</td>';
                        $e_81 .= '<td style="text-align:left">'.$get_sasaran_pd->deskripsi.'</td>';
                        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $get_sasaran_pd->id)->get();
                        $i_a = 1;
                        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                            if($i_a == 1)
                            {
                                    $e_81 .= '<td></td>';
                                    // Kolom 4 Start
                                    $e_81 .= '<td style="text-align:left">'.$sasaran_pd_indikator_kinerja->deskripsi.' </td>';
                                    // Kolom 4 End
                                    // Kolom 5 Start
                                    $last_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                    ->where('tahun', end($tahuns))->first();

                                    if($last_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $e_81 .= '<td>'.$last_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    }

                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 5 End
                                    // Kolom 6 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun - 1)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $cek_sasaran_pd_tw_realisasi = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                        $sasaran_pd_realisasi = [];
                                        if($cek_sasaran_pd_tw_realisasi)
                                        {
                                            $sasaran_pd_tw_realisasies = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)->get();
                                            foreach ($sasaran_pd_tw_realisasies as $sasaran_pd_tw_realisasi) {
                                                $sasaran_pd_realisasi[] = $sasaran_pd_tw_realisasi->realisasi;
                                            }
                                            $e_81 .= '<td>'.array_sum($sasaran_pd_realisasi).'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        } else {
                                            $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. 0, 00</td>';
                                    }
                                    // Kolom 6 End
                                    // Kolom 7 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $e_81 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format($cek_sasaran_pd_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. 0, 00</td>';
                                    }
                                    // Kolom 7 End
                                    // Kolom 8 - 11 Start
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 8 - 11 End
                                    // Kolom 12 Start
                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)
                                                                        ->whereHas('sasaran_pd_target_satuan_rp_realisasi', function($q) use ($tahun){
                                                                            $q->where('tahun', $tahun);
                                                                        })->first();
                                        if($sasaran_pd_realisasi_renja)
                                        {
                                            $e_81 .= '<td>'.$sasaran_pd_realisasi_renja->realisasi.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        } else {
                                            $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td></td>';
                                        $e_81 .= '<td></td>';
                                    }
                                    // Kolom 12 Start
                                    // Kolom 13 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6 = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun - 1)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6)
                                    {
                                        $cek_sasaran_pd_tw_realisasi_kolom_13_6 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                        $sasaran_pd_realisasi_kolom_13_6 = [];
                                        if($cek_sasaran_pd_tw_realisasi_kolom_13_6)
                                        {
                                            $sasaran_pd_tw_realisasies_kolom_13_6 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)->get();
                                            foreach ($sasaran_pd_tw_realisasies_kolom_13_6 as $sasaran_pd_tw_realisasi_kolom_13_6) {
                                                $sasaran_pd_realisasi_kolom_13_6[] = $sasaran_pd_tw_realisasi_kolom_13_6->realisasi;
                                            }
                                        } else {
                                            $sasaran_pd_realisasi_kolom_13_6 = [];
                                        }
                                    } else {
                                        $sasaran_pd_realisasi_kolom_13_6 = [];
                                    }

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $cek_sasaran_pd_realisasi_renja_kolom_13_12 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)
                                                                                        ->whereHas('sasaran_pd_target_satuan_rp_realisasi', function($q) use ($tahun){
                                                                                            $q->where('tahun', $tahun);
                                                                                        })->first();
                                        if($cek_sasaran_pd_realisasi_renja_kolom_13_12)
                                        {
                                            $sasaran_pd_realisasi_renja_kolom_13_12 = $cek_sasaran_pd_realisasi_renja_kolom_13_12->realisasi;
                                        } else {
                                            $sasaran_pd_realisasi_renja_kolom_13_12 = 0;
                                        }
                                    } else {
                                        $sasaran_pd_realisasi_renja_kolom_13_12 = 0;
                                    }
                                    $kolom_13 = array_sum($sasaran_pd_realisasi_kolom_13_6) + $sasaran_pd_realisasi_renja_kolom_13_12;
                                    $e_81 .= '<td>'.$kolom_13.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp.0, 00</td>';
                                    // Kolom 13 End

                                    // Kolom 14 Start
                                    $last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5 = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                    ->where('tahun', end($tahuns))->first();
                                    if($last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5)
                                    {
                                        if($last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5->target)
                                        {
                                            $realisasi_kolom_14 = ((array_sum($sasaran_pd_realisasi_kolom_13_6) + $sasaran_pd_realisasi_renja_kolom_13_12) / $last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                            $e_81 .= '<td>'. $realisasi_kolom_14 .'</td>';
                                        } else {
                                            $e_81 .= '<td>0</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td>0</td>';
                                    }
                                    $e_81 .= '<td>0,00</td>';
                                    // Kolom 14 End
                                    $e_81 .= '<td>'.$opd->nama.'</td>';
                                $e_81 .= '</tr>';
                            } else {
                                $e_81 .= '<tr>';
                                    $e_81 .= '<td></td>';
                                    $e_81 .= '<td></td>';
                                    $e_81 .= '<td></td>';
                                    // Kolom 4 Start
                                    $e_81 .= '<td style="text-align:left">'.$sasaran_pd_indikator_kinerja->deskripsi.' </td>';
                                    // Kolom 4 End
                                    // Kolom 5 Start
                                    $last_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                    ->where('tahun', end($tahuns))->first();

                                    if($last_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $e_81 .= '<td>'.$last_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    }

                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 5 End
                                    // Kolom 6 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun - 1)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $cek_sasaran_pd_tw_realisasi = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                        $sasaran_pd_realisasi = [];
                                        if($cek_sasaran_pd_tw_realisasi)
                                        {
                                            $sasaran_pd_tw_realisasies = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)->get();
                                            foreach ($sasaran_pd_tw_realisasies as $sasaran_pd_tw_realisasi) {
                                                $sasaran_pd_realisasi[] = $sasaran_pd_tw_realisasi->realisasi;
                                            }
                                            $e_81 .= '<td>'.array_sum($sasaran_pd_realisasi).'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        } else {
                                            $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. 0, 00</td>';
                                    }
                                    // Kolom 6 End
                                    // Kolom 7 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $e_81 .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format($cek_sasaran_pd_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                    } else {
                                        $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. 0, 00</td>';
                                    }
                                    // Kolom 7 End
                                    // Kolom 8 - 11 Start
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                    // Kolom 8 - 11 End
                                    // Kolom 12 Start
                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)
                                                                    ->whereHas('sasaran_pd_target_satuan_rp_realisasi', function($q) use ($tahun){
                                                                        $q->where('tahun', $tahun);
                                                                    })->first();
                                        if($sasaran_pd_realisasi_renja)
                                        {
                                            $e_81 .= '<td>'.$sasaran_pd_realisasi_renja->realisasi.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        } else {
                                            $e_81 .= '<td>0/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td></td>';
                                        $e_81 .= '<td></td>';
                                    }
                                    // Kolom 12 Start
                                    // Kolom 13 Start
                                    $cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6 = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun - 1)->first();

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6)
                                    {
                                        $cek_sasaran_pd_tw_realisasi_kolom_13_6 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6->id)->first();
                                        $sasaran_pd_realisasi_kolom_13_6 = [];
                                        if($cek_sasaran_pd_tw_realisasi_kolom_13_6)
                                        {
                                            $sasaran_pd_tw_realisasies_kolom_13_6 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                            foreach ($sasaran_pd_tw_realisasies_kolom_13_6 as $sasaran_pd_tw_realisasi_kolom_13_6) {
                                                $sasaran_pd_realisasi_kolom_13_6[] = $sasaran_pd_tw_realisasi_kolom_13_6->realisasi;
                                            }
                                        } else {
                                            $sasaran_pd_realisasi_kolom_13_6 = [];
                                        }
                                    } else {
                                        $sasaran_pd_realisasi_kolom_13_6 = [];
                                    }

                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                    {
                                        $cek_sasaran_pd_realisasi_renja_kolom_13_12 = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id',$cek_sasaran_pd_target_satuan_rp_realisasi->id)
                                                                        ->whereHas('sasaran_pd_target_satuan_rp_realisasi', function($q) use ($tahun){
                                                                            $q->where('tahun', $tahun);
                                                                        })->first();
                                        if($cek_sasaran_pd_realisasi_renja_kolom_13_12)
                                        {
                                            $sasaran_pd_realisasi_renja_kolom_13_12 = $cek_sasaran_pd_realisasi_renja_kolom_13_12->realisasi;
                                        } else {
                                            $sasaran_pd_realisasi_renja_kolom_13_12 = 0;
                                        }
                                    } else {
                                        $sasaran_pd_realisasi_renja_kolom_13_12 = 0;
                                    }
                                    $kolom_13 = array_sum($sasaran_pd_realisasi_kolom_13_6) + $sasaran_pd_realisasi_renja_kolom_13_12;
                                    $e_81 .= '<td>'.$kolom_13.'/'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                    $e_81 .= '<td>Rp.0, 00</td>';
                                    // Kolom 13 End

                                    // Kolom 14 Start
                                    $last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5 = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                    ->where('tahun', end($tahuns))->first();
                                    if($last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5)
                                    {
                                        if($last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5->target)
                                        {
                                            $realisasi_kolom_14 = ((array_sum($sasaran_pd_realisasi_kolom_13_6) + $sasaran_pd_realisasi_renja_kolom_13_12) / $last_sasaran_pd_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                            $e_81 .= '<td>'. $realisasi_kolom_14 .'</td>';
                                        } else {
                                            $e_81 .= '<td>0</td>';
                                        }
                                    } else {
                                        $e_81 .= '<td>0</td>';
                                    }
                                    $e_81 .= '<td>0,00</td>';
                                    // Kolom 14 End
                                    $e_81 .= '<td>'.$opd->nama.'</td>';
                                $e_81 .= '</tr>';
                            }
                            $i_a++;
                        }

                    $get_programs = Program::whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                        $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                            $q->where('opd_id', $opd_id);
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
                        $e_81 .= '<tr>';
                            $e_81 .= '<td>pr.'.$a.'</td>';
                            $e_81 .= '<td></td>';
                            $e_81 .= '<td style="text-align:left">'.$program['deskripsi'].'</td>';

                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                $q->where('opd_id', $opd_id);
                            })->get();
                            $b = 1;
                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                if($b == 1)
                                {
                                        // Kolom 4 Start
                                        $e_81 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.' </td>';
                                        // Kolom 4 End

                                        // Kolom 5 Start
                                        $program_target_satuan_rp_realisasi_target = [];;
                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                        foreach ($tahuns as $item) {
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $item)->first();
                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                        }
                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', end($tahuns))->first();
                                        $e_81 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                        // Kolom 5 End

                                        // Kolom 6 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun - 1)->first();

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
                                                $e_81 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0, 00</td>';
                                            }
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                        // Kolom 6 End

                                        // Kolom 7 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();

                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_81 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp_renja, 2, ',', '.').'</td>';
                                            $kolom7ProgramRp[] = $cek_program_target_satuan_rp_realisasi->target_rp_renja;
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }

                                        // Kolom 7 End

                                        // Kolom 8 - 11 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();

                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $i_tw = 1;
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                    if($i_tw == 1)
                                                    {
                                                        $kolom8ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 2)
                                                    {
                                                        $kolom9ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 3)
                                                    {
                                                        $kolom10ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 4)
                                                    {
                                                        $kolom11ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                } else {
                                                    $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                $i_tw++;
                                            }
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }
                                        // Kolom 8 - 11 End

                                        // Kolom 12 Start
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_tw_realisasi_realisasi = [];
                                            $program_tw_realisasi_realisasi_rp = [];
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                            }
                                            $e_81 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                            $kolom12ProgramRp[] = array_sum($program_tw_realisasi_realisasi_rp);
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }
                                        // Kolom 12 Start

                                        // Kolom 13 Start
                                        $cek_program_target_satuan_rp_realisasi_kolom_13_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun - 1)->first();
                                        $program_realisasi_kolom_13_6 = [];
                                        $program_realisasi_rp_kolom_13_6 = [];
                                        if($cek_program_target_satuan_rp_realisasi_kolom_13_6)
                                        {
                                            $cek_program_tw_realisasi_kolom_13_6 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                            if($cek_program_tw_realisasi_kolom_13_6)
                                            {
                                                $program_tw_realisasies_kolom_13_6 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                foreach ($program_tw_realisasies_kolom_13_6 as $program_tw_realisasi_kolom_13_6) {
                                                    $program_realisasi_kolom_13_6[] = $program_tw_realisasi_kolom_13_6->realisasi;
                                                    $program_realisasi_rp_kolom_13_6[] = $program_tw_realisasi_kolom_13_6->realisasi_rp;
                                                }
                                            } else {
                                                $program_realisasi_kolom_13_6[] = 0;
                                                $program_realisasi_rp_kolom_13_6[] = 0;
                                            }
                                        } else {
                                            $program_realisasi_kolom_13_6[] = 0;
                                            $program_realisasi_rp_kolom_13_6[] = 0;
                                        }

                                        $program_tw_realisasi_realisasi_kolom_13_12 = [];
                                        $program_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi_kolom_13_12 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                $program_tw_realisasi_realisasi_kolom_13_12[] = $cek_program_tw_realisasi_kolom_13_12?$cek_program_tw_realisasi_kolom_13_12->realisasi : 0;
                                                $program_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_program_tw_realisasi_kolom_13_12?$cek_program_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                            }
                                        } else {
                                            $program_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                            $program_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                        }
                                        $realisasi_kolom_13 = array_sum($program_realisasi_kolom_13_6) + array_sum($program_tw_realisasi_realisasi_kolom_13_12);
                                        $realisasi_rp_kolom_13 = array_sum($program_realisasi_rp_kolom_13_6) + array_sum($program_tw_realisasi_realisasi_rp_kolom_13_12);
                                        $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$program_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'</td>';
                                        // Kolom 13 End
                                        // Kolom 14 Start
                                        $program_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                        foreach ($tahuns as $item) {
                                            $cek_program_target_satuan_rp_realisasi_kolom_14_5 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $item)->first();

                                            $program_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_program_target_satuan_rp_realisasi_kolom_14_5 ? $cek_program_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                        }

                                        $last_program_target_satuan_rp_realisasi_kolom_14_5 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', end($tahuns))->first();
                                        if($last_program_target_satuan_rp_realisasi_kolom_14_5)
                                        {
                                            if($last_program_target_satuan_rp_realisasi_kolom_14_5->target)
                                            {
                                                $target_kolom_14 = ($realisasi_kolom_13 / $last_program_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                            } else {
                                                $target_kolom_14 = 0;
                                            }
                                        } else {
                                            $target_kolom_14 = 0;
                                        }

                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                        {
                                            $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($program_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                        } else {
                                            $target_rp_kolom_14 = 0;
                                        }

                                        $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                        $e_81 .= '<td>'.number_format($target_rp_kolom_14, 2, ',', '.').'</td>';
                                        $kolom14ProgramK[] = $target_kolom_14;
                                        $kolom14ProgramRp[] = $target_rp_kolom_14;
                                        // Kolom 14 End
                                        $e_81 .= '<td>'.$opd->nama.'</td>';
                                    $e_81 .= '</tr>';
                                } else {
                                    $e_81 .= '<tr>';
                                        $e_81 .= '<td></td>';
                                        $e_81 .= '<td></td>';
                                        $e_81 .= '<td></td>';
                                        // Kolom 4 Start
                                        $e_81 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.' </td>';
                                        // Kolom 4 End

                                        // Kolom 5 Start
                                        $program_target_satuan_rp_realisasi_target = [];;
                                        $program_target_satuan_rp_realisasi_target_rp = [];

                                        foreach ($tahuns as $item) {
                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $item)->first();
                                            $program_target_satuan_rp_realisasi_target_rp[] = $cek_program_target_satuan_rp_realisasi ? $cek_program_target_satuan_rp_realisasi->target_rp : 0;
                                        }
                                        $last_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', end($tahuns))->first();
                                        $e_81 .= '<td>'.$last_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format(array_sum($program_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                        // Kolom 5 End

                                        // Kolom 6 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun - 1)->first();

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
                                                $e_81 .= '<td>'.array_sum($program_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($program_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0, 00</td>';
                                            }
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0, 00</td>';
                                        }
                                        // Kolom 6 End

                                        // Kolom 7 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();

                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $e_81 .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format($cek_program_target_satuan_rp_realisasi->target_rp_renja, 2, ',', '.').'</td>';
                                            $kolom7ProgramRp[] = $cek_program_target_satuan_rp_realisasi->target_rp_renja;
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }

                                        // Kolom 7 End

                                        // Kolom 8 - 11 Start
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun)->first();

                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $i_tw = 1;
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                if($cek_program_tw_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format($cek_program_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                    if($i_tw == 1)
                                                    {
                                                        $kolom8ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 2)
                                                    {
                                                        $kolom9ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 3)
                                                    {
                                                        $kolom10ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    if($i_tw == 4)
                                                    {
                                                        $kolom11ProgramRp[] = $cek_program_tw_realisasi->realisasi_rp;
                                                    }
                                                    $i_tw++;
                                                } else {
                                                    $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                            }
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }
                                        // Kolom 8 - 11 End

                                        // Kolom 12 Start
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $program_tw_realisasi_realisasi = [];
                                            $program_tw_realisasi_realisasi_rp = [];
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                $program_tw_realisasi_realisasi[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi : 0;
                                                $program_tw_realisasi_realisasi_rp[] = $cek_program_tw_realisasi?$cek_program_tw_realisasi->realisasi_rp : 0;
                                            }
                                            $e_81 .= '<td>'.array_sum($program_tw_realisasi_realisasi).'/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format(array_sum($program_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                            $kolom12ProgramRp[] = array_sum($program_tw_realisasi_realisasi_rp);
                                        } else {
                                            $e_81 .= '<td>0/'.$program_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. 0,00</td>';
                                        }
                                        // Kolom 12 Start

                                        // Kolom 13 Start
                                        $cek_program_target_satuan_rp_realisasi_kolom_13_6 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', $tahun - 1)->first();
                                        $program_realisasi_kolom_13_6 = [];
                                        $program_realisasi_rp_kolom_13_6 = [];
                                        if($cek_program_target_satuan_rp_realisasi_kolom_13_6)
                                        {
                                            $cek_program_tw_realisasi_kolom_13_6 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                            if($cek_program_tw_realisasi_kolom_13_6)
                                            {
                                                $program_tw_realisasies_kolom_13_6 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                foreach ($program_tw_realisasies_kolom_13_6 as $program_tw_realisasi_kolom_13_6) {
                                                    $program_realisasi_kolom_13_6[] = $program_tw_realisasi_kolom_13_6->realisasi;
                                                    $program_realisasi_rp_kolom_13_6[] = $program_tw_realisasi_kolom_13_6->realisasi_rp;
                                                }
                                            } else {
                                                $program_realisasi_kolom_13_6[] = 0;
                                                $program_realisasi_rp_kolom_13_6[] = 0;
                                            }
                                        } else {
                                            $program_realisasi_kolom_13_6[] = 0;
                                            $program_realisasi_rp_kolom_13_6[] = 0;
                                        }

                                        $program_tw_realisasi_realisasi_kolom_13_12 = [];
                                        $program_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            foreach ($tws as $tw) {
                                                $cek_program_tw_realisasi_kolom_13_12 = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id',$cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                $program_tw_realisasi_realisasi_kolom_13_12[] = $cek_program_tw_realisasi_kolom_13_12?$cek_program_tw_realisasi_kolom_13_12->realisasi : 0;
                                                $program_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_program_tw_realisasi_kolom_13_12?$cek_program_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                            }
                                        } else {
                                            $program_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                            $program_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                        }
                                        $realisasi_kolom_13 = array_sum($program_realisasi_kolom_13_6) + array_sum($program_tw_realisasi_realisasi_kolom_13_12);
                                        $realisasi_rp_kolom_13 = array_sum($program_realisasi_rp_kolom_13_6) + array_sum($program_tw_realisasi_realisasi_rp_kolom_13_12);
                                        $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$program_indikator_kinerja->satuan.'</td>';
                                        $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'</td>';
                                        // Kolom 13 End
                                        // Kolom 14 Start
                                        $program_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                        foreach ($tahuns as $item) {
                                            $cek_program_target_satuan_rp_realisasi_kolom_14_5 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                    $q->where('id', $program_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $item)->first();

                                            $program_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_program_target_satuan_rp_realisasi_kolom_14_5 ? $cek_program_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                        }

                                        $last_program_target_satuan_rp_realisasi_kolom_14_5 = ProgramTargetSatuanRpRealisasi::whereHas('opd_program_indikator_kinerja', function($q) use ($program_indikator_kinerja, $opd_id){
                                            $q->where('opd_id', $opd_id);
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($program_indikator_kinerja){
                                                $q->where('id', $program_indikator_kinerja->id);
                                            });
                                        })->where('tahun', end($tahuns))->first();
                                        if($last_program_target_satuan_rp_realisasi_kolom_14_5)
                                        {
                                            if($last_program_target_satuan_rp_realisasi_kolom_14_5->target)
                                            {
                                                $target_kolom_14 = ($realisasi_kolom_13 / $last_program_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                            } else {
                                                $target_kolom_14 = 0;
                                            }
                                        } else {
                                            $target_kolom_14 = 0;
                                        }

                                        if(array_sum($program_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                        {
                                            $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($program_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                        } else {
                                            $target_rp_kolom_14 = 0;
                                        }

                                        $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                        $e_81 .= '<td>'.number_format($target_rp_kolom_14, 2, ',', '.').'</td>';
                                        $kolom14ProgramK[] = $target_kolom_14;
                                        $kolom14ProgramRp[] = $target_rp_kolom_14;
                                        // Kolom 14 End
                                        $e_81 .= '<td>'.$opd->nama.'</td>';
                                    $e_81 .= '</tr>';
                                }
                                $b++;
                            }

                        $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                $q->where('opd_id', $opd_id);
                            });
                        })->get();
                        $kegiatans = [];

                        foreach ($get_kegiatans as $get_kegiatan)
                        {
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
                            $e_81 .= '<tr>';
                                $e_81 .= '<td>kg.'.$a.'</td>';
                                $e_81 .= '<td></td>';
                                $e_81 .= '<td style="text-align:left">'.$kegiatan['deskripsi'].'</td>';

                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                    $q->where('opd_id', $opd_id);
                                })->get();
                                $c = 1;
                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            // Kolom 4 Start
                                            $e_81 .= '<td style="text-align:left">'.$kegiatan_indikator_kinerja->deskripsi.' </td>';
                                            // Kolom 4 End

                                            // Kolom 5 Start
                                            $kegiatan_target_satuan_rp_realisasi_target = [];;
                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();
                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                            }
                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', end($tahuns))->first();
                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $e_81 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            }
                                            $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End

                                            // Kolom 6 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun - 1)->first();

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
                                                    $e_81 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                                }
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0, 00</td>';
                                            }
                                            // Kolom 6 End

                                            // Kolom 7 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();

                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $e_81 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp_renja, 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }

                                            // Kolom 7 End

                                            // Kolom 8 - 11 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();

                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $e_81 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. 0,00</td>';
                                                    }
                                                }
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }
                                            // Kolom 8 - 11 End

                                            // Kolom 12 Start
                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $kegiatan_tw_realisasi_realisasi = [];
                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                }
                                                $e_81 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }
                                            // Kolom 12 Start

                                            // Kolom 13 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun - 1)->first();
                                            $kegiatan_realisasi_kolom_13_6 = [];
                                            $kegiatan_realisasi_rp_kolom_13_6 = [];
                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6)
                                            {
                                                $cek_kegiatan_tw_realisasi_kolom_13_6 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                                if($cek_kegiatan_tw_realisasi_kolom_13_6)
                                                {
                                                    $kegiatan_tw_realisasies_kolom_13_6 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                    foreach ($kegiatan_tw_realisasies_kolom_13_6 as $kegiatan_tw_realisasi_kolom_13_6) {
                                                        $kegiatan_realisasi_kolom_13_6[] = $kegiatan_tw_realisasi_kolom_13_6->realisasi;
                                                        $kegiatan_realisasi_rp_kolom_13_6[] = $kegiatan_tw_realisasi_kolom_13_6->realisasi_rp;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_kolom_13_6[] = 0;
                                                    $kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                }
                                            } else {
                                                $kegiatan_realisasi_kolom_13_6[] = 0;
                                                $kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                            }

                                            $kegiatan_tw_realisasi_realisasi_kolom_13_12 = [];
                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi_kolom_13_12 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $kegiatan_tw_realisasi_realisasi_kolom_13_12[] = $cek_kegiatan_tw_realisasi_kolom_13_12?$cek_kegiatan_tw_realisasi_kolom_13_12->realisasi : 0;
                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_kegiatan_tw_realisasi_kolom_13_12?$cek_kegiatan_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                                }
                                            } else {
                                                $kegiatan_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                            }
                                            $realisasi_kolom_13 = array_sum($kegiatan_realisasi_kolom_13_6) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_13_12);
                                            $realisasi_rp_kolom_13 = array_sum($kegiatan_realisasi_rp_kolom_13_6) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_13_12);
                                            $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'</td>';
                                            // Kolom 13 End
                                            // Kolom 14 Start
                                            $kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                            foreach ($tahuns as $item) {
                                                $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5 ? $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                            }

                                            $last_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', end($tahuns))->first();
                                            if($last_kegiatan_target_satuan_rp_realisasi_kolom_14_5)
                                            {
                                                if($last_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target)
                                                {
                                                    $target_kolom_14 = ($realisasi_kolom_13 / $last_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                                } else {
                                                    $target_kolom_14 = 0;
                                                }
                                            } else {
                                                $target_kolom_14 = 0;
                                            }

                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                            {
                                                $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                            } else {
                                                $target_rp_kolom_14 = 0;
                                            }

                                            $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                            $e_81 .= '<td>'.number_format($target_rp_kolom_14, 2, ',', '.').'</td>';
                                            // Kolom 14 End
                                            $e_81 .= '<td>'.$opd->nama.'</td>';
                                        $e_81 .= '</tr>';
                                    } else {
                                        $e_81 .= '<tr>';
                                            $e_81 .= '<td></td>';
                                            $e_81 .= '<td></td>';
                                            $e_81 .= '<td></td>';
                                            // Kolom 4 Start
                                            $e_81 .= '<td style="text-align:left">'.$kegiatan_indikator_kinerja->deskripsi.' </td>';
                                            // Kolom 4 End

                                            // Kolom 5 Start
                                            $kegiatan_target_satuan_rp_realisasi_target = [];;
                                            $kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                            foreach ($tahuns as $item) {
                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();
                                                $kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_kegiatan_target_satuan_rp_realisasi ? $cek_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                            }
                                            $last_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', end($tahuns))->first();
                                            if($last_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $e_81 .= '<td>'.$last_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            }
                                            $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                            // Kolom 5 End

                                            // Kolom 6 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun - 1)->first();

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
                                                    $e_81 .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0, 00</td>';
                                                }
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0, 00</td>';
                                            }
                                            // Kolom 6 End

                                            // Kolom 7 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();

                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $e_81 .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp_renja, 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }

                                            // Kolom 7 End

                                            // Kolom 8 - 11 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun)->first();

                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    if($cek_kegiatan_tw_realisasi)
                                                    {
                                                        $e_81 .= '<td>'.$cek_kegiatan_tw_realisasi->realisasi.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. '.number_format($cek_kegiatan_tw_realisasi->realisasi_rp, 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. 0,00</td>';
                                                    }
                                                }
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }
                                            // Kolom 8 - 11 End

                                            // Kolom 12 Start
                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $kegiatan_tw_realisasi_realisasi = [];
                                                $kegiatan_tw_realisasi_realisasi_rp = [];
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $kegiatan_tw_realisasi_realisasi[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi : 0;
                                                    $kegiatan_tw_realisasi_realisasi_rp[] = $cek_kegiatan_tw_realisasi?$cek_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                }
                                                $e_81 .= '<td>'.array_sum($kegiatan_tw_realisasi_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                            } else {
                                                $e_81 .= '<td>0/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. 0,00</td>';
                                            }
                                            // Kolom 12 Start

                                            // Kolom 13 Start
                                            $cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', $tahun - 1)->first();
                                            $kegiatan_realisasi_kolom_13_6 = [];
                                            $kegiatan_realisasi_rp_kolom_13_6 = [];
                                            if($cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6)
                                            {
                                                $cek_kegiatan_tw_realisasi_kolom_13_6 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                                if($cek_kegiatan_tw_realisasi_kolom_13_6)
                                                {
                                                    $kegiatan_tw_realisasies_kolom_13_6 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                    foreach ($kegiatan_tw_realisasies_kolom_13_6 as $kegiatan_tw_realisasi_kolom_13_6) {
                                                        $kegiatan_realisasi_kolom_13_6[] = $kegiatan_tw_realisasi_kolom_13_6->realisasi;
                                                        $kegiatan_realisasi_rp_kolom_13_6[] = $kegiatan_tw_realisasi_kolom_13_6->realisasi_rp;
                                                    }
                                                } else {
                                                    $kegiatan_realisasi_kolom_13_6[] = 0;
                                                    $kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                }
                                            } else {
                                                $kegiatan_realisasi_kolom_13_6[] = 0;
                                                $kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                            }

                                            $kegiatan_tw_realisasi_realisasi_kolom_13_12 = [];
                                            $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                            {
                                                foreach ($tws as $tw) {
                                                    $cek_kegiatan_tw_realisasi_kolom_13_12 = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id',$cek_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                    $kegiatan_tw_realisasi_realisasi_kolom_13_12[] = $cek_kegiatan_tw_realisasi_kolom_13_12?$cek_kegiatan_tw_realisasi_kolom_13_12->realisasi : 0;
                                                    $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_kegiatan_tw_realisasi_kolom_13_12?$cek_kegiatan_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                                }
                                            } else {
                                                $kegiatan_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                                $kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                            }
                                            $realisasi_kolom_13 = array_sum($kegiatan_realisasi_kolom_13_6) + array_sum($kegiatan_tw_realisasi_realisasi_kolom_13_12);
                                            $realisasi_rp_kolom_13 = array_sum($kegiatan_realisasi_rp_kolom_13_6) + array_sum($kegiatan_tw_realisasi_realisasi_rp_kolom_13_12);
                                            $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                            $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'</td>';
                                            // Kolom 13 End
                                            // Kolom 14 Start
                                            $kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                            foreach ($tahuns as $item) {
                                                $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                        $q->where('id', $kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $item)->first();

                                                $kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5 ? $cek_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                            }

                                            $last_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                                $q->where('opd_id', $opd_id);
                                                $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                    $q->where('id', $kegiatan_indikator_kinerja->id);
                                                });
                                            })->where('tahun', end($tahuns))->first();
                                            if($last_kegiatan_target_satuan_rp_realisasi_kolom_14_5)
                                            {
                                                if($last_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target)
                                                {
                                                    $target_kolom_14 = ($realisasi_kolom_13 / $last_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                                } else {
                                                    $target_kolom_14 = 0;
                                                }
                                            } else {
                                                $target_kolom_14 = 0;
                                            }

                                            if(array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                            {
                                                $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                            } else {
                                                $target_rp_kolom_14 = 0;
                                            }

                                            $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                            $e_81 .= '<td>'.number_format($target_rp_kolom_14, 2, ',', '.').'</td>';
                                            // Kolom 14 End
                                            $e_81 .= '<td>'.$opd->nama.'</td>';
                                        $e_81 .= '</tr>';
                                    }
                                    $c++;
                                }

                            $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan['id'])->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                    $q->where('opd_id', $opd_id);
                                });
                            })->get();

                            $sub_kegiatans = [];

                            foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)
                                                        ->latest()->first();
                                if($cek_perubahan_sub_kegiatan)
                                {
                                    $sub_kegiatans[] = [
                                        'id' => $cek_perubahan_sub_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_sub_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi,
                                        'tahun_perubahan' => $cek_perubahan_sub_kegiatan->tahun_perubahan
                                    ];
                                } else {
                                    $sub_kegiatans[] = [
                                        'id' => $get_sub_kegiatan->id,
                                        'kode' => $get_sub_kegiatan->kode,
                                        'deskripsi' => $get_sub_kegiatan->deskripsi,
                                        'tahun_perubahan' => $get_sub_kegiatan->tahun_perubahan
                                    ];
                                }
                            }

                            foreach ($sub_kegiatans as $sub_kegiatan) {
                                $e_81 .= '<tr>';
                                    $e_81 .= '<td>skg.'.$a.'</td>';
                                    $e_81 .= '<td></td>';
                                    $e_81 .= '<td style="text-align:left">'.$sub_kegiatan['deskripsi'].'</td>';
                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                        $q->where('opd_id', $opd_id);
                                    })->get();
                                    $d = 1;
                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                        if ($d == 1) {
                                                // Kolom 4 Start
                                                $e_81 .= '<td style="text-align:left">'.$sub_kegiatan_indikator_kinerja->deskripsi.' </td>';
                                                // Kolom 4 End

                                                // Kolom 5 Start
                                                $sub_kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                        $q->where('opd_id', $opd_id);
                                                        $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                            $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();
                                                    $sub_kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_sub_kegiatan_target_satuan_rp_realisasi ? $cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                }
                                                $last_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', end($tahuns))->first();
                                                if($last_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$last_sub_kegiatan_target_satuan_rp_realisasi->target.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                }
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End

                                                // Kolom 6 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun - 1)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $sub_kegiatan_realisasi = [];
                                                    $sub_kegiatan_realisasi_rp = [];
                                                    if($cek_sub_kegiatan_tw_realisasi)
                                                    {
                                                        $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                            $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                            $sub_kegiatan_realisasi_rp[] = $sub_kegiatan_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td>'.array_sum($sub_kegiatan_realisasi).'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. 0,00</td>';
                                                    }
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 6 End

                                                // Kolom 7 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }

                                                // Kolom 7 End

                                                // Kolom 8 - 11 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                        {
                                                            $e_81 .= '<td>'.$cek_sub_kegiatan_tw_realisasi->realisasi.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_81 .= '<td>Rp. '.number_format($cek_sub_kegiatan_tw_realisasi->realiasi_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_81 .= '<td>Rp. 0,00</td>';
                                                        }
                                                    }
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 8 - 11 End

                                                // Kolom 12 Start
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $sub_kegiatan_tw_realisasi_realisasi = [];
                                                    $sub_kegiatan_tw_realisasi_realisasi_rp = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $sub_kegiatan_tw_realisasi_realisasi[] = $cek_sub_kegiatan_tw_realisasi?$cek_sub_kegiatan_tw_realisasi->realisasi : 0;
                                                        $sub_kegiatan_tw_realisasi_realisasi_rp[] = $cek_sub_kegiatan_tw_realisasi?$cek_sub_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                    }
                                                    $e_81 .= '<td>'.array_sum($sub_kegiatan_tw_realisasi_realisasi).'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 12 Start

                                                // Kolom 13 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun - 1)->first();
                                                $sub_kegiatan_realisasi_kolom_13_6 = [];
                                                $sub_kegiatan_realisasi_rp_kolom_13_6 = [];
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6)
                                                {
                                                    $cek_sub_kegiatan_tw_realisasi_kolom_13_6 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                                    if($cek_sub_kegiatan_tw_realisasi_kolom_13_6)
                                                    {
                                                        $sub_kegiatan_tw_realisasies_kolom_13_6 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                        foreach ($sub_kegiatan_tw_realisasies_kolom_13_6 as $sub_kegiatan_tw_realisasi_kolom_13_6) {
                                                            $sub_kegiatan_realisasi_kolom_13_6[] = $sub_kegiatan_tw_realisasi_kolom_13_6->realisasi;
                                                            $sub_kegiatan_realisasi_rp_kolom_13_6[] = $sub_kegiatan_tw_realisasi_kolom_13_6->realisasi_rp;
                                                        }
                                                    } else {
                                                        $sub_kegiatan_realisasi_kolom_13_6[] = 0;
                                                        $sub_kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                    }
                                                } else {
                                                    $sub_kegiatan_realisasi_kolom_13_6[] = 0;
                                                    $sub_kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                }

                                                $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12 = [];
                                                $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi_kolom_13_12 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12[] = $cek_sub_kegiatan_tw_realisasi_kolom_13_12?$cek_sub_kegiatan_tw_realisasi_kolom_13_12->realisasi : 0;
                                                        $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_sub_kegiatan_tw_realisasi_kolom_13_12?$cek_sub_kegiatan_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                                    }
                                                } else {
                                                    $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                                    $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                                }
                                                $realisasi_kolom_13 = array_sum($sub_kegiatan_realisasi_kolom_13_6) + array_sum($sub_kegiatan_tw_realisasi_realisasi_kolom_13_12);
                                                $realisasi_rp_kolom_13 = array_sum($sub_kegiatan_realisasi_rp_kolom_13_6) + array_sum($sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12);
                                                $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'.</td>';
                                                // Kolom 13 End
                                                // Kolom 14 Start
                                                $sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                        $q->where('opd_id', $opd_id);
                                                        $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                            $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 ? $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                                }

                                                $last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', end($tahuns))->first();
                                                if($last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5)
                                                {
                                                    if($last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target)
                                                    {
                                                        $target_kolom_14 = ($realisasi_kolom_13 / $last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                                    } else {
                                                        $target_kolom_14 = 0;
                                                    }
                                                } else {
                                                    $target_kolom_14 = 0;
                                                }
                                                if(array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                                {
                                                    $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                                } else {
                                                    $target_rp_kolom_14 = 0;
                                                }

                                                $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                                $e_81 .= '<td>'.$target_rp_kolom_14.'</td>';
                                                // Kolom 14 End
                                                $e_81 .= '<td>'.$opd->nama.'</td>';
                                            $e_81 .='</tr>';
                                        } else {
                                            $e_81 .= '<tr>';
                                                $e_81 .= '<td></td>';
                                                $e_81 .= '<td></td>';
                                                $e_81 .= '<td></td>';
                                                // Kolom 4 Start
                                                $e_81 .= '<td style="text-align:left">'.$sub_kegiatan_indikator_kinerja->deskripsi.' </td>';
                                                // Kolom 4 End

                                                // Kolom 5 Start
                                                $sub_kegiatan_target_satuan_rp_realisasi_target_rp = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                        $q->where('opd_id', $opd_id);
                                                        $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                            $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();
                                                    $sub_kegiatan_target_satuan_rp_realisasi_target_rp[] = $cek_sub_kegiatan_target_satuan_rp_realisasi ? $cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp : 0;
                                                }
                                                $last_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', end($tahuns))->first();
                                                if($last_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$last_sub_kegiatan_target_satuan_rp_realisasi->target.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                }
                                                $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp), 2, ',', '.').'</td>';
                                                // Kolom 5 End

                                                // Kolom 6 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun - 1)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                    $sub_kegiatan_realisasi = [];
                                                    $sub_kegiatan_realisasi_rp = [];
                                                    if($cek_sub_kegiatan_tw_realisasi)
                                                    {
                                                        $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                        foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                            $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                            $sub_kegiatan_realisasi_rp[] = $sub_kegiatan_tw_realisasi->realisasi_rp;
                                                        }
                                                        $e_81 .= '<td>'.array_sum($sub_kegiatan_realisasi).'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_realisasi_rp), 2, ',', '.').'</td>';
                                                    } else {
                                                        $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                        $e_81 .= '<td>Rp. 0,00</td>';
                                                    }
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 6 End

                                                // Kolom 7 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $e_81 .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }

                                                // Kolom 7 End

                                                // Kolom 8 - 11 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun)->first();

                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                        {
                                                            $e_81 .= '<td>'.$cek_sub_kegiatan_tw_realisasi->realisasi.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_81 .= '<td>Rp. '.number_format($cek_sub_kegiatan_tw_realisasi->realiasi_rp, 2, ',', '.').'</td>';
                                                        } else {
                                                            $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                            $e_81 .= '<td>Rp. 0,00</td>';
                                                        }
                                                    }
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 8 - 11 End

                                                // Kolom 12 Start
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    $sub_kegiatan_tw_realisasi_realisasi = [];
                                                    $sub_kegiatan_tw_realisasi_realisasi_rp = [];
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $sub_kegiatan_tw_realisasi_realisasi[] = $cek_sub_kegiatan_tw_realisasi?$cek_sub_kegiatan_tw_realisasi->realisasi : 0;
                                                        $sub_kegiatan_tw_realisasi_realisasi_rp[] = $cek_sub_kegiatan_tw_realisasi?$cek_sub_kegiatan_tw_realisasi->realisasi_rp : 0;
                                                    }
                                                    $e_81 .= '<td>'.array_sum($sub_kegiatan_tw_realisasi_realisasi).'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. '.number_format(array_sum($sub_kegiatan_tw_realisasi_realisasi_rp), 2, ',', '.').'</td>';
                                                } else {
                                                    $e_81 .= '<td>0/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $e_81 .= '<td>Rp. 0,00</td>';
                                                }
                                                // Kolom 12 Start

                                                // Kolom 13 Start
                                                $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', $tahun - 1)->first();
                                                $sub_kegiatan_realisasi_kolom_13_6 = [];
                                                $sub_kegiatan_realisasi_rp_kolom_13_6 = [];
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6)
                                                {
                                                    $cek_sub_kegiatan_tw_realisasi_kolom_13_6 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->first();

                                                    if($cek_sub_kegiatan_tw_realisasi_kolom_13_6)
                                                    {
                                                        $sub_kegiatan_tw_realisasies_kolom_13_6 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_13_6->id)->get();
                                                        foreach ($sub_kegiatan_tw_realisasies_kolom_13_6 as $sub_kegiatan_tw_realisasi_kolom_13_6) {
                                                            $sub_kegiatan_realisasi_kolom_13_6[] = $sub_kegiatan_tw_realisasi_kolom_13_6->realisasi;
                                                            $sub_kegiatan_realisasi_rp_kolom_13_6[] = $sub_kegiatan_tw_realisasi_kolom_13_6->realisasi_rp;
                                                        }
                                                    } else {
                                                        $sub_kegiatan_realisasi_kolom_13_6[] = 0;
                                                        $sub_kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                    }
                                                } else {
                                                    $sub_kegiatan_realisasi_kolom_13_6[] = 0;
                                                    $sub_kegiatan_realisasi_rp_kolom_13_6[] = 0;
                                                }

                                                $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12 = [];
                                                $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12 = [];
                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                {
                                                    foreach ($tws as $tw) {
                                                        $cek_sub_kegiatan_tw_realisasi_kolom_13_12 = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id',$cek_sub_kegiatan_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                                        $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12[] = $cek_sub_kegiatan_tw_realisasi_kolom_13_12?$cek_sub_kegiatan_tw_realisasi_kolom_13_12->realisasi : 0;
                                                        $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = $cek_sub_kegiatan_tw_realisasi_kolom_13_12?$cek_sub_kegiatan_tw_realisasi_kolom_13_12->realisasi_rp : 0;
                                                    }
                                                } else {
                                                    $sub_kegiatan_tw_realisasi_realisasi_kolom_13_12[] = 0;
                                                    $sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12[] = 0;
                                                }
                                                $realisasi_kolom_13 = array_sum($sub_kegiatan_realisasi_kolom_13_6) + array_sum($sub_kegiatan_tw_realisasi_realisasi_kolom_13_12);
                                                $realisasi_rp_kolom_13 = array_sum($sub_kegiatan_realisasi_rp_kolom_13_6) + array_sum($sub_kegiatan_tw_realisasi_realisasi_rp_kolom_13_12);
                                                $e_81 .= '<td>'.$realisasi_kolom_13.'/'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                $e_81 .= '<td>Rp. '.number_format($realisasi_rp_kolom_13, 2, ',', '.').'.</td>';
                                                // Kolom 13 End
                                                // Kolom 14 Start
                                                $sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5 = [];

                                                foreach ($tahuns as $item) {
                                                    $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                        $q->where('opd_id', $opd_id);
                                                        $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                            $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                        });
                                                    })->where('tahun', $item)->first();

                                                    $sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5[] = $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 ? $cek_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target_rp : 0;
                                                }

                                                $last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5 = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                        $q->where('id', $sub_kegiatan_indikator_kinerja->id);
                                                    });
                                                })->where('tahun', end($tahuns))->first();
                                                if($last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5)
                                                {
                                                    if($last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target)
                                                    {
                                                        $target_kolom_14 = ($realisasi_kolom_13 / $last_sub_kegiatan_target_satuan_rp_realisasi_kolom_14_5->target) * 100;
                                                    } else {
                                                        $target_kolom_14 = 0;
                                                    }
                                                } else {
                                                    $target_kolom_14 = 0;
                                                }
                                                if(array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5) != 0)
                                                {
                                                    $target_rp_kolom_14 = ($realisasi_rp_kolom_13 / array_sum($sub_kegiatan_target_satuan_rp_realisasi_target_rp_kolom_14_5)) * 100;
                                                } else {
                                                    $target_rp_kolom_14 = 0;
                                                }

                                                $e_81 .= '<td>'.number_format($target_kolom_14, 2).'</td>';
                                                $e_81 .= '<td>'.$target_rp_kolom_14.'</td>';
                                                // Kolom 14 End
                                                $e_81 .= '<td>'.$opd->nama.'</td>';
                                            $e_81 .='</tr>';
                                        }
                                        $d++;
                                    }
                            }
                        }
                    }
                    $a++;
                }
            }
        }

        $e_81 .= '<tr><td colspan="25"></td></tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td class="font-weight-bold" align="left" colspan="5">Jumlah Anggaran dan Realisasi dari Seluruh Program</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom7ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom8ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom9ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom10ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom11ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td>Rp. '.number_format(array_sum($kolom12ProgramRp), 2, ',', '.').'</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
        $e_81 .= '</tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td class="font-weight-bold" align="left" colspan="5">Rata - rata capaian kinerja (%)</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            if(count($kolom14ProgramK))
            {
                $e_81 .= '<td>'.number_format(array_sum($kolom14ProgramK)/count($kolom14ProgramK), 2).'</td>';
            } else {
                $e_81 .= '<td>0</td>';
            }
            if(count($kolom14ProgramRp))
            {
                $e_81 .= '<td>'.number_format(array_sum($kolom14ProgramRp)/count($kolom14ProgramRp), 2).'</td>';
            } else {
                $e_81 .= '<td>0</td>';
            }
            $e_81 .= '<td></td>';
        $e_81 .= '</tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td class="font-weight-bold" align="left" colspan="5">Predikat Kinerja</td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            $e_81 .= '<td></td>';
            if(count($kolom14ProgramK))
            {
                $averageCapaianKinerjaKolom14K = array_sum($kolom14ProgramK)/count($kolom14ProgramK);
            } else {
                $averageCapaianKinerjaKolom14K = 0;
            }
            if(count($kolom14ProgramRp))
            {
                $averageCapaianKinerjaKolom14Rp = array_sum($kolom14ProgramRp)/count($kolom14ProgramRp);
            } else {
                $averageCapaianKinerjaKolom14Rp = 0;
            }

            $kriteriaKolom14K = '';
            $kriteriaKolom14Rp = '';
            foreach ($getSkalas as $getSkala) {
                if($averageCapaianKinerjaKolom14K >= $getSkala->terkecil &&  $averageCapaianKinerjaKolom14K <= $getSkala->terbesar)
                {
                    $kriteriaKolom14K = $getSkala->kriteria;
                }

                if($averageCapaianKinerjaKolom14K > 100)
                {
                    $kriteriaKolom14K = 'Sangat Tinggi';
                }
            }
            foreach ($getSkalas as $getSkala) {
                if($averageCapaianKinerjaKolom14Rp >= $getSkala->terkecil && $averageCapaianKinerjaKolom14Rp <= $getSkala->terbesar)
                {
                    $kriteriaKolom14Rp = $getSkala->kriteria;
                }

                if($averageCapaianKinerjaKolom14Rp > 100)
                {
                    $kriteriaKolom14Rp = 'Sangat Tinggi';
                }
            }
            $e_81 .= '<td>'.$kriteriaKolom14K.'</td>';
            $e_81 .= '<td>'.$kriteriaKolom14Rp.'</td>';
            $e_81 .= '<td></td>';
        $e_81 .= '</tr>';

        $e_81 .= '<tr><td colspan="25"></td></tr>';
        $e_81 .= '<tr><td colspan="25"></td></tr>';
        $e_81 .= '<tr><td colspan="25"></td></tr>';

        $faktorTindakLanjutE81 = FaktorTindakLanjutE81::where('tahun', $tahun)->where('tahun_periode_id', $get_periode->id)->where('opd_id', $opd_id)->first();
        $e_81 .= '<tr>';
            $e_81 .= '<td align="left" colspan="5">Faktor pendorong keberhasilan pencapaian:</td>';
            $e_81 .= '<td align="left" colspan="20">';
                if($faktorTindakLanjutE81){
                    $e_81 .= $faktorTindakLanjutE81->faktor_pendorong;
                }
            $e_81 .='</td>';
        $e_81 .= '</tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td align="left" colspan="5">Faktor penghambat pencapaian kinerja:</td>';
            $e_81 .= '<td align="left" colspan="20">';
                if($faktorTindakLanjutE81){
                    $e_81 .= $faktorTindakLanjutE81->faktor_penghambat;
                }
            $e_81 .='</td>';
        $e_81 .= '</tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td align="left" colspan="5">Tindak lanjut yang diperlukan dalam Triwulan berikutnya:</td>';
            $e_81 .= '<td align="left" colspan="20">';
                if($faktorTindakLanjutE81){
                    $e_81 .= $faktorTindakLanjutE81->tindak_lanjut_triwulan;
                }
            $e_81 .='</td>';
        $e_81 .= '</tr>';
        $e_81 .= '<tr>';
            $e_81 .= '<td align="left" colspan="5">Tindak lanjut yang diperlukan dalam Renja Perangkat Daerah Kabupaten Madiun Berikutnya:</td>';
            $e_81 .= '<td align="left" colspan="20">';
                if($faktorTindakLanjutE81){
                    $e_81 .= $faktorTindakLanjutE81->tindak_lanjut_renja;
                }
            $e_81 .='</td>';
        $e_81 .= '</tr>';

        $pdf = PDF::loadView('admin.laporan.e-81', [
            'e_81' => $e_81,
            'tahun' => $tahun
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan E-81.pdf');
    }

    public function e_81_ekspor_excel($opd_id, $tahun)
    {
        return Excel::download(new E81Ekspor($opd_id, $tahun), 'Laporan E-81.xlsx');
    }

    public function edit_tindak_lanjut_triwulan(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'edit_tindak_lanjut_triwulan_tahun' => 'required',
            'edit_tindak_lanjut_triwulan_opd_id' => 'required',
            'edit_tindak_lanjut_triwulan_tahun_periode_id' => 'required',
            'edit_tindak_lanjut_triwulan_text' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->back();
        }

        $cekLastTindakLanjutTriwulan = FaktorTindakLanjutE81::where('tahun_periode_id', $request->edit_tindak_lanjut_triwulan_tahun_periode_id)
                                        ->where('tahun', $request->edit_tindak_lanjut_triwulan_tahun)
                                        ->where('opd_id', $request->edit_tindak_lanjut_triwulan_opd_id)
                                        ->first();
        if($cekLastTindakLanjutTriwulan)
        {
            $tindak_lanjut_triwulan = FaktorTindakLanjutE81::find($cekLastTindakLanjutTriwulan->id);
        } else {
            $tindak_lanjut_triwulan = new FaktorTindakLanjutE81;
            $tindak_lanjut_triwulan->tahun_periode_id = $request->edit_tindak_lanjut_triwulan_tahun_periode_id;
            $tindak_lanjut_triwulan->tahun = $request->edit_tindak_lanjut_triwulan_tahun;
            $tindak_lanjut_triwulan->opd_id = $request->edit_tindak_lanjut_triwulan_opd_id;
        }

        $tindak_lanjut_triwulan->tindak_lanjut_triwulan = $request->edit_tindak_lanjut_triwulan_text;
        $tindak_lanjut_triwulan->save();

        Alert::success('Berhasil', 'Berhasil merubah Tindak Lanjut Triwulan');
        return redirect()->back();
    }

    public function edit_tindak_lanjut_renja(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'edit_tindak_lanjut_renja_tahun' => 'required',
            'edit_tindak_lanjut_renja_opd_id' => 'required',
            'edit_tindak_lanjut_renja_tahun_periode_id' => 'required',
            'edit_tindak_lanjut_renja_text' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->back();
        }

        $cekLastTindakLanjutRenja = FaktorTindakLanjutE81::where('tahun_periode_id', $request->edit_tindak_lanjut_renja_tahun_periode_id)
                                        ->where('tahun', $request->edit_tindak_lanjut_renja_tahun)
                                        ->where('opd_id', $request->edit_tindak_lanjut_renja_opd_id)
                                        ->first();
        if($cekLastTindakLanjutRenja)
        {
            $tindak_lanjut_renja = FaktorTindakLanjutE81::find($cekLastTindakLanjutRenja->id);
        } else {
            $tindak_lanjut_renja = new FaktorTindakLanjutE81;
            $tindak_lanjut_renja->tahun_periode_id = $request->edit_tindak_lanjut_renja_tahun_periode_id;
            $tindak_lanjut_renja->tahun = $request->edit_tindak_lanjut_renja_tahun;
            $tindak_lanjut_renja->opd_id = $request->edit_tindak_lanjut_renja_opd_id;
        }

        $tindak_lanjut_renja->tindak_lanjut_renja = $request->edit_tindak_lanjut_renja_text;
        $tindak_lanjut_renja->save();

        Alert::success('Berhasil', 'Berhasil merubah Tindak Lanjut renja');
        return redirect()->back();
    }
}
