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
use App\Models\PivotOpdRentraKegiatan;
use App\Models\Kegiatan;
use App\Models\PivotPerubahanKegiatan;

class RenstraKegiatanController extends Controller
{
    public function get_kegiatan(Request $request)
    {
        $get_kegiatans = Kegiatan::where('program_id', $request->id)->get();
        $kegiatans = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                        ->orderBy('tahun_perubahan', 'desc')->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatans[] = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
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

        return response()->json($kegiatans);
    }

    public function get_opd(Request $request)
    {
        $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $request->id)
                    ->get();
        $opds = [];
        foreach ($get_opds as $get_opd) {
            $opds[] = [
                'id' => $get_opd->opd_id,
                'nama' => $get_opd->opd->nama
            ];
        }

        return response()->json($opds);
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'renstra_kegiatan_program_rpjmd_id' => 'required',
            'renstra_kegiatan_kegiatan_id' => 'required',
            'renstra_kegiatan_opd_id.*' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $renstra_kegiatan = new RenstraKegiatan;
        $renstra_kegiatan->program_rpjmd_id = $request->renstra_kegiatan_program_rpjmd_id;
        $renstra_kegiatan->kegiatan_id = $request->renstra_kegiatan_kegiatan_id;
        $renstra_kegiatan->save();

        $opd_id = $request->renstra_kegiatan_opd_id;

        for ($i=0; $i < count($opd_id); $i++) {
            $pivot_opd = new PivotOpdRentraKegiatan;
            $pivot_opd->rentra_kegiatan_id = $renstra_kegiatan->id;
            $pivot_opd->opd_id = $opd_id[$i];
            $pivot_opd->save();
        }

        return response()->json(['success' => 'Berhasil menambahkan kegiatan renstra']);
    }
}
