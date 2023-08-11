<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use App\Exports\Tc14Ekspor;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\SubKegiatanTwRealisasi;

class Tc14Controller extends Controller
{
    public function tc_14()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $new_get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $new_tahun_awal = $new_get_periode->tahun_awal;
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
                            $q->whereHas('program_rpjmd');
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
                            $q->whereHas('program_rpjmd');
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
                            'id' => $get_tujuan->tujuan_id,
                            'kode' => $get_tujuan->kode,
                            'deskripsi' => $get_tujuan->deskripsi
                        ];
                    }
                }
                // Tujuan
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
                                    $tc_14 .= '<td style="text-align:left">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    $tc_14 .= '<td style="text-align:left">'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                    $indikator_b = 0;
                                    $len_b = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                        if($cek_tujuan_target_satuan_rp_realisasi)
                                        {
                                            $tc_14 .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                            $tc_14 .= '<td></td>';
                                            if($indikator_b == $len_b -1 )
                                            {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                            }
                                        } else {
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            if($indikator_b == $len_b -1 )
                                            {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                            }
                                        }
                                        $indikator_b++;
                                    }
                                    $tc_14 .= '<td></td>';
                                $tc_14 .='</tr>';
                            } else {
                                $tc_14 .= '<tr>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td style="text-align:left">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    $tc_14 .= '<td style="text-align:left">'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                    $indikator_b = 0;
                                    $len_b = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                        if($cek_tujuan_target_satuan_rp_realisasi)
                                        {
                                            $tc_14 .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                            $tc_14 .= '<td></td>';
                                            if($indikator_b == $len_b -1 )
                                            {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                            }
                                        } else {
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            if($indikator_b == $len_b -1 )
                                            {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                            }
                                        }
                                        $indikator_b++;
                                    }
                                    $tc_14 .= '<td></td>';
                                $tc_14 .='</tr>';
                            }
                            $a++;
                        }

                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
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
                                'deskripsi' => $get_sasaran->deskripsi,
                            ];
                        }
                    }

                    // Sasaran

                    foreach ($sasarans as $sasaran) {
                        $tc_14 .= '<tr>';
                            $tc_14 .= '<td>'.$misi['kode'].'</td>';
                            $tc_14 .= '<td>'.$tujuan['kode'].'</td>';
                            $tc_14 .= '<td>'.$sasaran['kode'].'</td>';
                            $tc_14 .= '<td style="text-align:left">'.$sasaran['deskripsi'].'</td>';
                            // Sasaran Indikator Kinerja
                            $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                            $b = 1;
                            foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                if($b == 1)
                                {
                                        $tc_14 .= '<td style="text-align:left">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                        $tc_14 .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $indikator_c = 0;
                                        $len_c = count($tahuns);
                                        foreach ($tahuns as $tahun) {
                                            $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja)->where('tahun', $tahun)->first();
                                            if($cek_sasaran_target_satuan_rp_realisasi)
                                            {
                                                $tc_14 .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                $tc_14 .= '<td></td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_14 .= '<td></td>';
                                                    $tc_14 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_14 .= '<td></td>';
                                                    $tc_14 .= '<td></td>';
                                                }
                                            }
                                            $indikator_c++;
                                        }
                                        $tc_14 .= '<td></td>';
                                    $tc_14 .='</tr>';
                                    $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                                    foreach ($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd) {
                                        $tc_14 .= '<tr>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td style="text-align:left">'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi.'</td>';
                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program_id)->get();
                                            $c = 1;
                                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                                if($c == 1)
                                                {
                                                    $tc_14 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                    $tc_14 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                    $d = 1;
                                                    // Lokasi 2 source
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                                $data_target = [];
                                                                $data_satuan = '';
                                                                $data_target_rp = [];
                                                                foreach ($tahuns as $tahun) {
                                                                    $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun)->first();
                                                                    if($program_target_satuan_rp_realisasi)
                                                                    {
                                                                        $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                        $data_satuan = $program_indikator_kinerja->satuan;
                                                                        $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                        $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                }
                                                                $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_akhir)->first();
                                                                if($kondisi_akhir_tahun)
                                                                {
                                                                    $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                }
                                                                $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_14 .='</tr>';
                                                        } else {
                                                            $tc_14 .= '<tr>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $data_target = [];
                                                                $data_satuan = '';
                                                                $data_target_rp = [];
                                                                foreach ($tahuns as $tahun) {
                                                                    $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun)->first();
                                                                    if($program_target_satuan_rp_realisasi)
                                                                    {
                                                                        $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                        $data_satuan = $program_indikator_kinerja->satuan;
                                                                        $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                        $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                }
                                                                $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_akhir)->first();
                                                                if($kondisi_akhir_tahun)
                                                                {
                                                                    $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                }
                                                                $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_14 .='</tr>';
                                                        }
                                                        $d++;
                                                    }
                                                } else {
                                                    $tc_14 .= '<tr>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                        $tc_14 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                        $d = 1;
                                                        // Lokasi 2 Destinasi
                                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                            if($d == 1)
                                                            {
                                                                    $data_target = [];
                                                                    $data_satuan = '';
                                                                    $data_target_rp = [];
                                                                    foreach ($tahuns as $tahun) {
                                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun)->first();
                                                                        if($program_target_satuan_rp_realisasi)
                                                                        {
                                                                            $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                            $data_satuan = $program_indikator_kinerja->satuan;
                                                                            $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                            $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                        } else {
                                                                            $tc_14 .= '<td></td>';
                                                                            $tc_14 .= '<td></td>';
                                                                        }
                                                                    }
                                                                    $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_akhir)->first();
                                                                    if($kondisi_akhir_tahun)
                                                                    {
                                                                        $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                        $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                $tc_14 .='</tr>';
                                                            } else {
                                                                $tc_14 .= '<tr>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $data_target = [];
                                                                    $data_satuan = '';
                                                                    $data_target_rp = [];
                                                                    foreach ($tahuns as $tahun) {
                                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun)->first();
                                                                        if($program_target_satuan_rp_realisasi)
                                                                        {
                                                                            $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                            $data_satuan = $program_indikator_kinerja->satuan;
                                                                            $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                            $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                        } else {
                                                                            $tc_14 .= '<td></td>';
                                                                            $tc_14 .= '<td></td>';
                                                                        }
                                                                    }
                                                                    $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_akhir)->first();
                                                                    if($kondisi_akhir_tahun)
                                                                    {
                                                                        $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                        $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                $tc_14 .='</tr>';
                                                            }
                                                            $d++;
                                                        }
                                                }
                                                $c++;
                                            }
                                    }
                                } else {
                                    $tc_14 .= '<tr>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td style="text-align:left">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                        $tc_14 .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $indikator_c = 0;
                                        $len_c = count($tahuns);
                                        foreach ($tahuns as $tahun) {
                                            $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja)->where('tahun', $tahun)->first();
                                            if($cek_sasaran_target_satuan_rp_realisasi)
                                            {
                                                $tc_14 .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                $tc_14 .= '<td></td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_14 .= '<td></td>';
                                                    $tc_14 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_14 .= '<td></td>';
                                                    $tc_14 .= '<td></td>';
                                                }
                                            }
                                            $indikator_c++;
                                        }
                                        $tc_14 .= '<td></td>';
                                    $tc_14 .='</tr>';
                                    $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                                    foreach ($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd) {
                                        $tc_14 .= '<tr>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td style="text-align:left">'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi.'</td>';
                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program_id)->get();
                                            $c = 1;
                                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                                if($c == 1)
                                                {
                                                    $tc_14 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                    $tc_14 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                    $d = 1;
                                                    // Lokasi 2 source
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                                $data_target = [];
                                                                $data_satuan = '';
                                                                $data_target_rp = [];
                                                                foreach ($tahuns as $tahun) {
                                                                    $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun)->first();
                                                                    if($program_target_satuan_rp_realisasi)
                                                                    {
                                                                        $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                        $data_satuan = $program_indikator_kinerja->satuan;
                                                                        $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                        $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                }
                                                                $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_akhir)->first();
                                                                if($kondisi_akhir_tahun)
                                                                {
                                                                    $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                }
                                                                $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_14 .='</tr>';
                                                        } else {
                                                            $tc_14 .= '<tr>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $data_target = [];
                                                                $data_satuan = '';
                                                                $data_target_rp = [];
                                                                foreach ($tahuns as $tahun) {
                                                                    $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun)->first();
                                                                    if($program_target_satuan_rp_realisasi)
                                                                    {
                                                                        $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                        $data_satuan = $program_indikator_kinerja->satuan;
                                                                        $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                        $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                }
                                                                $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_akhir)->first();
                                                                if($kondisi_akhir_tahun)
                                                                {
                                                                    $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                }
                                                                $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_14 .='</tr>';
                                                        }
                                                        $d++;
                                                    }
                                                } else {
                                                    $tc_14 .= '<tr>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                        $tc_14 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                        $d = 1;
                                                        // Lokasi 2 source
                                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                            if($d == 1)
                                                            {
                                                                    $data_target = [];
                                                                    $data_satuan = '';
                                                                    $data_target_rp = [];
                                                                    foreach ($tahuns as $tahun) {
                                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun)->first();
                                                                        if($program_target_satuan_rp_realisasi)
                                                                        {
                                                                            $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                            $data_satuan = $program_indikator_kinerja->satuan;
                                                                            $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                            $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                        } else {
                                                                            $tc_14 .= '<td></td>';
                                                                            $tc_14 .= '<td></td>';
                                                                        }
                                                                    }
                                                                    $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_akhir)->first();
                                                                    if($kondisi_akhir_tahun)
                                                                    {
                                                                        $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                        $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                $tc_14 .='</tr>';
                                                            } else {
                                                                $tc_14 .= '<tr>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $data_target = [];
                                                                    $data_satuan = '';
                                                                    $data_target_rp = [];
                                                                    foreach ($tahuns as $tahun) {
                                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun)->first();
                                                                        if($program_target_satuan_rp_realisasi)
                                                                        {
                                                                            $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                            $data_satuan = $program_indikator_kinerja->satuan;
                                                                            $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                            $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                        } else {
                                                                            $tc_14 .= '<td></td>';
                                                                            $tc_14 .= '<td></td>';
                                                                        }
                                                                    }
                                                                    $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_akhir)->first();
                                                                    if($kondisi_akhir_tahun)
                                                                    {
                                                                        $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                        $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                $tc_14 .='</tr>';
                                                            }
                                                            $d++;
                                                        }
                                                }
                                                $c++;
                                            }
                                    }
                                }
                                $b++;
                            }
                    }
                }
            }
        }
        // TC 14 End

        return response()->json(['tc_14' => $tc_14]);
    }

    public function tc_14_ekspor_pdf()
    {
        $new_get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $new_tahun_awal = $new_get_periode->tahun_awal-1;
        $new_jarak_tahun = $new_get_periode->tahun_akhir - $new_tahun_awal;
        $new_tahun_akhir = $new_get_periode->tahun_akhir;
        $new_tahuns = [];
        for ($i=0; $i < $new_jarak_tahun + 1; $i++) {
            $new_tahuns[] = $new_tahun_awal + $i;
        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
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
                            $q->whereHas('program_rpjmd');
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
                            $q->whereHas('program_rpjmd');
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
                            'id' => $get_tujuan->tujuan_id,
                            'kode' => $get_tujuan->kode,
                            'deskripsi' => $get_tujuan->deskripsi
                        ];
                    }
                }
                // Tujuan
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
                                    $tc_14 .= '<td style="text-align:left">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    $tc_14 .= '<td style="text-align:left">'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                    $indikator_b = 0;
                                    $len_b = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                        if($cek_tujuan_target_satuan_rp_realisasi)
                                        {
                                            $tc_14 .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                            $tc_14 .= '<td></td>';
                                            if($indikator_b == $len_b -1 )
                                            {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                            }
                                        } else {
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            if($indikator_b == $len_b -1 )
                                            {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                            }
                                        }
                                        $indikator_b++;
                                    }
                                    $tc_14 .= '<td></td>';
                                $tc_14 .='</tr>';
                            } else {
                                $tc_14 .= '<tr>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td style="text-align:left">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    $tc_14 .= '<td style="text-align:left">'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                    $indikator_b = 0;
                                    $len_b = count($tahuns);
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                        if($cek_tujuan_target_satuan_rp_realisasi)
                                        {
                                            $tc_14 .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'/'.$tujuan_indikator_kinerja->satuan.'</td>';
                                            $tc_14 .= '<td></td>';
                                            if($indikator_b == $len_b -1 )
                                            {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                            }
                                        } else {
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            if($indikator_b == $len_b -1 )
                                            {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                            }
                                        }
                                        $indikator_b++;
                                    }
                                    $tc_14 .= '<td></td>';
                                $tc_14 .='</tr>';
                            }
                            $a++;
                        }

                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
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
                                'deskripsi' => $get_sasaran->deskripsi,
                            ];
                        }
                    }

                    // Sasaran

                    foreach ($sasarans as $sasaran) {
                        $tc_14 .= '<tr>';
                            $tc_14 .= '<td>'.$misi['kode'].'</td>';
                            $tc_14 .= '<td>'.$tujuan['kode'].'</td>';
                            $tc_14 .= '<td>'.$sasaran['kode'].'</td>';
                            $tc_14 .= '<td style="text-align:left">'.$sasaran['deskripsi'].'</td>';
                            // Sasaran Indikator Kinerja
                            $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                            $b = 1;
                            foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                if($b == 1)
                                {
                                        $tc_14 .= '<td style="text-align:left">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                        $tc_14 .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $indikator_c = 0;
                                        $len_c = count($tahuns);
                                        foreach ($tahuns as $tahun) {
                                            $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja)->where('tahun', $tahun)->first();
                                            if($cek_sasaran_target_satuan_rp_realisasi)
                                            {
                                                $tc_14 .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                $tc_14 .= '<td></td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_14 .= '<td></td>';
                                                    $tc_14 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_14 .= '<td></td>';
                                                    $tc_14 .= '<td></td>';
                                                }
                                            }
                                            $indikator_c++;
                                        }
                                        $tc_14 .= '<td></td>';
                                    $tc_14 .='</tr>';
                                    $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                                    foreach ($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd) {
                                        $tc_14 .= '<tr>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td style="text-align:left">'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi.'</td>';
                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program_id)->get();
                                            $c = 1;
                                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                                if($c == 1)
                                                {
                                                    $tc_14 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                    $tc_14 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                    $d = 1;
                                                    // Lokasi 2 source
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                                $data_target = [];
                                                                $data_satuan = '';
                                                                $data_target_rp = [];
                                                                foreach ($tahuns as $tahun) {
                                                                    $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun)->first();
                                                                    if($program_target_satuan_rp_realisasi)
                                                                    {
                                                                        $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                        $data_satuan = $program_indikator_kinerja->satuan;
                                                                        $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                        $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                }
                                                                $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_akhir)->first();
                                                                if($kondisi_akhir_tahun)
                                                                {
                                                                    $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                }
                                                                $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_14 .='</tr>';
                                                        } else {
                                                            $tc_14 .= '<tr>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $data_target = [];
                                                                $data_satuan = '';
                                                                $data_target_rp = [];
                                                                foreach ($tahuns as $tahun) {
                                                                    $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun)->first();
                                                                    if($program_target_satuan_rp_realisasi)
                                                                    {
                                                                        $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                        $data_satuan = $program_indikator_kinerja->satuan;
                                                                        $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                        $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                }
                                                                $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_akhir)->first();
                                                                if($kondisi_akhir_tahun)
                                                                {
                                                                    $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                }
                                                                $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_14 .='</tr>';
                                                        }
                                                        $d++;
                                                    }
                                                } else {
                                                    $tc_14 .= '<tr>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                        $tc_14 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                        $d = 1;
                                                        // Lokasi 2 Destinasi
                                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                            if($d == 1)
                                                            {
                                                                    $data_target = [];
                                                                    $data_satuan = '';
                                                                    $data_target_rp = [];
                                                                    foreach ($tahuns as $tahun) {
                                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun)->first();
                                                                        if($program_target_satuan_rp_realisasi)
                                                                        {
                                                                            $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                            $data_satuan = $program_indikator_kinerja->satuan;
                                                                            $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                            $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                        } else {
                                                                            $tc_14 .= '<td></td>';
                                                                            $tc_14 .= '<td></td>';
                                                                        }
                                                                    }
                                                                    $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_akhir)->first();
                                                                    if($kondisi_akhir_tahun)
                                                                    {
                                                                        $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                        $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                $tc_14 .='</tr>';
                                                            } else {
                                                                $tc_14 .= '<tr>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $data_target = [];
                                                                    $data_satuan = '';
                                                                    $data_target_rp = [];
                                                                    foreach ($tahuns as $tahun) {
                                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun)->first();
                                                                        if($program_target_satuan_rp_realisasi)
                                                                        {
                                                                            $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                            $data_satuan = $program_indikator_kinerja->satuan;
                                                                            $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                            $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                        } else {
                                                                            $tc_14 .= '<td></td>';
                                                                            $tc_14 .= '<td></td>';
                                                                        }
                                                                    }
                                                                    $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_akhir)->first();
                                                                    if($kondisi_akhir_tahun)
                                                                    {
                                                                        $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                        $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                $tc_14 .='</tr>';
                                                            }
                                                            $d++;
                                                        }
                                                }
                                                $c++;
                                            }
                                    }
                                } else {
                                    $tc_14 .= '<tr>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td style="text-align:left">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                        $tc_14 .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                        $indikator_c = 0;
                                        $len_c = count($tahuns);
                                        foreach ($tahuns as $tahun) {
                                            $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja)->where('tahun', $tahun)->first();
                                            if($cek_sasaran_target_satuan_rp_realisasi)
                                            {
                                                $tc_14 .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'/'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                $tc_14 .= '<td></td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_14 .= '<td></td>';
                                                    $tc_14 .= '<td></td>';
                                                }
                                            } else {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                                if($indikator_c == $len_c - 1)
                                                {
                                                    $tc_14 .= '<td></td>';
                                                    $tc_14 .= '<td></td>';
                                                }
                                            }
                                            $indikator_c++;
                                        }
                                        $tc_14 .= '<td></td>';
                                    $tc_14 .='</tr>';
                                    $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                                    foreach ($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd) {
                                        $tc_14 .= '<tr>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td></td>';
                                            $tc_14 .= '<td style="text-align:left">'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi.'</td>';
                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program_id)->get();
                                            $c = 1;
                                            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                                if($c == 1)
                                                {
                                                    $tc_14 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                    $tc_14 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                    $d = 1;
                                                    // Lokasi 2 source
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                                $data_target = [];
                                                                $data_satuan = '';
                                                                $data_target_rp = [];
                                                                foreach ($tahuns as $tahun) {
                                                                    $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun)->first();
                                                                    if($program_target_satuan_rp_realisasi)
                                                                    {
                                                                        $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                        $data_satuan = $program_indikator_kinerja->satuan;
                                                                        $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                        $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                }
                                                                $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_akhir)->first();
                                                                if($kondisi_akhir_tahun)
                                                                {
                                                                    $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                }
                                                                $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_14 .='</tr>';
                                                        } else {
                                                            $tc_14 .= '<tr>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $tc_14 .= '<td></td>';
                                                                $data_target = [];
                                                                $data_satuan = '';
                                                                $data_target_rp = [];
                                                                foreach ($tahuns as $tahun) {
                                                                    $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                            ->where('tahun', $tahun)->first();
                                                                    if($program_target_satuan_rp_realisasi)
                                                                    {
                                                                        $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                        $data_satuan = $program_indikator_kinerja->satuan;
                                                                        $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                        $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                }
                                                                $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_akhir)->first();
                                                                if($kondisi_akhir_tahun)
                                                                {
                                                                    $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                    $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                }
                                                                $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                            $tc_14 .='</tr>';
                                                        }
                                                        $d++;
                                                    }
                                                } else {
                                                    $tc_14 .= '<tr>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td></td>';
                                                        $tc_14 .= '<td style="text-align:left">'.$program_indikator_kinerja->deskripsi.'</td>';
                                                        $tc_14 .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                        $d = 1;
                                                        // Lokasi 2 source
                                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                            if($d == 1)
                                                            {
                                                                    $data_target = [];
                                                                    $data_satuan = '';
                                                                    $data_target_rp = [];
                                                                    foreach ($tahuns as $tahun) {
                                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun)->first();
                                                                        if($program_target_satuan_rp_realisasi)
                                                                        {
                                                                            $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                            $data_satuan = $program_indikator_kinerja->satuan;
                                                                            $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                            $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                        } else {
                                                                            $tc_14 .= '<td></td>';
                                                                            $tc_14 .= '<td></td>';
                                                                        }
                                                                    }
                                                                    $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_akhir)->first();
                                                                    if($kondisi_akhir_tahun)
                                                                    {
                                                                        $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                        $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                $tc_14 .='</tr>';
                                                            } else {
                                                                $tc_14 .= '<tr>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $tc_14 .= '<td></td>';
                                                                    $data_target = [];
                                                                    $data_satuan = '';
                                                                    $data_target_rp = [];
                                                                    foreach ($tahuns as $tahun) {
                                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                                                ->where('tahun', $tahun)->first();
                                                                        if($program_target_satuan_rp_realisasi)
                                                                        {
                                                                            $data_target[] =  $program_target_satuan_rp_realisasi->target;
                                                                            $data_satuan = $program_indikator_kinerja->satuan;
                                                                            $data_target_rp[] = $program_target_satuan_rp_realisasi->target_rp;
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_indikator_kinerja->satuan.'</td>';
                                                                            $tc_14 .= '<td style="text-align:left">Rp. '.number_format($program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                        } else {
                                                                            $tc_14 .= '<td></td>';
                                                                            $tc_14 .= '<td></td>';
                                                                        }
                                                                    }
                                                                    $kondisi_akhir_tahun = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_akhir)->first();
                                                                    if($kondisi_akhir_tahun)
                                                                    {
                                                                        $tc_14 .= '<td>'.array_sum($data_target).'/'.$data_satuan.'</td>';
                                                                        $tc_14 .= '<td>Rp. '.number_format(array_sum($data_target_rp), 2).'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                        $tc_14 .= '<td></td>';
                                                                    }
                                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                $tc_14 .='</tr>';
                                                            }
                                                            $d++;
                                                        }
                                                }
                                                $c++;
                                            }
                                    }
                                }
                                $b++;
                            }
                    }
                }
            }
        }
        // TC 14 End

        $pdf = PDF::loadView('admin.laporan.tc-14', [
            'tc_14' => $tc_14
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan TC-14.pdf');
    }

    public function tc_14_ekspor_excel()
    {
        return Excel::download(new Tc14Ekspor, 'Laporan TC-14.xlsx');
    }
}
