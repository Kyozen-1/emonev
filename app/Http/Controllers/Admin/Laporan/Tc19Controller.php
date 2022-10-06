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
use Carbon\Carbon;
use App\Models\Urusan;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\PivotPerubahanKegiatan;
use App\Models\PivotKegiatanIndikator;
use App\Imports\SubKegiatanImport;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotPerubahanUrusan;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\PivotSubKegiatanIndikator;
use App\Models\TahunPeriode;
use App\Models\TargetRpPertahunProgram;
use App\Models\PivotProgramIndikator;

class Tc19Controller extends Controller
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
        return view('admin.laporan.tc-19.index');
    }

    public function detail($tahun)
    {
        $get_urusans = Urusan::all();
        $urusans = [];
        foreach ($get_urusans as $get_urusan) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
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

        $html = '';
        $a = 1;
        foreach ($urusans as $urusan) {
            $html .= '<tr>';
                $html .= '<td colspan="23" style="text-align:left;"><strong>Urusan</strong></td>';
            $html .= '</tr>';
            $html .= '<tr>';
                $html .= '<td>'.$a++.'</td>';
                $html .= '<td>'.$urusan['kode'].'</td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td>'.$urusan['deskripsi'].'</td>';
            $html .= '</tr>';

            $get_programs = Program::where('urusan_id', $urusan['id'])->get();
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
            foreach ($programs as $program) {
                $html .= '<tr>';
                    $html .= '<td colspan="24" style="text-align:left"><strong>Program</strong></td>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td>'.$urusan['kode'].'</td>';
                    $html .= '<td>'.$program['kode'].'</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td>'.$program['deskripsi'].'</td>';
                    $b = 0;
                    $get_opds = TargetRpPertahunProgram::select('opd_id')->distinct()->get();
                    foreach ($get_opds as $get_opd) {
                        $get_program_indikators = PivotProgramIndikator::whereHas('target_rp_pertahun_program', function($q) use ($get_opd, $tahun){
                            $q->where('opd_id', $get_opd->opd_id);
                            $q->where('tahun', $tahun);
                        })->where('program_id', $program['id'])->get();
                        foreach ($get_program_indikators as $get_program_indikator)
                        {
                            if($b == 0)
                            {
                                    $html .= '<td>'.$get_program_indikator->indikator.'</td>';
                                $html .= '</tr>';
                            } else {
                                $html .= '<tr>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                $html .= '</tr>';
                            }
                            $b++;
                        }
                    }

                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->latest()->get();
                $kegiatans = [];
                foreach ($get_kegiatans as $get_kegiatan) {
                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
                    if($cek_perubahan_kegiatan)
                    {
                        $kegiatans[] = [
                            'id' => $cek_perubahan_kegiatan->id,
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
                    $html .= '<tr>';
                        $html .= '<td colspan="24" style="text-align:left"><strong>Kegiatan</strong></td>';
                    $html .='</tr>';
                    $html .= '<tr>';
                        $html .= '<td></td>';
                        $html .= '<td>'.$urusan['kode'].'</td>';
                        $html .= '<td>'.$program['kode'].'</td>';
                        $html .= '<td>'.$kegiatan['kode'].'</td>';
                        $html .= '<td></td>';
                        $html .= '<td>'.$kegiatan['deskripsi'].'</td>';
                        $get_kegiatan_indikators = PivotKegiatanIndikator::where('kegiatan_id', $kegiatan['id'])->get();
                        $c = 0;
                        foreach ($get_kegiatan_indikators as $get_kegiatan_indikator) {
                            if($c == 0)
                            {
                                    $html .= '<td>'.$get_kegiatan_indikator->indikator.'</td>';
                                $html .= '</tr>';
                            } else {
                                $html .= '<tr>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td>'.$get_kegiatan_indikator->indikator.'</td>';
                                $html .= '</tr>';
                            }
                            $c++;
                        }
                }
            }
        }

        return view('admin.laporan.tc-19.detail.index', [
            'tahun' => $tahun,
            'html' => $html
        ]);
    }
}
