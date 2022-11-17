<?php

namespace App\Http\Controllers\Admin;

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
use App\Exports\Tc19Ekspor;
use App\Exports\E79Ekspor;
use App\Exports\E78Ekspor;
use App\Exports\E80Ekspor;
use App\Exports\E81Ekspor;

class LaporanController extends Controller
{
    public function index()
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

        $get_visis = Visi::whereHas('misi', function($q){
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
                                $tc_14 .='</tr>';
                            } else {
                                $tc_14 .= '<tr>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td style="text-align:left">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
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
                                    $tc_14 .='</tr>';
                                    // Permulaan Permasalahan
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
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                    $d = 1;
                                                    // Lokasi 2 source
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                                $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_awal)->first();
                                                                if($kondisi_kinerja_awal)
                                                                {
                                                                    $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                }
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
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                                $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_awal)->first();
                                                                if($kondisi_kinerja_awal)
                                                                {
                                                                    $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                }
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
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                        $d = 1;
                                                        // Lokasi 2 Destinasi
                                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                            if($d == 1)
                                                            {
                                                                    $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_awal)->first();
                                                                    if($kondisi_kinerja_awal)
                                                                    {
                                                                        $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                    }
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
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                                    $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_awal)->first();
                                                                    if($kondisi_kinerja_awal)
                                                                    {
                                                                        $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                    }
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
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                    $tc_14 .='</tr>';
                                    // Belum di konfigurasi
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
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                    $d = 1;
                                                    // Lokasi 2 source
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                                $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_awal)->first();
                                                                if($kondisi_kinerja_awal)
                                                                {
                                                                    $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                }

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
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                                $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_awal)->first();
                                                                if($kondisi_kinerja_awal)
                                                                {
                                                                    $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                }
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
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                        $d = 1;
                                                        // Lokasi 2 source
                                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                            if($d == 1)
                                                            {
                                                                    $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_awal)->first();
                                                                    if($kondisi_kinerja_awal)
                                                                    {
                                                                        $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                    }
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
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                                    $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_awal)->first();
                                                                    if($kondisi_kinerja_awal)
                                                                    {
                                                                        $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                    }
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
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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

        // TC 19 Start
        $tc_19 = '';

        $get_urusans = Urusan::orderBy('kode', 'asc')->where('tahun_perubahan', $tahun_awal)->get();
        $urusans = [];
        foreach ($get_urusans as $get_urusan) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $urusans[] = [
                    'id' => $cek_perubahan_urusan->urusan_id,
                    'kode' => $cek_perubahan_urusan->kode,
                    'deskripsi' => $cek_perubahan_urusan->deskripsi
                ];
            } else {
                $urusans[] = [
                    'id' => $get_urusan->id,
                    'kode' => $get_urusan->kode,
                    'deskripsi' => $get_urusan->deskripsi
                ];
            }
        }
        $a = 1;
        foreach ($urusans as $urusan) {
            $tc_19 .= '<tr>';
                $tc_19 .= '<td>'.$a++.'</td>';
                $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td>'.$urusan['deskripsi'].'</td>';
            $tc_19 .= '</tr>';

            $get_programs = Program::where('urusan_id', $urusan['id'])->get();
            $programs = [];
            foreach ($get_programs as $get_program) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)
                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
            // Program
            foreach ($programs as $program) {
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                    $tc_19 .= '<td>'.$program['kode'].'</td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$program['deskripsi'].'</td>';

                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                    $b = 1;
                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                        if($b == 1)
                        {
                            $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                            $opd = [];
                            $c = 1;
                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                if($c == 1)
                                {
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                } else {
                                    $tc_19 .= '<tr>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                }
                                $c++;
                            }
                        } else {
                            $tc_19 .= '<tr>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        }
                        $b++;
                    }

                // Kegiatan
                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
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
                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                        $tc_19 .= '<td>'.$program['kode'].'</td>';
                        $tc_19 .= '<td>'.$kegiatan['kode'].'</td>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$kegiatan['deskripsi'].'</td>';

                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                    $b = 1;
                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                        if($b == 1)
                        {
                            $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                            $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                            $opd = [];
                            $c = 1;
                            foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                if($c == 1)
                                {
                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($kegiatan_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                } else {
                                    $tc_19 .= '<tr>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($kegiatan_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                }
                                $c++;
                            }
                        } else {
                            $tc_19 .= '<tr>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        }
                        $b++;
                    }
                }
            }
        }
        // TC 19 End

        // E 79 Start
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
        $e_79 = '';
        $get_opd = [];
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_79 .= '<tr>';
                $e_79 .= '<td>'.$a++.'</td>';
                $e_79 .= '<td>'.$sasaran['deskripsi'].'</td>';
                $e_79 .= '<td>'.$sasaran['kode'].'</td>';

                $get_urusans = Urusan::whereHas('program', function($q) use ($sasaran){
                    $q->whereHas('program_rpjmd', function($q) use ($sasaran){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                $q->whereHas('sasaran', function($q) use ($sasaran){
                                    $q->where('id', $sasaran['id']);
                                });
                            });
                        });
                    });
                })->get();
                $urusans = [];
                foreach ($get_urusans as $get_urusan) {
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        $urusans[] = [
                            'id' => $cek_perubahan_urusan->urusan_id,
                            'kode' => $cek_perubahan_urusan->kode,
                            'deskripsi' => $cek_perubahan_urusan->deskripsi,
                        ];
                    } else {
                        $urusans[] = [
                            'id' => $get_urusan->id,
                            'kode' => $get_urusan->kode,
                            'deskripsi' => $get_urusan->deskripsi,
                        ];
                    }
                }

                $b = 1;
                foreach ($urusans as $urusan) {
                    if($b == 1)
                    {
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        // Start Program
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
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
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    }
                                    $c++;
                                }
                        }

                    } else {
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
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
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
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
        // E 79 End

        // E 78 Start

        $e_78 = '';
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_78 .= '<tr>';
                $e_78 .= '<td>'.$a++.'</td>';
                $e_78 .= '<td>'.$sasaran['deskripsi'].'</td>';

                $get_programs = Program::whereHas('program_rpjmd', function($q) use ($sasaran) {
                    $q->where('status_program', 'Prioritas');
                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                        $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran) {
                            $q->whereHas('sasaran', function($q) use ($sasaran) {
                                $q->where('id', $sasaran['id']);
                            });
                        });
                    });
                })->get();
                $programs = [];
                foreach ($get_programs as $get_program) {
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
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                        // Indikator Program Start
                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                        $c = 1;
                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                            if($c == 1)
                            {
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            }
                            $c++;
                        }
                    } else {
                        $e_78 .= '<tr>';
                        $e_78 .= '<td></td>';
                        $e_78 .= '<td></td>';
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                        // Indikator Program Start
                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                        $c = 1;
                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                            if($c == 1)
                            {
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            }
                            $c++;
                        }
                    }
                    $b++;
                }
        }

        // E 78 End

        // E 80 Start
        // $get_sasarans = Sasaran::all();
        // $sasarans = [];
        // foreach ($get_sasarans as $get_sasaran) {
        //     $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();
        //                                 // ->where('tahun_perubahan', $tahun_awal)->latest()->first();
        //     if($cek_perubahan_sasaran)
        //     {
        //         $sasarans[] = [
        //             'id' => $cek_perubahan_sasaran->sasaran_id,
        //             'kode' => $cek_perubahan_sasaran->kode,
        //             'deskripsi' => $cek_perubahan_sasaran->deskripsi
        //         ];
        //     } else {
        //         $sasarans[] = [
        //             'id' => $get_sasaran->id,
        //             'kode' => $get_sasaran->kode,
        //             'deskripsi' => $get_sasaran->deskripsi
        //         ];
        //     }
        // }
        $e_80 = '';
        // $a = 1;
        // foreach ($sasarans as $sasaran) {
        //     $e_80 .= '<tr>';
        //         $e_80 .= '<td colspan="41" style="text-align: left"><strong>Sasaran</strong></td>';
        //     $e_80 .= '</tr>';
        //     $e_80 .= '<tr>';
        //         $e_80 .= '<td>'.$a++.'</td>';
        //         $e_80 .= '<td>'.$sasaran['deskripsi'].'</td>';

        //     $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
        //         $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran) {
        //             $q->where('sasaran_id', $sasaran['id']);
        //         });
        //     })->get();
        //     $program = [];
        //     $urutan_a = 1;
        //     foreach ($get_program_rpjmds as $get_program_rpjmd) {
        //         $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
        //                                     ->where('tahun_perubahan', $tahun_awal)->latest()->first();
        //         if($cek_perubahan_program)
        //         {
        //             $program = [
        //                 'id' => $cek_perubahan_program->program_id,
        //                 'kode' => $cek_perubahan_program->kode,
        //                 'deskripsi' => $cek_perubahan_program->deskripsi
        //             ];
        //         } else {
        //             $program = [
        //                 'id' => $get_program_rpjmd->program_id,
        //                 'kode' => $get_program_rpjmd->program->kode,
        //                 'deskripsi' => $get_program_rpjmd->program->deskripsi
        //             ];
        //         }
        //         if($urutan_a == 1)
        //         {
        //                 $e_80 .= '<td>'.$program['deskripsi'].'</td>';
        //             $e_80 .= '</tr>';
        //         } else {
        //             $e_80 .= '<tr>';
        //                 $e_80 .= '<td>'.$a++.'</td>';
        //                 $e_80 .= '<td>'.$sasaran['deskripsi'].'</td>';
        //                 $e_80 .= '<td>'.$program['deskripsi'].'</td>';
        //             $e_80 .= '</tr>';
        //         }
        //         $urutan_a++;
        //     }
        // }
        // E 80 End

        // E 81 Start
        // $get_sasarans = Sasaran::all();
        // $sasarans = [];
        // foreach ($get_sasarans as $get_sasaran) {
        //     $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();
        //                                 // ->where('tahun_perubahan', $tahun_awal)->latest()->first();
        //     if($cek_perubahan_sasaran)
        //     {
        //         $sasarans[] = [
        //             'id' => $cek_perubahan_sasaran->sasaran_id,
        //             'kode' => $cek_perubahan_sasaran->kode,
        //             'deskripsi' => $cek_perubahan_sasaran->deskripsi
        //         ];
        //     } else {
        //         $sasarans[] = [
        //             'id' => $get_sasaran->id,
        //             'kode' => $get_sasaran->kode,
        //             'deskripsi' => $get_sasaran->deskripsi
        //         ];
        //     }
        // }
        $e_81 = '';
        // $a = 1;
        // foreach ($sasarans as $sasaran) {
        //     $e_81 .= '<tr>';
        //         $e_81 .= '<td colspan="41" style="text-align: left"><strong>Sasaran</strong></td>';
        //     $e_81 .= '</tr>';
        //     $e_81 .= '<tr>';
        //         $e_81 .= '<td>'.$a++.'</td>';
        //         $e_81 .= '<td>'.$sasaran['deskripsi'].'</td>';

        //     $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
        //         $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran) {
        //             $q->where('sasaran_id', $sasaran['id']);
        //         });
        //     })->get();
        //     $program = [];
        //     $urutan_a = 1;
        //     foreach ($get_program_rpjmds as $get_program_rpjmd) {
        //         $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
        //                                     ->where('tahun_perubahan', $tahun_awal)->latest()->first();
        //         if($cek_perubahan_program)
        //         {
        //             $program = [
        //                 'id' => $cek_perubahan_program->program_id,
        //                 'kode' => $cek_perubahan_program->kode,
        //                 'deskripsi' => $cek_perubahan_program->deskripsi
        //             ];
        //         } else {
        //             $program = [
        //                 'id' => $get_program_rpjmd->program_id,
        //                 'kode' => $get_program_rpjmd->program->kode,
        //                 'deskripsi' => $get_program_rpjmd->program->deskripsi
        //             ];
        //         }
        //         if($urutan_a == 1)
        //         {
        //                 $e_81 .= '<td>'.$program['deskripsi'].'</td>';
        //             $e_81 .= '</tr>';
        //         } else {
        //             $e_81 .= '<tr>';
        //                 $e_81 .= '<td>'.$a++.'</td>';
        //                 $e_81 .= '<td>'.$sasaran['deskripsi'].'</td>';
        //                 $e_81 .= '<td>'.$program['deskripsi'].'</td>';
        //             $e_81 .= '</tr>';
        //         }
        //         $urutan_a++;
        //     }
        // }
        // E 81 End

        return view('admin.laporan.index', [
            'tahuns' => $tahuns,
            'tc_14' => $tc_14,
            'tc_19' => $tc_19,
            'e_79' => $e_79,
            'e_78' => $e_78,
            'e_80' => $e_80,
            'e_81' => $e_81,
        ]);
    }

    public function laporan_tc_19(Request $request)
    {
        $tahun_awal = $request->tahun;
        // TC 19 Start
        $tc_19 = '';

        $get_urusans = Urusan::orderBy('kode', 'asc')->where('tahun_perubahan', $tahun_awal)->get();
        $urusans = [];
        foreach ($get_urusans as $get_urusan) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $urusans[] = [
                    'id' => $cek_perubahan_urusan->urusan_id,
                    'kode' => $cek_perubahan_urusan->kode,
                    'deskripsi' => $cek_perubahan_urusan->deskripsi
                ];
            } else {
                $urusans[] = [
                    'id' => $get_urusan->id,
                    'kode' => $get_urusan->kode,
                    'deskripsi' => $get_urusan->deskripsi
                ];
            }
        }
        $a = 1;
        foreach ($urusans as $urusan) {
            $tc_19 .= '<tr>';
                $tc_19 .= '<td>'.$a++.'</td>';
                $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td>'.$urusan['deskripsi'].'</td>';
            $tc_19 .= '</tr>';

            $get_programs = Program::where('urusan_id', $urusan['id'])->get();
            $programs = [];
            foreach ($get_programs as $get_program) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)
                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
            // Program
            foreach ($programs as $program) {
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                    $tc_19 .= '<td>'.$program['kode'].'</td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$program['deskripsi'].'</td>';

                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                    $b = 1;
                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                        if($b == 1)
                        {
                            $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                            $opd = [];
                            $c = 1;
                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                if($c == 1)
                                {
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                } else {
                                    $tc_19 .= '<tr>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                }
                                $c++;
                            }
                        } else {
                            $tc_19 .= '<tr>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        }
                        $b++;
                    }

                // Kegiatan
                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
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
                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                        $tc_19 .= '<td>'.$program['kode'].'</td>';
                        $tc_19 .= '<td>'.$kegiatan['kode'].'</td>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$kegiatan['deskripsi'].'</td>';

                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                    $b = 1;
                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                        if($b == 1)
                        {
                            $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                            $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                            $opd = [];
                            $c = 1;
                            foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                if($c == 1)
                                {
                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($kegiatan_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                } else {
                                    $tc_19 .= '<tr>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($kegiatan_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                }
                                $c++;
                            }
                        } else {
                            $tc_19 .= '<tr>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        }
                        $b++;
                    }
                }
            }
        }
        // TC 19 End

        return response()->json($tc_19);
    }

    public function laporan_e_79(Request $request)
    {
        $tahun_awal = $request->tahun;
        // E 79 Start
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
        $e_79 = '';
        $get_opd = [];
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_79 .= '<tr>';
                $e_79 .= '<td>'.$a++.'</td>';
                $e_79 .= '<td>'.$sasaran['deskripsi'].'</td>';
                $e_79 .= '<td>'.$sasaran['kode'].'</td>';

                $get_urusans = Urusan::whereHas('program', function($q) use ($sasaran){
                    $q->whereHas('program_rpjmd', function($q) use ($sasaran){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                $q->whereHas('sasaran', function($q) use ($sasaran){
                                    $q->where('id', $sasaran['id']);
                                });
                            });
                        });
                    });
                })->get();
                $urusans = [];
                foreach ($get_urusans as $get_urusan) {
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        $urusans[] = [
                            'id' => $cek_perubahan_urusan->urusan_id,
                            'kode' => $cek_perubahan_urusan->kode,
                            'deskripsi' => $cek_perubahan_urusan->deskripsi,
                        ];
                    } else {
                        $urusans[] = [
                            'id' => $get_urusan->id,
                            'kode' => $get_urusan->kode,
                            'deskripsi' => $get_urusan->deskripsi,
                        ];
                    }
                }

                $b = 1;
                foreach ($urusans as $urusan) {
                    if($b == 1)
                    {
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        // Start Program
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
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
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    }
                                    $c++;
                                }
                        }

                    } else {
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
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
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
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
        // E 79 End

        return response()->json(['e_79' => $e_79]);
    }

    public function laporan_e_78(Request $request)
    {
        $tahun_awal = $request->tahun;
        // E 78 Start
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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

        $e_78 = '';
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_78 .= '<tr>';
                $e_78 .= '<td>'.$a++.'</td>';
                $e_78 .= '<td>'.$sasaran['deskripsi'].'</td>';

                $get_programs = Program::whereHas('program_rpjmd', function($q) use ($sasaran) {
                    $q->where('status_program', 'Prioritas');
                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                        $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran) {
                            $q->whereHas('sasaran', function($q) use ($sasaran) {
                                $q->where('id', $sasaran['id']);
                            });
                        });
                    });
                })->get();
                $programs = [];
                foreach ($get_programs as $get_program) {
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
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                        // Indikator Program Start
                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                        $c = 1;
                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                            if($c == 1)
                            {
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            }
                            $c++;
                        }
                    } else {
                        $e_78 .= '<tr>';
                        $e_78 .= '<td></td>';
                        $e_78 .= '<td></td>';
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                        // Indikator Program Start
                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                        $c = 1;
                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                            if($c == 1)
                            {
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            }
                            $c++;
                        }
                    }
                    $b++;
                }
        }

        // E 78 End

        return response()->json(['e_78' => $e_78]);
    }

    public function laporan_e_80(Request $request)
    {
        $tahun_awal = $request->tahun;
        // E 80 Start
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();
                                        // ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
        $e_80 = '';
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_80 .= '<tr>';
                $e_80 .= '<td colspan="41" style="text-align: left"><strong>Sasaran</strong></td>';
            $e_80 .= '</tr>';
            $e_80 .= '<tr>';
                $e_80 .= '<td>'.$a++.'</td>';
                $e_80 .= '<td>'.$sasaran['deskripsi'].'</td>';

            $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran) {
                    $q->where('sasaran_id', $sasaran['id']);
                });
            })->get();
            $program = [];
            $urutan_a = 1;
            foreach ($get_program_rpjmds as $get_program_rpjmd) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                if($cek_perubahan_program)
                {
                    $program = [
                        'id' => $cek_perubahan_program->program_id,
                        'kode' => $cek_perubahan_program->kode,
                        'deskripsi' => $cek_perubahan_program->deskripsi
                    ];
                } else {
                    $program = [
                        'id' => $get_program_rpjmd->program_id,
                        'kode' => $get_program_rpjmd->program->kode,
                        'deskripsi' => $get_program_rpjmd->program->deskripsi
                    ];
                }
                if($urutan_a == 1)
                {
                        $e_80 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_80 .= '</tr>';
                } else {
                    $e_80 .= '<tr>';
                        $e_80 .= '<td>'.$a++.'</td>';
                        $e_80 .= '<td>'.$sasaran['deskripsi'].'</td>';
                        $e_80 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_80 .= '</tr>';
                }
                $urutan_a++;
            }
        }
        // E 80 End

        return response()->json(['e_80' => $e_80]);
    }

    public function laporan_e_81(Request $request)
    {
        // E 81 Start
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();
                                        // ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
                $e_81 .= '<td colspan="41" style="text-align: left"><strong>Sasaran</strong></td>';
            $e_81 .= '</tr>';
            $e_81 .= '<tr>';
                $e_81 .= '<td>'.$a++.'</td>';
                $e_81 .= '<td>'.$sasaran['deskripsi'].'</td>';

            $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran) {
                    $q->where('sasaran_id', $sasaran['id']);
                });
            })->get();
            $program = [];
            $urutan_a = 1;
            foreach ($get_program_rpjmds as $get_program_rpjmd) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                if($cek_perubahan_program)
                {
                    $program = [
                        'id' => $cek_perubahan_program->program_id,
                        'kode' => $cek_perubahan_program->kode,
                        'deskripsi' => $cek_perubahan_program->deskripsi
                    ];
                } else {
                    $program = [
                        'id' => $get_program_rpjmd->program_id,
                        'kode' => $get_program_rpjmd->program->kode,
                        'deskripsi' => $get_program_rpjmd->program->deskripsi
                    ];
                }
                if($urutan_a == 1)
                {
                        $e_81 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_81 .= '</tr>';
                } else {
                    $e_81 .= '<tr>';
                        $e_81 .= '<td>'.$a++.'</td>';
                        $e_81 .= '<td>'.$sasaran['deskripsi'].'</td>';
                        $e_81 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_81 .= '</tr>';
                }
                $urutan_a++;
            }
        }
        // E 81 End

        return response()->json(['e_81' => $e_81]);
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

        $get_visis = Visi::whereHas('misi', function($q){
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
                                $tc_14 .='</tr>';
                            } else {
                                $tc_14 .= '<tr>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td></td>';
                                    $tc_14 .= '<td style="text-align:left">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
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
                                    $tc_14 .='</tr>';
                                    // Permulaan Permasalahan
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
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                    $d = 1;
                                                    // Lokasi 2 source
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                                $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_awal)->first();
                                                                if($kondisi_kinerja_awal)
                                                                {
                                                                    $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                }
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
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                                $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_awal)->first();
                                                                if($kondisi_kinerja_awal)
                                                                {
                                                                    $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                }
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
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                        $d = 1;
                                                        // Lokasi 2 Destinasi
                                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                            if($d == 1)
                                                            {
                                                                    $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_awal)->first();
                                                                    if($kondisi_kinerja_awal)
                                                                    {
                                                                        $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                    }
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
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                                    $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_awal)->first();
                                                                    if($kondisi_kinerja_awal)
                                                                    {
                                                                        $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                    }
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
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                    $tc_14 .='</tr>';
                                    // Belum di konfigurasi
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
                                                    $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                    $d = 1;
                                                    // Lokasi 2 source
                                                    foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                        if($d == 1)
                                                        {
                                                                $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_awal)->first();
                                                                if($kondisi_kinerja_awal)
                                                                {
                                                                    $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                }

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
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                                $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                        ->where('tahun', $new_tahun_awal)->first();
                                                                if($kondisi_kinerja_awal)
                                                                {
                                                                    $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                } else {
                                                                    $tc_14 .= '<td></td>';
                                                                }
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
                                                                        $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                        $d = 1;
                                                        // Lokasi 2 source
                                                        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
                                                            if($d == 1)
                                                            {
                                                                    $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_awal)->first();
                                                                    if($kondisi_kinerja_awal)
                                                                    {
                                                                        $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                    }
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
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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
                                                                    $kondisi_kinerja_awal = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)
                                                                                            ->where('tahun', $new_tahun_awal)->first();
                                                                    if($kondisi_kinerja_awal)
                                                                    {
                                                                        $tc_14 .= '<td>'.$kondisi_kinerja_awal->target.'</td>';
                                                                    } else {
                                                                        $tc_14 .= '<td></td>';
                                                                    }
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
                                                                            $tc_14 .= '<td style="text-align:left">'.$program_target_satuan_rp_realisasi->target.' / '.$program_target_satuan_rp_realisasi->satuan.'</td>';
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

    public function tc_19_ekspor_pdf($tahun)
    {
        $tahun_awal = $tahun;
        // TC 19 Start
        $tc_19 = '';

        $get_urusans = Urusan::orderBy('kode', 'asc')->where('tahun_perubahan', $tahun_awal)->get();
        $urusans = [];
        foreach ($get_urusans as $get_urusan) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $urusans[] = [
                    'id' => $cek_perubahan_urusan->urusan_id,
                    'kode' => $cek_perubahan_urusan->kode,
                    'deskripsi' => $cek_perubahan_urusan->deskripsi
                ];
            } else {
                $urusans[] = [
                    'id' => $get_urusan->id,
                    'kode' => $get_urusan->kode,
                    'deskripsi' => $get_urusan->deskripsi
                ];
            }
        }
        $a = 1;
        foreach ($urusans as $urusan) {
            $tc_19 .= '<tr>';
                $tc_19 .= '<td>'.$a++.'</td>';
                $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td>'.$urusan['deskripsi'].'</td>';
            $tc_19 .= '</tr>';

            $get_programs = Program::where('urusan_id', $urusan['id'])->get();
            $programs = [];
            foreach ($get_programs as $get_program) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)
                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
            // Program
            foreach ($programs as $program) {
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                    $tc_19 .= '<td>'.$program['kode'].'</td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$program['deskripsi'].'</td>';

                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                    $b = 1;
                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                        if($b == 1)
                        {
                            $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                            $opd = [];
                            $c = 1;
                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                if($c == 1)
                                {
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                } else {
                                    $tc_19 .= '<tr>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                }
                                $c++;
                            }
                        } else {
                            $tc_19 .= '<tr>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        }
                        $b++;
                    }

                // Kegiatan
                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
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
                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                        $tc_19 .= '<td>'.$program['kode'].'</td>';
                        $tc_19 .= '<td>'.$kegiatan['kode'].'</td>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$kegiatan['deskripsi'].'</td>';

                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                    $b = 1;
                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                        if($b == 1)
                        {
                            $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                            $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                            $opd = [];
                            $c = 1;
                            foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                if($c == 1)
                                {
                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($kegiatan_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                } else {
                                    $tc_19 .= '<tr>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($kegiatan_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                }
                                $c++;
                            }
                        } else {
                            $tc_19 .= '<tr>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        }
                        $b++;
                    }
                }
            }
        }
        // TC 19 End

        $pdf = PDF::loadView('admin.laporan.tc-19', [
            'tc_19' => $tc_19,
            'tahun' => $tahun
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan TC-19.pdf');
    }

    public function tc_19_ekspor_excel($tahun)
    {
        return Excel::download(new Tc19Ekspor($tahun), 'Laporan TC-19.xlsx');
    }

    public function e_79_ekspor_pdf($tahun)
    {
        $tahun_awal = $tahun;
        // E 79 Start
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
        $e_79 = '';
        $get_opd = [];
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_79 .= '<tr>';
                $e_79 .= '<td>'.$a++.'</td>';
                $e_79 .= '<td>'.$sasaran['deskripsi'].'</td>';
                $e_79 .= '<td>'.$sasaran['kode'].'</td>';

                $get_urusans = Urusan::whereHas('program', function($q) use ($sasaran){
                    $q->whereHas('program_rpjmd', function($q) use ($sasaran){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                $q->whereHas('sasaran', function($q) use ($sasaran){
                                    $q->where('id', $sasaran['id']);
                                });
                            });
                        });
                    });
                })->get();
                $urusans = [];
                foreach ($get_urusans as $get_urusan) {
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        $urusans[] = [
                            'id' => $cek_perubahan_urusan->urusan_id,
                            'kode' => $cek_perubahan_urusan->kode,
                            'deskripsi' => $cek_perubahan_urusan->deskripsi,
                        ];
                    } else {
                        $urusans[] = [
                            'id' => $get_urusan->id,
                            'kode' => $get_urusan->kode,
                            'deskripsi' => $get_urusan->deskripsi,
                        ];
                    }
                }

                $b = 1;
                foreach ($urusans as $urusan) {
                    if($b == 1)
                    {
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        // Start Program
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
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
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    }
                                    $c++;
                                }
                        }

                    } else {
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                        $e_79 .= '</tr>';
                        $get_programs = Program::where('urusan_id', $urusan['id'])->whereHas('program_rpjmd', function($q) use ($sasaran){
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
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program){
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
                            $e_79 .= '<tr>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                $e_79 .= '<td>'.$program['kode'].'</td>';
                                $e_79 .= '<td></td>';
                                $e_79 .= '<td>'.$program['deskripsi'].'</td>';

                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                $c = 1;
                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Start Opd
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                }
                                                $d++;
                                            }
                                    } else {
                                        $e_79 .= '<tr>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td></td>';
                                            $e_79 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                            // Not yet OPD
                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                            $d = 1;
                                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                if($d == 1)
                                                {
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
                                                } else {
                                                    $e_79 .= '<tr>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $e_79 .= '<td></td>';
                                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                                        if($program_target_satuan_rp_realisasi)
                                                        {
                                                            $e_79 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                            $e_79 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                                        } else {
                                                            $e_79 .= '<td></td>';
                                                            $e_79 .= '<td></td>';
                                                        }
                                                        $e_79 .= '<td colspan="18"></td>';
                                                        $e_79 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                    $e_79 .= '</tr>';
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
        // E 79 End

        $pdf = PDF::loadView('admin.laporan.e-79', [
            'e_79' => $e_79,
            'tahun' => $tahun
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan E-79.pdf');
    }

    public function e_79_ekspor_excel($tahun)
    {
        return Excel::download(new E79Ekspor($tahun), 'Laporan E - 79.xlsx');
    }

    public function e_78_ekspor_pdf($tahun)
    {
        $tahun_awal = $tahun;
        // E 78 Start
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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

        $e_78 = '';
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_78 .= '<tr>';
                $e_78 .= '<td>'.$a++.'</td>';
                $e_78 .= '<td>'.$sasaran['deskripsi'].'</td>';

                $get_programs = Program::whereHas('program_rpjmd', function($q) use ($sasaran) {
                    $q->where('status_program', 'Prioritas');
                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                        $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran) {
                            $q->whereHas('sasaran', function($q) use ($sasaran) {
                                $q->where('id', $sasaran['id']);
                            });
                        });
                    });
                })->get();
                $programs = [];
                foreach ($get_programs as $get_program) {
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
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                        // Indikator Program Start
                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                        $c = 1;
                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                            if($c == 1)
                            {
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            }
                            $c++;
                        }
                    } else {
                        $e_78 .= '<tr>';
                        $e_78 .= '<td></td>';
                        $e_78 .= '<td></td>';
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                        // Indikator Program Start
                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                        $c = 1;
                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                            if($c == 1)
                            {
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            } else {
                                $e_78 .= '<tr>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td></td>';
                                    $e_78 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                $e_78 .= '</tr>';
                            }
                            $c++;
                        }
                    }
                    $b++;
                }
        }

        // E 78 End

        $pdf = PDF::loadView('admin.laporan.e-78', [
            'e_78' => $e_78,
            'tahun' => $tahun
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan E-78.pdf');
    }

    public function e_78_ekspor_excel($tahun)
    {
        return Excel::download(new E78Ekspor($tahun), 'Laporan E - 78.xlsx');
    }

    public function e_80_ekspor_pdf($tahun)
    {
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();
                                        // ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
        $e_80 = '';
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_80 .= '<tr>';
                $e_80 .= '<td colspan="41" style="text-align: left"><strong>Sasaran</strong></td>';
            $e_80 .= '</tr>';
            $e_80 .= '<tr>';
                $e_80 .= '<td>'.$a++.'</td>';
                $e_80 .= '<td>'.$sasaran['deskripsi'].'</td>';

            $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran) {
                    $q->where('sasaran_id', $sasaran['id']);
                });
            })->get();
            $program = [];
            $urutan_a = 1;
            foreach ($get_program_rpjmds as $get_program_rpjmd) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                            ->where('tahun_perubahan', $tahun)->latest()->first();
                if($cek_perubahan_program)
                {
                    $program = [
                        'id' => $cek_perubahan_program->program_id,
                        'kode' => $cek_perubahan_program->kode,
                        'deskripsi' => $cek_perubahan_program->deskripsi
                    ];
                } else {
                    $program = [
                        'id' => $get_program_rpjmd->program_id,
                        'kode' => $get_program_rpjmd->program->kode,
                        'deskripsi' => $get_program_rpjmd->program->deskripsi
                    ];
                }
                if($urutan_a == 1)
                {
                        $e_80 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_80 .= '</tr>';
                } else {
                    $e_80 .= '<tr>';
                        $e_80 .= '<td>'.$a++.'</td>';
                        $e_80 .= '<td>'.$sasaran['deskripsi'].'</td>';
                        $e_80 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_80 .= '</tr>';
                }
                $urutan_a++;
            }
        }
        // E 80 End

        $pdf = PDF::loadView('admin.laporan.e-80', [
            'e_80' => $e_80,
            'tahun' => $tahun
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan E-80.pdf');
    }

    public function e_80_ekspor_excel($tahun)
    {
        return Excel::download(new E80Ekspor($tahun), 'Laporan E - 80.xlsx');
    }

    public function e_81_ekspor_pdf($tahun)
    {
        // E 81 Start
        $get_sasarans = Sasaran::all();
        $sasarans = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();
                                        // ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
                $e_81 .= '<td colspan="41" style="text-align: left"><strong>Sasaran</strong></td>';
            $e_81 .= '</tr>';
            $e_81 .= '<tr>';
                $e_81 .= '<td>'.$a++.'</td>';
                $e_81 .= '<td>'.$sasaran['deskripsi'].'</td>';

            $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran) {
                    $q->where('sasaran_id', $sasaran['id']);
                });
            })->get();
            $program = [];
            $urutan_a = 1;
            foreach ($get_program_rpjmds as $get_program_rpjmd) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                            ->where('tahun_perubahan', $tahun)->latest()->first();
                if($cek_perubahan_program)
                {
                    $program = [
                        'id' => $cek_perubahan_program->program_id,
                        'kode' => $cek_perubahan_program->kode,
                        'deskripsi' => $cek_perubahan_program->deskripsi
                    ];
                } else {
                    $program = [
                        'id' => $get_program_rpjmd->program_id,
                        'kode' => $get_program_rpjmd->program->kode,
                        'deskripsi' => $get_program_rpjmd->program->deskripsi
                    ];
                }
                if($urutan_a == 1)
                {
                        $e_81 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_81 .= '</tr>';
                } else {
                    $e_81 .= '<tr>';
                        $e_81 .= '<td>'.$a++.'</td>';
                        $e_81 .= '<td>'.$sasaran['deskripsi'].'</td>';
                        $e_81 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_81 .= '</tr>';
                }
                $urutan_a++;
            }
        }
        // E 81 End

        $pdf = PDF::loadView('admin.laporan.e-81', [
            'e_81' => $e_81,
            'tahun' => $tahun
        ]);

        return $pdf->setPaper('b2', 'landscape')->stream('Laporan E-81.pdf');
    }

    public function e_81_ekspor_excel($tahun)
    {
        return Excel::download(new E81Ekspor($tahun), 'Laporan E - 81.xlsx');
    }
}
