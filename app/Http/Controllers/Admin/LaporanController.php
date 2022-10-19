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
use App\Models\TargetRpPertahunSasaran;
use App\Models\ProgramRpjmd;
use App\Models\PivotOpdProgramRpjmd;
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Models\MasterOpd;
use App\Models\PivotSasaranIndikatorProgramRpjmd;
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotProgramKegiatanRenstra;

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
                    $tc_14 .= '<td>'.$misi['deskripsi'].'</td>';
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
                        $tc_14 .= '<td>'.$tujuan['deskripsi'].'</td>';
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
                            $tc_14 .= '<td>'.$sasaran['deskripsi'].'</td>';
                            $get_sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                            $a = 0;
                            foreach ($get_sasaran_indikators as $get_sasaran_indikator) {
                                if($a == 0)
                                {
                                        $tc_14 .= '<td>'.$get_sasaran_indikator->indikator.'</td>';
                                        $tc_14 .= '<td colspan="14"></td>';
                                    $tc_14 .= '</tr>';
                                } else {
                                    $tc_14 .= '<tr>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td></td>';
                                        $tc_14 .= '<td>'.$get_sasaran_indikator->indikator.'</td>';
                                        $tc_14 .= '<td colspan="14"></td>';
                                    $tc_14 .= '</tr>';

                                    // $program_rpjmd = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($get_sasaran_indikator){
                                    //     $q->where('sasaran_indikator_id', $get_sasaran_indikator->id);
                                    // })->first();
                                    // if($program_rpjmd)
                                    // {
                                    //     $pivot_opd_program_rpjmds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)
                                    //                                 ->get();
                                    //     foreach ($pivot_opd_program_rpjmds as $pivot_opd_program_rpjmd) {
                                    //         $tc_14 .= '<tr>';
                                    //             $tc_14 .= '<td colspan="19" style="text-align:left"><strong>Program</strong></td>';
                                    //         $tc_14 .= '</tr>';
                                    //         $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $pivot_opd_program_rpjmd->program_rpjmd->program_id)
                                    //                                     ->orderBy('tahun_perubahan', 'desc')
                                    //                                     ->latest()->first();
                                    //                                     $program = [];
                                    //         if($cek_perubahan_program)
                                    //         {
                                    //             $program = [
                                    //                 'deskripsi' => $cek_perubahan_program->deskripsi,
                                    //             ];
                                    //         } else {
                                    //             $get_program = Program::find($pivot_opd_program_rpjmd->program_rpjmd->program_id);
                                    //             $program = [
                                    //                 'deskripsi' => $get_program->deskripsi
                                    //             ];
                                    //         }
                                    //         $tc_14 .= '<tr>';
                                    //             $tc_14 .= '<td></td>';
                                    //             $tc_14 .= '<td></td>';
                                    //             $tc_14 .= '<td></td>';
                                    //             $tc_14 .= '<td>'.$program['deskripsi'].'</td>';
                                    //         $tc_14 .= '</tr>';
                                    //     }
                                    // }
                                }
                                $a++;
                            }
                    }
                }
            }
        }

        return view('admin.laporan.index', [
            'tahuns' => $tahuns,
            'tc_14' => $tc_14
        ]);
    }
}
