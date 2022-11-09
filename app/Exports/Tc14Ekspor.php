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
use App\Models\PivotPerubahanKegiatan;
use App\Models\Kegiatan;
use App\Models\PivotOpdRentraKegiatan;
use App\Models\TargetRpPertahunRenstraKegiatan;

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

        return view('admin.laporan.tc-14', compact('tc_14'));
    }
}
