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
use App\Models\PivotPerubahanProgramRpjmd;
use App\Models\MasterOpd;
use App\Models\TargetRpPertahunTujuan;
use App\Models\TargetRpPertahunSasaran;

class E79Controller extends Controller
{
    public function index()
    {
        return view('admin.laporan.e-79.index');
    }

    public function detail($tahun)
    {
        $opds = ProgramRpjmd::select('opd_id')->distinct('opd_id')->get();
        $html = '';
        $a = 1;
        foreach ($opds as $opd) {
            $get_sasarans = Sasaran::whereHas('program_rpjmd', function($q) use ($opd){
                $q->where('opd_id', $opd->opd_id);
            })->get();
            $sasarans = [];
            foreach ($get_sasarans as $get_sasaran) {
                $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();
                if($cek_perubahan_sasaran)
                {
                    $sasarans[] = [
                        'id' => $cek_perubahan_sasaran->sasaran_id,
                        'deskripsi' => $cek_perubahan_sasaran->deskripsi
                    ];
                } else {
                    $sasarans[] = [
                        'id' => $get_sasaran->id,
                        'deskripsi' => $get_sasaran->deskripsi
                    ];
                }
            }
            foreach ($sasarans as $sasaran) {
                $html .= '<tr>';
                    $html .= '<td>'.$a++.'</td>';
                    $html .= '<td>'.$sasaran['deskripsi'].'</td>';
                    $get_urusans = Urusan::whereHas('program', function($q) use ($sasaran){
                        $q->whereHas('program_rpjmd', function($q) use ($sasaran){
                            $q->where('sasaran_id', $sasaran['id']);
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
                                'deskripsi' => $cek_perubahban_urusan->deskripsi
                            ];
                        } else {
                            $urusans[] = [
                                'id' => $get_urusan->id,
                                'kode' => $get_urusan->kode,
                                'deskripsi' => $get_urusan->deskripsi
                            ];
                        }
                    }
                    $b = 0;
                    foreach ($urusans as $urusan) {
                        if($b == 0)
                        {
                                $html .= '<td>'.$urusan['kode'].'</td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$urusan['deskripsi'].'</td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$urusan['kode'].'</td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$urusan['deskripsi'].'</td>';
                            $html .='</tr>';
                        }
                        $b++;

                        $get_programs = Program::where('urusan_id', $urusan['id'])
                                        ->whereHas('program_rpjmd', function($q) use ($sasaran) {
                                            $q->where('sasaran_id', $sasaran['id']);
                                        })->get();
                        $programs = [];
                        foreach ($get_programs as $get_program) {
                            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                            if($cek_perubahan_program)
                            {
                                $programs[] = [
                                    'id' => $cek_perubahan_program->program_id,
                                    'kode' =>$cek_perubahan_program->kode,
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
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$urusan['kode'].'</td>';
                                $html .= '<td>'.$program['kode'].'</td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$program['deskripsi'].'</td>';
                            $html .='</tr>';
                        }
                    }
            }
        }
        return view('admin.laporan.e-79.detail.index', [
            'tahun' => $tahun,
            'html' => $html
        ]);
    }
}
