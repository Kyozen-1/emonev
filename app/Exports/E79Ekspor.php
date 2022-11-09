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

class E79Ekspor implements FromView
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        $tahun_awal = $this->tahun;
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

        return view('admin.laporan.e-79', [
            'e_79' => $e_79,
            'tahun' => $this->tahun
        ]);
    }
}
