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

class E80Ekspor implements FromView
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function view(): View
    {
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
                                            ->where('tahun_perubahan', $this->tahun)->latest()->first();
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

        return view('admin.laporan.e-80', [
            'e_80' => $e_80,
            'tahun' => $this->tahun
        ]);
    }
}
