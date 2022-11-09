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

class Tc19Ekspor implements FromView
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        $tahun_awal = $this->tahun;
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

        return view('admin.laporan.tc-19', [
            'tc_19' => $tc_19,
            'tahun' => $this->tahun
        ]);
    }
}
