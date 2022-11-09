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
use App\Models\PivotPerubahanKegiatan;
use App\Models\Kegiatan;
use App\Models\PivotOpdRentraKegiatan;
use App\Models\TargetRpPertahunRenstraKegiatan;
use App\Exports\Tc14Ekspor;
use App\Exports\Tc19Ekspor;
use App\Exports\E79Ekspor;
use App\Exports\E78Ekspor;

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

        $get_visis = Visi::all();
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
            $get_misis = Misi::where('visi_id', $visi['id'])->get();
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
            foreach ($misis as $misi) {
                $tc_14 .= '<tr>';
                    $tc_14 .= '<td colspan="19" style="text-align:left"><strong>Misi</strong></td>';
                $tc_14 .='</tr>';
                $tc_14 .= '<tr>';
                    $tc_14 .= '<td>'.$misi['kode'].'</td>';
                    $tc_14 .= '<td></td>';
                    $tc_14 .= '<td></td>';
                    $tc_14 .= '<td style="text-align:left">'.$misi['deskripsi'].'</td>';
                    $tc_14 .= '<td colspan="15"></td>';
                $tc_14 .= '</tr>';

                $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
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
                        $tc_14 .= '<td colspan="19" style="text-align:left"><strong>Tujuan</strong></td>';
                    $tc_14 .= '</tr>';
                    $tc_14 .= '<tr>';
                        $tc_14 .= '<td>'.$misi['kode'].'</td>';
                        $tc_14 .= '<td>'.$tujuan['kode'].'</td>';
                        $tc_14 .= '<td></td>';
                        $tc_14 .= '<td style="text-align:left">'.$tujuan['deskripsi'].'</td>';
                        $tc_14 .= '<td colspan="15"></td>';
                    $tc_14 .= '</tr>';

                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
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
                            $tc_14 .= '<td colspan="19" style="text-align: left"><strong>Sasaran</strong></td>';
                        $tc_14 .= '</tr>';
                        $tc_14 .= '<tr>';
                            $tc_14 .= '<td>'.$misi['kode'].'</td>';
                            $tc_14 .= '<td>'.$tujuan['kode'].'</td>';
                            $tc_14 .= '<td>'.$sasaran['kode'].'</td>';
                            $tc_14 .= '<td style="text-align:left">'.$sasaran['deskripsi'].'</td>';
                            $get_sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                            $a = 0;
                            foreach ($get_sasaran_indikators as $get_sasaran_indikator) {
                                if($a == 0)
                                {
                                        $tc_14 .= '<td style="text-align:left">'.$get_sasaran_indikator->indikator.'</td>';
                                        $tc_14 .= '<td colspan="14"></td>';
                                    $tc_14 .= '</tr>';
                                } else {
                                    $tc_14 .= '<tr>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td style="text-align:left">'.$get_sasaran_indikator->indikator.'</td>';
                                        $tc_14 .= '<td colspan="14"></td>';
                                    $tc_14 .= '</tr>';
                                }
                                $a++;
                            }
                            $tc_14 .= '<tr>';
                                $tc_14 .= '<td colspan="19" style="text-align:left"><strong>Program</strong></td>';
                            $tc_14 .= '</tr>';
                            $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                                $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran){
                                    $q->where('sasaran_id', $sasaran['id']);
                                });
                            })->distinct('id')->get();
                            foreach ($get_program_rpjmds as $get_program_rpjmd) {
                                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                                            ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                if($cek_perubahan_program)
                                {
                                    $program = [
                                        'id' => $cek_perubahan_program->program_id,
                                        'deskripsi' => $cek_perubahan_program->deskripsi
                                    ];
                                } else {
                                    $get_program = Program::find($get_program_rpjmd->program_id);
                                    $program = [
                                        'id' => $get_program->id,
                                        'deskripsi' => $get_program->deskripsi
                                    ];
                                }

                                $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $get_program_rpjmd->id)->get();
                                foreach ($get_opds as $get_opd) {
                                    $tc_14 .= '<tr>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td style="text-align:left">'.$program['deskripsi'].'</td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $len = count($tahuns);
                                        $program_a = 0;
                                        foreach ($tahuns as $tahun) {
                                            $target_rp_pertahun_program = TargetRpPertahunProgram::where('program_rpjmd_id', $get_program_rpjmd->id)
                                                                            ->where('tahun', $tahun)->where('opd_id', $get_opd->opd_id)
                                                                            ->first();
                                            if($target_rp_pertahun_program)
                                            {
                                                $tc_14 .= '<td style="text-align:left">'.$target_rp_pertahun_program->target.' / '.$target_rp_pertahun_program->satuan.'</td>';
                                                $tc_14 .= '<td style="text-align:left">Rp. '.number_format($target_rp_pertahun_program->rp, 2).'</td>';
                                                if($program_a == $len - 1)
                                                {
                                                    $last_satuan = $target_rp_pertahun_program->satuan;
                                                    $last_target = $target_rp_pertahun_program->target;
                                                    $last_rp = $target_rp_pertahun_program->rp;

                                                    $tc_14 .= '<td style="text-align:left">'.$last_target.' / '.$last_satuan.'</td>';
                                                    $tc_14 .= '<td style="text-align:left">Rp. '.number_format($last_rp,2).'</td>';
                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd->opd->nama.'</td>';
                                                }
                                            } else {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                                if($program_a == $len - 1)
                                                {
                                                    $tc_14 .= '<td colspan="2"></td>';
                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd->opd->nama.'</td>';
                                                }
                                            }
                                            $program_a++;
                                        }
                                    $tc_14 .= '</tr>';
                                }
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
                                        ->where('tahun_perubahan', $tahun_awal)
                                        ->orderBy('tahun_perubahan', 'desc')->latest()->first();
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
                $tc_19 .= '<td colspan="24" style="text-align: left"><strong>Urusan</strong></td>';
            $tc_19 .= '</tr>';
            $tc_19 .= '<tr>';
                $tc_19 .= '<td>'.$a++.'</td>';
                $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td>'.$urusan['deskripsi'].'</td>';
            $tc_19 .= '</tr>';

            $get_opd_rpjmds = PivotOpdProgramRpjmd::whereHas('program_rpjmd', function($q) use ($urusan, $tahun_awal){
                $q->whereHas('program', function($q) use ($urusan, $tahun_awal){
                    $q->where('urusan_id', $urusan['id']);
                    $q->where('tahun_perubahan', $tahun_awal);
                });
            })->get();
            foreach ($get_opd_rpjmds as $get_opd_rpjmd) {
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td colspan="24" style="text-align: left"><strong>Program</strong></td>';
                $tc_19 .= '</tr>';
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                    $get_program_rpjmd = ProgramRpjmd::whereHas('pivot_opd_program_rpjmd', function($q) use ($get_opd_rpjmd){
                        $q->where('id', $get_opd_rpjmd->id);
                    })->first();
                    $program = [];
                    $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)->where('tahun_perubahan', $tahun_awal)->latest()->first();
                    if($cek_perubahan_program)
                    {
                        $program = [
                            'id' => $cek_perubahan_program->program_id,
                            'kode' => $cek_perubahan_program->kode,
                            'deskripsi' => $cek_perubahan_program->deskripsi,
                            'program_rpjmd_id' => $get_program_rpjmd->id
                        ];
                    } else {
                        $get_program = Program::find($get_program_rpjmd->program_id);
                        $program = [
                            'id' => $get_program->id,
                            'kode' => $get_program->kode,
                            'deskripsi' => $get_program->deskripsi,
                            'program_rpjmd_id' => $get_program_rpjmd->id
                        ];
                    }

                    $tc_19 .= '<td>'.$program['kode'].'</td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$program['deskripsi'].'</td>';
                    $tc_19 .= '<td></td>';
                    $get_target_rp_pertahun_program = TargetRpPertahunProgram::where('program_rpjmd_id', $program['program_rpjmd_id'])
                                                        ->where('opd_id', $get_opd_rpjmd->opd->id)
                                                        ->where('tahun', $tahun_awal)->first();
                    if($get_target_rp_pertahun_program)
                    {
                        $tc_19 .= '<td>'.$get_target_rp_pertahun_program->target.'/'.$get_target_rp_pertahun_program->satuan.'</td>';
                        $tc_19 .= '<td>Rp. '.number_format($get_target_rp_pertahun_program->rp, 2).'</td>';
                    } else {
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td></td>';
                    }
                    $tc_19 .= '<td colspan="12"></td>';
                    $tc_19 .= '<td>'.$get_opd_rpjmd->opd->nama.'</td>';
                    $tc_19 .= '<td></td>';
                $tc_19 .= '</tr>';

                $get_pivot_opd_renstra_kegiatans = PivotOpdRentraKegiatan::whereHas('renstra_kegiatan', function($q) use ($program){
                    $q->where('program_rpjmd_id', $program['program_rpjmd_id']);
                })->get();

                foreach ($get_pivot_opd_renstra_kegiatans as $get_pivot_opd_renstra_kegiatan) {
                    $get_renstra_kegiatan = RenstraKegiatan::find($get_pivot_opd_renstra_kegiatan->rentra_kegiatan_id);
                    $kegiatan = [];
                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                    if($cek_perubahan_kegiatan)
                    {
                        $kegiatan = [
                            'id' => $cek_perubahan_kegiatan->kegiatan_id,
                            'kode' => $cek_perubahan_kegiatan->kode,
                            'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                            'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                        ];
                    } else {
                        $kegiatan = [
                            'id' => $get_renstra_kegiatan->kegiatan_id,
                            'kode' => $get_renstra_kegiatan->kegiatan->kode,
                            'deskripsi' => $get_renstra_kegiatan->kegiatan->deskripsi,
                            'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                        ];
                    }

                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td colspan="24" style="text-align: left"><strong>Kegiatan</strong></td>';
                    $tc_19 .= '</tr>';
                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                        $tc_19 .= '<td>'.$program['kode'].'</td>';
                        $tc_19 .= '<td>'.$kegiatan['kode'].'</td>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$kegiatan['deskripsi'].'</td>';
                        $tc_19 .= '<td></td>';
                        $get_target_rp_pertahun_renstra_kegiatan = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                            ->where('opd_id', $get_pivot_opd_renstra_kegiatan->opd->id)
                                                            ->where('tahun', $tahun_awal)->first();
                        if($get_target_rp_pertahun_renstra_kegiatan)
                        {
                            $tc_19 .= '<td>'.$get_target_rp_pertahun_renstra_kegiatan->target.'/'.$get_target_rp_pertahun_renstra_kegiatan->satuan.'</td>';
                            $tc_19 .= '<td>Rp. '.number_format($get_target_rp_pertahun_renstra_kegiatan->rp, 2).'</td>';
                        } else {
                            $tc_19 .= '<td></td>';
                            $tc_19 .= '<td></td>';
                        }
                        $tc_19 .= '<td colspan="12"></td>';
                        $tc_19 .= '<td>'.$get_pivot_opd_renstra_kegiatan->opd->nama.'</td>';
                        $tc_19 .= '<td></td>';
                    $tc_19 .= '</tr>';
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
        $a = 1;
        $get_opd = [];
        foreach ($sasarans as $sasaran) {
            $e_79 .= '<tr>';
                $e_79 .= '<td colspan="29" style="text-align: left"><strong>Sasaran</strong></td>';
            $e_79 .='</tr>';
            $e_79 .= '<tr>';
                $e_79 .= '<td>'.$a++.'</td>';
                $e_79 .= '<td>'.$sasaran['deskripsi'].'</td>';
                $e_79 .= '<td>'.$sasaran['kode'].'</td>';

                $pivot_opd_program_rpjmds = PivotOpdProgramRpjmd::whereHas('program_rpjmd', function($q) use ($sasaran){
                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                        $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran) {
                            $q->where('sasaran_id', $sasaran['id']);
                        });
                    });
                })->get();

                $b = 1;
                foreach ($pivot_opd_program_rpjmds as $pivot_opd_program_rpjmd) {
                    $get_program_rpjmd = ProgramRpjmd::find($pivot_opd_program_rpjmd->program_rpjmd_id);
                    $get_program = Program::find($get_program_rpjmd->program_id);
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_program->urusan_id)->where('tahun_perubahan', $tahun_awal)->latest()->first();
                    $urusan = [];
                    if($cek_perubahan_urusan)
                    {
                        $urusan = [
                            'id' => $cek_perubahan_urusan->urusan_id,
                            'kode' => $cek_perubahan_urusan->kode,
                            'deskripsi' => $cek_perubahan_urusan->deskripsi
                        ];
                    } else {
                        $get_urusan = Urusan::find($get_program->urusan_id);
                        $urusan = [
                            'id' => $get_urusan->id,
                            'kode' => $get_urusan->kode,
                            'deskripsi' => $get_urusan->deskripsi
                        ];
                    }
                    if($b == 1)
                    {
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .='</tr>';
                        $get_program_from_urusans = Program::where('urusan_id', $urusan['id'])
                                                    ->whereHas('program_rpjmd', function($q) use ($pivot_opd_program_rpjmd){
                                                        $q->where('id', $pivot_opd_program_rpjmd->program_rpjmd_id);
                                                    })->first();
                        $program_from_urusan = [];
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_from_urusans->id)
                                                    ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $program_from_urusan = [
                                'id' => $cek_perubahan_program->program_id,
                                'kode' => $cek_perubahan_program->kode,
                                'deskripsi' => $cek_perubahan_program->deskripsi
                            ];
                        } else {
                            $program_from_urusan = [
                                'id' => $get_program_from_urusans->id,
                                'kode' => $get_program_from_urusans->kode,
                                'deskripsi' => $get_program_from_urusans->deskripsi
                            ];
                        }
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$program_from_urusan['deskripsi'].'</td>';
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .= '</tr>';

                        $cek_renstra_kegiatan = RenstraKegiatan::where('program_rpjmd_id',  $pivot_opd_program_rpjmd->program_rpjmd_id)->first();
                        if($cek_renstra_kegiatan)
                        {
                            $get_pivot_opd_renstra_kegiatans = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $cek_renstra_kegiatan->id)->get();
                            foreach ($get_pivot_opd_renstra_kegiatans as $get_pivot_opd_renstra_kegiatan) {
                                $get_renstra_kegiatan = RenstraKegiatan::find($get_pivot_opd_renstra_kegiatan->rentra_kegiatan_id);
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                                $kegiatan = [];
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatan = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                                    ];
                                } else {
                                    $kegiatan = [
                                        'id' => $get_renstra_kegiatan->kegiatan_id,
                                        'kode' => $get_renstra_kegiatan->kegiatan->kode,
                                        'deskripsi' => $get_renstra_kegiatan->kegiatan->deskripsi
                                    ];
                                }

                                $e_79 .= '<tr>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                    $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';
                                    $e_79 .= '<td colspan="21"></td>';
                                    $e_79 .= '<td>'.$get_pivot_opd_renstra_kegiatan->opd->nama.'</td>';
                                $e_79 .= '</tr>';
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
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .= '</tr>';

                        $get_program_from_urusans = Program::where('urusan_id', $urusan['id'])
                                                    ->whereHas('program_rpjmd', function($q) use ($pivot_opd_program_rpjmd){
                                                        $q->where('id', $pivot_opd_program_rpjmd->program_rpjmd_id);
                                                    })->first();
                        $program_from_urusan = [];
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_from_urusans->id)
                                                    ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $program_from_urusan = [
                                'id' => $cek_perubahan_program->program_id,
                                'kode' => $cek_perubahan_program->kode,
                                'deskripsi' => $cek_perubahan_program->deskripsi
                            ];
                        } else {
                            $program_from_urusan = [
                                'id' => $get_program_from_urusans->id,
                                'kode' => $get_program_from_urusans->kode,
                                'deskripsi' => $get_program_from_urusans->deskripsi
                            ];
                        }
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$program_from_urusan['deskripsi'].'</td>';
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .= '</tr>';

                        $cek_renstra_kegiatan = RenstraKegiatan::where('program_rpjmd_id',  $pivot_opd_program_rpjmd->program_rpjmd_id)->first();
                        if($cek_renstra_kegiatan)
                        {
                            $get_pivot_opd_renstra_kegiatans = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $cek_renstra_kegiatan->id)->get();
                            foreach ($get_pivot_opd_renstra_kegiatans as $get_pivot_opd_renstra_kegiatan) {
                                $get_renstra_kegiatan = RenstraKegiatan::find($get_pivot_opd_renstra_kegiatan->rentra_kegiatan_id);
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                                $kegiatan = [];
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatan = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                                    ];
                                } else {
                                    $kegiatan = [
                                        'id' => $get_renstra_kegiatan->kegiatan_id,
                                        'kode' => $get_renstra_kegiatan->kegiatan->kode,
                                        'deskripsi' => $get_renstra_kegiatan->kegiatan->deskripsi
                                    ];
                                }

                                $e_79 .= '<tr>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                    $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';
                                    $e_79 .= '<td colspan="21"></td>';
                                    $e_79 .= '<td>'.$get_pivot_opd_renstra_kegiatan->opd->nama.'</td>';
                                $e_79 .= '</tr>';
                            }
                        }
                    }
                    $b++;
                }
        }
        // E 79 End

        // E 78 Start

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
        $e_78 = '';
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_78 .= '<tr>';
                $e_78 .= '<td colspan="41" style="text-align: left"><strong>Sasaran</strong></td>';
            $e_78 .= '</tr>';
            $e_78 .= '<tr>';
                $e_78 .= '<td>'.$a++.'</td>';
                $e_78 .= '<td>'.$sasaran['deskripsi'].'</td>';

            $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran) {
                    $q->where('sasaran_id', $sasaran['id']);
                });
            })->where('status_program', 'Program Prioritas')->get();
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
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_78 .= '</tr>';
                } else {
                    $e_78 .= '<tr>';
                        $e_78 .= '<td>'.$a++.'</td>';
                        $e_78 .= '<td>'.$sasaran['deskripsi'].'</td>';
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_78 .= '</tr>';
                }
                $urutan_a++;
            }
        }

        // E 78 End

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
                                        ->where('tahun_perubahan', $tahun_awal)
                                        ->orderBy('tahun_perubahan', 'desc')->latest()->first();
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
                $tc_19 .= '<td colspan="24" style="text-align: left"><strong>Urusan</strong></td>';
            $tc_19 .= '</tr>';
            $tc_19 .= '<tr>';
                $tc_19 .= '<td>'.$a++.'</td>';
                $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td>'.$urusan['deskripsi'].'</td>';
            $tc_19 .= '</tr>';

            $get_opd_rpjmds = PivotOpdProgramRpjmd::whereHas('program_rpjmd', function($q) use ($urusan, $tahun_awal){
                $q->whereHas('program', function($q) use ($urusan, $tahun_awal){
                    $q->where('urusan_id', $urusan['id']);
                    $q->where('tahun_perubahan', $tahun_awal);
                });
            })->get();
            foreach ($get_opd_rpjmds as $get_opd_rpjmd) {
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td colspan="24" style="text-align: left"><strong>Program</strong></td>';
                $tc_19 .= '</tr>';
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                    $get_program_rpjmd = ProgramRpjmd::whereHas('pivot_opd_program_rpjmd', function($q) use ($get_opd_rpjmd){
                        $q->where('id', $get_opd_rpjmd->id);
                    })->first();
                    $program = [];
                    $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)->where('tahun_perubahan', $tahun_awal)->latest()->first();
                    if($cek_perubahan_program)
                    {
                        $program = [
                            'id' => $cek_perubahan_program->program_id,
                            'kode' => $cek_perubahan_program->kode,
                            'deskripsi' => $cek_perubahan_program->deskripsi,
                            'program_rpjmd_id' => $get_program_rpjmd->id
                        ];
                    } else {
                        $get_program = Program::find($get_program_rpjmd->program_id);
                        $program = [
                            'id' => $get_program->id,
                            'kode' => $get_program->kode,
                            'deskripsi' => $get_program->deskripsi,
                            'program_rpjmd_id' => $get_program_rpjmd->id
                        ];
                    }

                    $tc_19 .= '<td>'.$program['kode'].'</td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$program['deskripsi'].'</td>';
                    $tc_19 .= '<td></td>';
                    $get_target_rp_pertahun_program = TargetRpPertahunProgram::where('program_rpjmd_id', $program['program_rpjmd_id'])
                                                        ->where('opd_id', $get_opd_rpjmd->opd->id)
                                                        ->where('tahun', $tahun_awal)->first();
                    if($get_target_rp_pertahun_program)
                    {
                        $tc_19 .= '<td>'.$get_target_rp_pertahun_program->target.'/'.$get_target_rp_pertahun_program->satuan.'</td>';
                        $tc_19 .= '<td>Rp. '.number_format($get_target_rp_pertahun_program->rp, 2).'</td>';
                    } else {
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td></td>';
                    }
                    $tc_19 .= '<td colspan="12"></td>';
                    $tc_19 .= '<td>'.$get_opd_rpjmd->opd->nama.'</td>';
                    $tc_19 .= '<td></td>';
                $tc_19 .= '</tr>';

                $get_pivot_opd_renstra_kegiatans = PivotOpdRentraKegiatan::whereHas('renstra_kegiatan', function($q) use ($program){
                    $q->where('program_rpjmd_id', $program['program_rpjmd_id']);
                })->get();

                foreach ($get_pivot_opd_renstra_kegiatans as $get_pivot_opd_renstra_kegiatan) {
                    $get_renstra_kegiatan = RenstraKegiatan::find($get_pivot_opd_renstra_kegiatan->rentra_kegiatan_id);
                    $kegiatan = [];
                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                    if($cek_perubahan_kegiatan)
                    {
                        $kegiatan = [
                            'id' => $cek_perubahan_kegiatan->kegiatan_id,
                            'kode' => $cek_perubahan_kegiatan->kode,
                            'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                            'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                        ];
                    } else {
                        $kegiatan = [
                            'id' => $get_renstra_kegiatan->kegiatan_id,
                            'kode' => $get_renstra_kegiatan->kegiatan->kode,
                            'deskripsi' => $get_renstra_kegiatan->kegiatan->deskripsi,
                            'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                        ];
                    }

                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td colspan="24" style="text-align: left"><strong>Kegiatan</strong></td>';
                    $tc_19 .= '</tr>';
                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                        $tc_19 .= '<td>'.$program['kode'].'</td>';
                        $tc_19 .= '<td>'.$kegiatan['kode'].'</td>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$kegiatan['deskripsi'].'</td>';
                        $tc_19 .= '<td></td>';
                        $get_target_rp_pertahun_renstra_kegiatan = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                            ->where('opd_id', $get_pivot_opd_renstra_kegiatan->opd->id)
                                                            ->where('tahun', $tahun_awal)->first();
                        if($get_target_rp_pertahun_renstra_kegiatan)
                        {
                            $tc_19 .= '<td>'.$get_target_rp_pertahun_renstra_kegiatan->target.'/'.$get_target_rp_pertahun_renstra_kegiatan->satuan.'</td>';
                            $tc_19 .= '<td>Rp. '.number_format($get_target_rp_pertahun_renstra_kegiatan->rp, 2).'</td>';
                        } else {
                            $tc_19 .= '<td></td>';
                            $tc_19 .= '<td></td>';
                        }
                        $tc_19 .= '<td colspan="12"></td>';
                        $tc_19 .= '<td>'.$get_pivot_opd_renstra_kegiatan->opd->nama.'</td>';
                        $tc_19 .= '<td></td>';
                    $tc_19 .= '</tr>';
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
        $a = 1;
        $get_opd = [];
        foreach ($sasarans as $sasaran) {
            $e_79 .= '<tr>';
                $e_79 .= '<td colspan="29" style="text-align: left"><strong>Sasaran</strong></td>';
            $e_79 .='</tr>';
            $e_79 .= '<tr>';
                $e_79 .= '<td>'.$a++.'</td>';
                $e_79 .= '<td>'.$sasaran['deskripsi'].'</td>';
                $e_79 .= '<td>'.$sasaran['kode'].'</td>';

                $pivot_opd_program_rpjmds = PivotOpdProgramRpjmd::whereHas('program_rpjmd', function($q) use ($sasaran){
                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                        $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran) {
                            $q->where('sasaran_id', $sasaran['id']);
                        });
                    });
                })->get();

                $b = 1;
                foreach ($pivot_opd_program_rpjmds as $pivot_opd_program_rpjmd) {
                    $get_program_rpjmd = ProgramRpjmd::find($pivot_opd_program_rpjmd->program_rpjmd_id);
                    $get_program = Program::find($get_program_rpjmd->program_id);
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_program->urusan_id)->where('tahun_perubahan', $tahun_awal)->latest()->first();
                    $urusan = [];
                    if($cek_perubahan_urusan)
                    {
                        $urusan = [
                            'id' => $cek_perubahan_urusan->urusan_id,
                            'kode' => $cek_perubahan_urusan->kode,
                            'deskripsi' => $cek_perubahan_urusan->deskripsi
                        ];
                    } else {
                        $get_urusan = Urusan::find($get_program->urusan_id);
                        $urusan = [
                            'id' => $get_urusan->id,
                            'kode' => $get_urusan->kode,
                            'deskripsi' => $get_urusan->deskripsi
                        ];
                    }
                    if($b == 1)
                    {
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .='</tr>';
                        $get_program_from_urusans = Program::where('urusan_id', $urusan['id'])
                                                    ->whereHas('program_rpjmd', function($q) use ($pivot_opd_program_rpjmd){
                                                        $q->where('id', $pivot_opd_program_rpjmd->program_rpjmd_id);
                                                    })->first();
                        $program_from_urusan = [];
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_from_urusans->id)
                                                    ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $program_from_urusan = [
                                'id' => $cek_perubahan_program->program_id,
                                'kode' => $cek_perubahan_program->kode,
                                'deskripsi' => $cek_perubahan_program->deskripsi
                            ];
                        } else {
                            $program_from_urusan = [
                                'id' => $get_program_from_urusans->id,
                                'kode' => $get_program_from_urusans->kode,
                                'deskripsi' => $get_program_from_urusans->deskripsi
                            ];
                        }
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$program_from_urusan['deskripsi'].'</td>';
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .= '</tr>';

                        $cek_renstra_kegiatan = RenstraKegiatan::where('program_rpjmd_id',  $pivot_opd_program_rpjmd->program_rpjmd_id)->first();
                        if($cek_renstra_kegiatan)
                        {
                            $get_pivot_opd_renstra_kegiatans = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $cek_renstra_kegiatan->id)->get();
                            foreach ($get_pivot_opd_renstra_kegiatans as $get_pivot_opd_renstra_kegiatan) {
                                $get_renstra_kegiatan = RenstraKegiatan::find($get_pivot_opd_renstra_kegiatan->rentra_kegiatan_id);
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                                $kegiatan = [];
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatan = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                                    ];
                                } else {
                                    $kegiatan = [
                                        'id' => $get_renstra_kegiatan->kegiatan_id,
                                        'kode' => $get_renstra_kegiatan->kegiatan->kode,
                                        'deskripsi' => $get_renstra_kegiatan->kegiatan->deskripsi
                                    ];
                                }

                                $e_79 .= '<tr>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                    $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';
                                    $e_79 .= '<td colspan="21"></td>';
                                    $e_79 .= '<td>'.$get_pivot_opd_renstra_kegiatan->opd->nama.'</td>';
                                $e_79 .= '</tr>';
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
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .= '</tr>';

                        $get_program_from_urusans = Program::where('urusan_id', $urusan['id'])
                                                    ->whereHas('program_rpjmd', function($q) use ($pivot_opd_program_rpjmd){
                                                        $q->where('id', $pivot_opd_program_rpjmd->program_rpjmd_id);
                                                    })->first();
                        $program_from_urusan = [];
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_from_urusans->id)
                                                    ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $program_from_urusan = [
                                'id' => $cek_perubahan_program->program_id,
                                'kode' => $cek_perubahan_program->kode,
                                'deskripsi' => $cek_perubahan_program->deskripsi
                            ];
                        } else {
                            $program_from_urusan = [
                                'id' => $get_program_from_urusans->id,
                                'kode' => $get_program_from_urusans->kode,
                                'deskripsi' => $get_program_from_urusans->deskripsi
                            ];
                        }
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$program_from_urusan['deskripsi'].'</td>';
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .= '</tr>';

                        $cek_renstra_kegiatan = RenstraKegiatan::where('program_rpjmd_id',  $pivot_opd_program_rpjmd->program_rpjmd_id)->first();
                        if($cek_renstra_kegiatan)
                        {
                            $get_pivot_opd_renstra_kegiatans = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $cek_renstra_kegiatan->id)->get();
                            foreach ($get_pivot_opd_renstra_kegiatans as $get_pivot_opd_renstra_kegiatan) {
                                $get_renstra_kegiatan = RenstraKegiatan::find($get_pivot_opd_renstra_kegiatan->rentra_kegiatan_id);
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                                $kegiatan = [];
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatan = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                                    ];
                                } else {
                                    $kegiatan = [
                                        'id' => $get_renstra_kegiatan->kegiatan_id,
                                        'kode' => $get_renstra_kegiatan->kegiatan->kode,
                                        'deskripsi' => $get_renstra_kegiatan->kegiatan->deskripsi
                                    ];
                                }

                                $e_79 .= '<tr>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                    $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';
                                    $e_79 .= '<td colspan="21"></td>';
                                    $e_79 .= '<td>'.$get_pivot_opd_renstra_kegiatan->opd->nama.'</td>';
                                $e_79 .= '</tr>';
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
        // E 78 Start

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
        $e_78 = '';
        $a = 1;
        foreach ($sasarans as $sasaran) {
            $e_78 .= '<tr>';
                $e_78 .= '<td colspan="41" style="text-align: left"><strong>Sasaran</strong></td>';
            $e_78 .= '</tr>';
            $e_78 .= '<tr>';
                $e_78 .= '<td>'.$a++.'</td>';
                $e_78 .= '<td>'.$sasaran['deskripsi'].'</td>';

            $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran) {
                    $q->where('sasaran_id', $sasaran['id']);
                });
            })->where('status_program', 'Program Prioritas')->get();
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
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_78 .= '</tr>';
                } else {
                    $e_78 .= '<tr>';
                        $e_78 .= '<td>'.$a++.'</td>';
                        $e_78 .= '<td>'.$sasaran['deskripsi'].'</td>';
                        $e_78 .= '<td>'.$program['deskripsi'].'</td>';
                    $e_78 .= '</tr>';
                }
                $urutan_a++;
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
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::all();
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
            $get_misis = Misi::where('visi_id', $visi['id'])->get();
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
            foreach ($misis as $misi) {
                $tc_14 .= '<tr>';
                    $tc_14 .= '<td colspan="19" style="text-align:left"><strong>Misi</strong></td>';
                $tc_14 .='</tr>';
                $tc_14 .= '<tr>';
                    $tc_14 .= '<td>'.$misi['kode'].'</td>';
                    $tc_14 .= '<td></td>';
                    $tc_14 .= '<td></td>';
                    $tc_14 .= '<td style="text-align:left">'.$misi['deskripsi'].'</td>';
                    $tc_14 .= '<td colspan="15"></td>';
                $tc_14 .= '</tr>';

                $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
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
                        $tc_14 .= '<td colspan="19" style="text-align:left"><strong>Tujuan</strong></td>';
                    $tc_14 .= '</tr>';
                    $tc_14 .= '<tr>';
                        $tc_14 .= '<td>'.$misi['kode'].'</td>';
                        $tc_14 .= '<td>'.$tujuan['kode'].'</td>';
                        $tc_14 .= '<td></td>';
                        $tc_14 .= '<td style="text-align:left">'.$tujuan['deskripsi'].'</td>';
                        $tc_14 .= '<td colspan="15"></td>';
                    $tc_14 .= '</tr>';

                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
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
                            $tc_14 .= '<td colspan="19" style="text-align: left"><strong>Sasaran</strong></td>';
                        $tc_14 .= '</tr>';
                        $tc_14 .= '<tr>';
                            $tc_14 .= '<td>'.$misi['kode'].'</td>';
                            $tc_14 .= '<td>'.$tujuan['kode'].'</td>';
                            $tc_14 .= '<td>'.$sasaran['kode'].'</td>';
                            $tc_14 .= '<td style="text-align:left">'.$sasaran['deskripsi'].'</td>';
                            $get_sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                            $a = 0;
                            foreach ($get_sasaran_indikators as $get_sasaran_indikator) {
                                if($a == 0)
                                {
                                        $tc_14 .= '<td style="text-align:left">'.$get_sasaran_indikator->indikator.'</td>';
                                        $tc_14 .= '<td colspan="14"></td>';
                                    $tc_14 .= '</tr>';
                                } else {
                                    $tc_14 .= '<tr>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td style="text-align:left">'.$get_sasaran_indikator->indikator.'</td>';
                                        $tc_14 .= '<td colspan="14"></td>';
                                    $tc_14 .= '</tr>';
                                }
                                $a++;
                            }
                            $tc_14 .= '<tr>';
                                $tc_14 .= '<td colspan="19" style="text-align:left"><strong>Program</strong></td>';
                            $tc_14 .= '</tr>';
                            $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                                $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran){
                                    $q->where('sasaran_id', $sasaran['id']);
                                });
                            })->distinct('id')->get();
                            foreach ($get_program_rpjmds as $get_program_rpjmd) {
                                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                                            ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                if($cek_perubahan_program)
                                {
                                    $program = [
                                        'id' => $cek_perubahan_program->program_id,
                                        'deskripsi' => $cek_perubahan_program->deskripsi
                                    ];
                                } else {
                                    $get_program = Program::find($get_program_rpjmd->program_id);
                                    $program = [
                                        'id' => $get_program->id,
                                        'deskripsi' => $get_program->deskripsi
                                    ];
                                }

                                $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $get_program_rpjmd->id)->get();
                                foreach ($get_opds as $get_opd) {
                                    $tc_14 .= '<tr>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td style="text-align:left">'.$program['deskripsi'].'</td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $len = count($tahuns);
                                        $program_a = 0;
                                        foreach ($tahuns as $tahun) {
                                            $target_rp_pertahun_program = TargetRpPertahunProgram::where('program_rpjmd_id', $get_program_rpjmd->id)
                                                                            ->where('tahun', $tahun)->where('opd_id', $get_opd->opd_id)
                                                                            ->first();
                                            if($target_rp_pertahun_program)
                                            {
                                                $tc_14 .= '<td style="text-align:left">'.$target_rp_pertahun_program->target.' / '.$target_rp_pertahun_program->satuan.'</td>';
                                                $tc_14 .= '<td style="text-align:left">Rp. '.number_format($target_rp_pertahun_program->rp, 2).'</td>';
                                                if($program_a == $len - 1)
                                                {
                                                    $last_satuan = $target_rp_pertahun_program->satuan;
                                                    $last_target = $target_rp_pertahun_program->target;
                                                    $last_rp = $target_rp_pertahun_program->rp;

                                                    $tc_14 .= '<td style="text-align:left">'.$last_target.' / '.$last_satuan.'</td>';
                                                    $tc_14 .= '<td style="text-align:left">Rp. '.number_format($last_rp,2).'</td>';
                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd->opd->nama.'</td>';
                                                }
                                            } else {
                                                $tc_14 .= '<td></td>';
                                                $tc_14 .= '<td></td>';
                                                if($program_a == $len - 1)
                                                {
                                                    $tc_14 .= '<td colspan="2"></td>';
                                                    $tc_14 .= '<td style="text-align:left">'.$get_opd->opd->nama.'</td>';
                                                }
                                            }
                                            $program_a++;
                                        }
                                    $tc_14 .= '</tr>';
                                }
                            }

                    }
                }
            }
        }
        // TC 14 End

        $pdf = PDF::loadView('admin.laporan.tc-14', [
            'tc_14' => $tc_14
        ]);

        return $pdf->setPaper('a4', 'landscape')->stream('Laporan TC-14.pdf');
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
                                        ->where('tahun_perubahan', $tahun_awal)
                                        ->orderBy('tahun_perubahan', 'desc')->latest()->first();
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
                $tc_19 .= '<td colspan="24" style="text-align: left"><strong>Urusan</strong></td>';
            $tc_19 .= '</tr>';
            $tc_19 .= '<tr>';
                $tc_19 .= '<td>'.$a++.'</td>';
                $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td>'.$urusan['deskripsi'].'</td>';
            $tc_19 .= '</tr>';

            $get_opd_rpjmds = PivotOpdProgramRpjmd::whereHas('program_rpjmd', function($q) use ($urusan, $tahun_awal){
                $q->whereHas('program', function($q) use ($urusan, $tahun_awal){
                    $q->where('urusan_id', $urusan['id']);
                    $q->where('tahun_perubahan', $tahun_awal);
                });
            })->get();
            foreach ($get_opd_rpjmds as $get_opd_rpjmd) {
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td colspan="24" style="text-align: left"><strong>Program</strong></td>';
                $tc_19 .= '</tr>';
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                    $get_program_rpjmd = ProgramRpjmd::whereHas('pivot_opd_program_rpjmd', function($q) use ($get_opd_rpjmd){
                        $q->where('id', $get_opd_rpjmd->id);
                    })->first();
                    $program = [];
                    $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)->where('tahun_perubahan', $tahun_awal)->latest()->first();
                    if($cek_perubahan_program)
                    {
                        $program = [
                            'id' => $cek_perubahan_program->program_id,
                            'kode' => $cek_perubahan_program->kode,
                            'deskripsi' => $cek_perubahan_program->deskripsi,
                            'program_rpjmd_id' => $get_program_rpjmd->id
                        ];
                    } else {
                        $get_program = Program::find($get_program_rpjmd->program_id);
                        $program = [
                            'id' => $get_program->id,
                            'kode' => $get_program->kode,
                            'deskripsi' => $get_program->deskripsi,
                            'program_rpjmd_id' => $get_program_rpjmd->id
                        ];
                    }

                    $tc_19 .= '<td>'.$program['kode'].'</td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$program['deskripsi'].'</td>';
                    $tc_19 .= '<td></td>';
                    $get_target_rp_pertahun_program = TargetRpPertahunProgram::where('program_rpjmd_id', $program['program_rpjmd_id'])
                                                        ->where('opd_id', $get_opd_rpjmd->opd->id)
                                                        ->where('tahun', $tahun_awal)->first();
                    if($get_target_rp_pertahun_program)
                    {
                        $tc_19 .= '<td>'.$get_target_rp_pertahun_program->target.'/'.$get_target_rp_pertahun_program->satuan.'</td>';
                        $tc_19 .= '<td>Rp. '.number_format($get_target_rp_pertahun_program->rp, 2).'</td>';
                    } else {
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td></td>';
                    }
                    $tc_19 .= '<td colspan="12"></td>';
                    $tc_19 .= '<td>'.$get_opd_rpjmd->opd->nama.'</td>';
                    $tc_19 .= '<td></td>';
                $tc_19 .= '</tr>';

                $get_pivot_opd_renstra_kegiatans = PivotOpdRentraKegiatan::whereHas('renstra_kegiatan', function($q) use ($program){
                    $q->where('program_rpjmd_id', $program['program_rpjmd_id']);
                })->get();

                foreach ($get_pivot_opd_renstra_kegiatans as $get_pivot_opd_renstra_kegiatan) {
                    $get_renstra_kegiatan = RenstraKegiatan::find($get_pivot_opd_renstra_kegiatan->rentra_kegiatan_id);
                    $kegiatan = [];
                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                    if($cek_perubahan_kegiatan)
                    {
                        $kegiatan = [
                            'id' => $cek_perubahan_kegiatan->kegiatan_id,
                            'kode' => $cek_perubahan_kegiatan->kode,
                            'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                            'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                        ];
                    } else {
                        $kegiatan = [
                            'id' => $get_renstra_kegiatan->kegiatan_id,
                            'kode' => $get_renstra_kegiatan->kegiatan->kode,
                            'deskripsi' => $get_renstra_kegiatan->kegiatan->deskripsi,
                            'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                        ];
                    }

                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td colspan="24" style="text-align: left"><strong>Kegiatan</strong></td>';
                    $tc_19 .= '</tr>';
                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                        $tc_19 .= '<td>'.$program['kode'].'</td>';
                        $tc_19 .= '<td>'.$kegiatan['kode'].'</td>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$kegiatan['deskripsi'].'</td>';
                        $tc_19 .= '<td></td>';
                        $get_target_rp_pertahun_renstra_kegiatan = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                            ->where('opd_id', $get_pivot_opd_renstra_kegiatan->opd->id)
                                                            ->where('tahun', $tahun_awal)->first();
                        if($get_target_rp_pertahun_renstra_kegiatan)
                        {
                            $tc_19 .= '<td>'.$get_target_rp_pertahun_renstra_kegiatan->target.'/'.$get_target_rp_pertahun_renstra_kegiatan->satuan.'</td>';
                            $tc_19 .= '<td>Rp. '.number_format($get_target_rp_pertahun_renstra_kegiatan->rp, 2).'</td>';
                        } else {
                            $tc_19 .= '<td></td>';
                            $tc_19 .= '<td></td>';
                        }
                        $tc_19 .= '<td colspan="12"></td>';
                        $tc_19 .= '<td>'.$get_pivot_opd_renstra_kegiatan->opd->nama.'</td>';
                        $tc_19 .= '<td></td>';
                    $tc_19 .= '</tr>';
                }
            }
        }
        // TC 19 End

        $pdf = PDF::loadView('admin.laporan.tc-19', [
            'tc_19' => $tc_19,
            'tahun' => $tahun
        ]);

        return $pdf->setPaper('a4', 'landscape')->stream('Laporan TC-19.pdf');
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
        $a = 1;
        $get_opd = [];
        foreach ($sasarans as $sasaran) {
            $e_79 .= '<tr>';
                $e_79 .= '<td colspan="29" style="text-align: left"><strong>Sasaran</strong></td>';
            $e_79 .='</tr>';
            $e_79 .= '<tr>';
                $e_79 .= '<td>'.$a++.'</td>';
                $e_79 .= '<td>'.$sasaran['deskripsi'].'</td>';
                $e_79 .= '<td>'.$sasaran['kode'].'</td>';

                $pivot_opd_program_rpjmds = PivotOpdProgramRpjmd::whereHas('program_rpjmd', function($q) use ($sasaran){
                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran){
                        $q->whereHas('pivot_sasaran_indikator', function($q) use ($sasaran) {
                            $q->where('sasaran_id', $sasaran['id']);
                        });
                    });
                })->get();

                $b = 1;
                foreach ($pivot_opd_program_rpjmds as $pivot_opd_program_rpjmd) {
                    $get_program_rpjmd = ProgramRpjmd::find($pivot_opd_program_rpjmd->program_rpjmd_id);
                    $get_program = Program::find($get_program_rpjmd->program_id);
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_program->urusan_id)->where('tahun_perubahan', $tahun_awal)->latest()->first();
                    $urusan = [];
                    if($cek_perubahan_urusan)
                    {
                        $urusan = [
                            'id' => $cek_perubahan_urusan->urusan_id,
                            'kode' => $cek_perubahan_urusan->kode,
                            'deskripsi' => $cek_perubahan_urusan->deskripsi
                        ];
                    } else {
                        $get_urusan = Urusan::find($get_program->urusan_id);
                        $urusan = [
                            'id' => $get_urusan->id,
                            'kode' => $get_urusan->kode,
                            'deskripsi' => $get_urusan->deskripsi
                        ];
                    }
                    if($b == 1)
                    {
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$urusan['deskripsi'].'</td>';
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .='</tr>';
                        $get_program_from_urusans = Program::where('urusan_id', $urusan['id'])
                                                    ->whereHas('program_rpjmd', function($q) use ($pivot_opd_program_rpjmd){
                                                        $q->where('id', $pivot_opd_program_rpjmd->program_rpjmd_id);
                                                    })->first();
                        $program_from_urusan = [];
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_from_urusans->id)
                                                    ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $program_from_urusan = [
                                'id' => $cek_perubahan_program->program_id,
                                'kode' => $cek_perubahan_program->kode,
                                'deskripsi' => $cek_perubahan_program->deskripsi
                            ];
                        } else {
                            $program_from_urusan = [
                                'id' => $get_program_from_urusans->id,
                                'kode' => $get_program_from_urusans->kode,
                                'deskripsi' => $get_program_from_urusans->deskripsi
                            ];
                        }
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$program_from_urusan['deskripsi'].'</td>';
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .= '</tr>';

                        $cek_renstra_kegiatan = RenstraKegiatan::where('program_rpjmd_id',  $pivot_opd_program_rpjmd->program_rpjmd_id)->first();
                        if($cek_renstra_kegiatan)
                        {
                            $get_pivot_opd_renstra_kegiatans = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $cek_renstra_kegiatan->id)->get();
                            foreach ($get_pivot_opd_renstra_kegiatans as $get_pivot_opd_renstra_kegiatan) {
                                $get_renstra_kegiatan = RenstraKegiatan::find($get_pivot_opd_renstra_kegiatan->rentra_kegiatan_id);
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                                $kegiatan = [];
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatan = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                                    ];
                                } else {
                                    $kegiatan = [
                                        'id' => $get_renstra_kegiatan->kegiatan_id,
                                        'kode' => $get_renstra_kegiatan->kegiatan->kode,
                                        'deskripsi' => $get_renstra_kegiatan->kegiatan->deskripsi
                                    ];
                                }

                                $e_79 .= '<tr>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                    $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';
                                    $e_79 .= '<td colspan="21"></td>';
                                    $e_79 .= '<td>'.$get_pivot_opd_renstra_kegiatan->opd->nama.'</td>';
                                $e_79 .= '</tr>';
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
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .= '</tr>';

                        $get_program_from_urusans = Program::where('urusan_id', $urusan['id'])
                                                    ->whereHas('program_rpjmd', function($q) use ($pivot_opd_program_rpjmd){
                                                        $q->where('id', $pivot_opd_program_rpjmd->program_rpjmd_id);
                                                    })->first();
                        $program_from_urusan = [];
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_from_urusans->id)
                                                    ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $program_from_urusan = [
                                'id' => $cek_perubahan_program->program_id,
                                'kode' => $cek_perubahan_program->kode,
                                'deskripsi' => $cek_perubahan_program->deskripsi
                            ];
                        } else {
                            $program_from_urusan = [
                                'id' => $get_program_from_urusans->id,
                                'kode' => $get_program_from_urusans->kode,
                                'deskripsi' => $get_program_from_urusans->deskripsi
                            ];
                        }
                        $e_79 .= '<tr>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                            $e_79 .= '<td>'.$urusan['kode'].'</td>';
                            $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                            $e_79 .= '<td></td>';
                            $e_79 .= '<td>'.$program_from_urusan['deskripsi'].'</td>';
                            $e_79 .= '<td colspan="21"></td>';
                            $e_79 .= '<td>'.$pivot_opd_program_rpjmd->opd->nama.'</td>';
                        $e_79 .= '</tr>';

                        $cek_renstra_kegiatan = RenstraKegiatan::where('program_rpjmd_id',  $pivot_opd_program_rpjmd->program_rpjmd_id)->first();
                        if($cek_renstra_kegiatan)
                        {
                            $get_pivot_opd_renstra_kegiatans = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $cek_renstra_kegiatan->id)->get();
                            foreach ($get_pivot_opd_renstra_kegiatans as $get_pivot_opd_renstra_kegiatan) {
                                $get_renstra_kegiatan = RenstraKegiatan::find($get_pivot_opd_renstra_kegiatan->rentra_kegiatan_id);
                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                                $kegiatan = [];
                                if($cek_perubahan_kegiatan)
                                {
                                    $kegiatan = [
                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                        'kode' => $cek_perubahan_kegiatan->kode,
                                        'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                                    ];
                                } else {
                                    $kegiatan = [
                                        'id' => $get_renstra_kegiatan->kegiatan_id,
                                        'kode' => $get_renstra_kegiatan->kegiatan->kode,
                                        'deskripsi' => $get_renstra_kegiatan->kegiatan->deskripsi
                                    ];
                                }

                                $e_79 .= '<tr>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td></td>';
                                    $e_79 .= '<td>'.$sasaran['kode'].'</td>';
                                    $e_79 .= '<td>'.$urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$program_from_urusan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['kode'].'</td>';
                                    $e_79 .= '<td>'.$kegiatan['deskripsi'].'</td>';
                                    $e_79 .= '<td colspan="21"></td>';
                                    $e_79 .= '<td>'.$get_pivot_opd_renstra_kegiatan->opd->nama.'</td>';
                                $e_79 .= '</tr>';
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

    public function e_78_ekspor_excel($tahun)
    {
        return Excel::download(new E78Ekspor($tahun), 'Laporan E - 78.xlsx');
    }
}
