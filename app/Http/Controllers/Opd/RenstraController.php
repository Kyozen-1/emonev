<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use RealRashid\SweetAlert\Facades\Alert;
use DB;
use Validator;
use Carbon\Carbon;
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
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\MasterOpd;
use App\Models\TargetRpPertahunTujuan;
use App\Models\TargetRpPertahunSasaran;
use App\Models\TargetRpPertahunProgram;
use App\Models\Renstra;
use Auth;

class RenstraController extends Controller
{
    public function index()
    {
        $get_renstras = Renstra::select('id', 'misi_id')->where('opd_id', Auth::user()->opd_id)->groupBy('misi_id')->get();
        $misis = [];
        foreach ($get_renstras as $get_renstra) {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_renstra->misi_id)->latest()->first();
            if ($cek_perubahan_misi) {
                $misis[] = [
                    'renstra_id' => $get_renstra->id,
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi
                ];
            } else {
                $misi = Misi::find($get_renstra->misi_id);
                $misis[] = [
                    'renstra_id' => $get_renstra->id,
                    'id' => $misi->id,
                    'kode' => $misi->kode,
                    'deskripsi' => $misi->deskripsi
                ];
            }
        }
        return view('opd.renstra.index', [
            'misis' => $misis
        ]);
    }

    public function get_tujuan(Request $request)
    {
        $get_tujuans = Tujuan::select('id', 'deskripsi')->where('misi_id', $request->id)->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::select('tujuan_id', 'deskripsi')->where('tujuan_id', $get_tujuan->id)->latest()->first();
            if($cek_perubahan_tujuan)
            {
                $tujuan[] = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi
                ];
            } else {
                $tujuan[] = [
                    'id' => $get_tujuan->id,
                    'deskripsi' => $get_tujuan->deskripsi
                ];
            }
        }
        return response()->json($tujuan);
    }

    public function get_sasaran(Request $request)
    {
        $get_sasarans = Sasaran::select('id', 'deskripsi')->where('tujuan_id', $request->id)->get();
        $sasaran = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::select('sasaran_id', 'deskripsi')->where('sasaran_id', $get_sasaran->id)->latest()->first();
            if($cek_perubahan_sasaran)
            {
                $sasaran[] = [
                    'id' => $cek_perubahan_sasaran->sasaran_id,
                    'deskripsi' => $cek_perubahan_sasaran->deskripsi
                ];
            } else {
                $sasaran[] = [
                    'id' => $get_sasaran->id,
                    'deskripsi' => $get_sasaran->deskripsi
                ];
            }
        }
        return response()->json($sasaran);
    }

    public function get_program_rpjmd(Request $request)
    {
        $get_program_rpjmds = ProgramRpjmd::where('sasaran_id', $request->id)->get();
        $program_rpjmds = [];
        $programs = [];
        foreach ($get_program_rpjmds as $get_program_rpjmd) {
            $cek_perubahan_program_rpjmd = PivotPerubahanProgramRpjmd::where('program_rpjmd_id', $get_program_rpjmd->id)->latest()->first();
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
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_rpjmd['program_id'])->latest()->first();
            if($cek_perubahan_program)
            {
                $programs = [
                    'id' => $cek_perubahan_program->program_id,
                    'deskripsi' => $cek_perubahan_program->deskripsi
                ];
            } else {
                $program = Program::find($program_rpjmd['program_id']);
                $programs = [
                    'id' => $program->id,
                    'deskripsi' => $program->deskripsi
                ];
            }
        }

        return response()->json($programs);
    }

    public function tambah_item_renstra(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'misi_id' => 'required',
            'tujuan_id' => 'required',
            'sasaran_id' => 'required',
            'program_id' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal Menyimpan!', $errors->errors()->all()[0]);
            return redirect()->route('opd.renstra.index');
        }
        $cek_renstra = Renstra::where('misi_id', $request->misi_id)
                            ->where('tujuan_id', $request->tujuan_id)
                            ->where('sasaran_id', $request->sasaran_id)
                            ->where('program_id', $request->program_id)
                            ->where('opd_id', Auth::user()->opd_id)
                            ->first();
        if(!$cek_renstra)
        {
            $renstra = new Renstra;
            $renstra->misi_id = $request->misi_id;
            $renstra->tujuan_id = $request->tujuan_id;
            $renstra->sasaran_id = $request->sasaran_id;
            $renstra->program_id = $request->program_id;
            $renstra->opd_id = Auth::user()->opd_id;
            $renstra->save();
        }

        Alert::success('Berhasil', 'Item Renstra Berhasil Tersimpan');
        return redirect()->route('opd.renstra.index');
    }
}
