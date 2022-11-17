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

class E78Ekspor implements FromView
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        // E 78 Start
        $tahun = $this->tahun;
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

        return view('admin.laporan.e-78', [
            'e_78' => $e_78,
            'tahun' => $tahun
        ]);
    }
}
