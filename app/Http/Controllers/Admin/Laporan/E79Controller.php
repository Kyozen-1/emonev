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
                $html .='</tr>';

                $get_program_rpjmds = ProgramRpjmd::where('sasaran_id', $sasaran['id'])
                                        ->where('opd_id', $opd->opd_id)
                                        ->get();
                $program_rpjmds = [];
                foreach ($get_program_rpjmds as $get_program_rpjmd) {
                    $cek_perubahan_program_rpjmd = PivotPerubahanProgramRpjmd::where('program_rpjmd_id', $get_program_rpjmd->id)
                                                    ->latest()->first();
                    if($cek_perubahan_program_rpjmd)
                    {
                        $program_rpjmds[] = [
                            'program_id' => $cek_perubahan_program_rpjmd->program_id
                        ];
                    } else {
                        $program_rpjmds[] = [
                            'program_id' => $get_program_rpjmd->program_id
                        ];
                    }
                }

                foreach ($program_rpjmds as $program_rpjmd) {
                    // $get_program =
                }
            }
        }
        return view('admin.laporan.e-79.detail.index', [
            'tahun' => $tahun,
            'html' => $html
        ]);
    }
}
