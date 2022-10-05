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

class ProgramRpjmdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $get_urusans = Urusan::all();
        $urusans = [];
        foreach ($get_urusans as $get_urusan) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $urusans[] = [
                    'id' => $cek_perubahan_urusan->urusan_id,
                    'deskripsi' => $cek_perubahan_urusan->deskripsi
                ];
            } else {
                $urusan = Urusan::find($get_urusan->id);
                $urusans[] = [
                    'id' => $urusan->id,
                    'deskripsi' => $urusan->deskripsi
                ];
            }
        }

        $master_opd = MasterOpd::pluck('nama', 'id');

        $sasarans = Sasaran::all();
        return view('admin.program-rpjmd.index', [
            'urusans' => $urusans,
            'master_opd' => $master_opd,
            'sasarans' => $sasarans
        ]);
    }

    public function get_program(Request $request)
    {
        $get_programs = Program::select('id', 'deskripsi')->where('urusan_id', $request->id)->get();
        $program = [];
        foreach ($get_programs as $get_program) {
            $cek_perubahan_program = PivotPerubahanProgram::select('program_id', 'deskripsi')->where('program_id', $get_program->id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program[] = [
                    'id' => $cek_perubahan_program->program_id,
                    'deskripsi' => $cek_perubahan_program->deskripsi
                ];
            } else {
                $program[] = [
                    'id' => $get_program->id,
                    'deskripsi' => $get_program->deskripsi
                ];
            }
        }
        return response()->json($program);
    }

    public function get_sasaran($id)
    {
        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $id)->latest()->first();
        if($cek_perubahan_sasaran)
        {
            $tujuan_id = $cek_perubahan_sasaran->tujuan_id;
            $kode_sasaran = $cek_perubahan_sasaran->kode;
            $deskripsi_sasaran = $cek_perubahan_sasaran->deskripsi;
        } else {
            $sasaran = Sasaran::find($id);
            $tujuan_id = $sasaran->tujuan_id;
            $kode_sasaran = $sasaran->kode;
            $deskripsi_sasaran = $sasaran->deskripsi;
        }

        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $tujuan_id)->latest()->first();
        if($cek_perubahan_tujuan)
        {
            $misi_id = $cek_perubahan_tujuan->misi_id;
            $kode_tujuan = $cek_perubahan_tujuan->kode;
            $deskripsi_tujuan = $cek_perubahan_tujuan->deskripsi;
        } else {
            $tujuan = Tujuan::find($tujuan_id);
            $misi_id = $tujuan->misi_id;
            $kode_tujuan = $tujuan->kode;
            $deskripsi_tujuan = $tujuan->deskripsi;
        }

        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
        if($cek_perubahan_misi)
        {
            $kode_misi = $cek_perubahan_misi->kode;
            $deskripsi_misi =  $cek_perubahan_misi->deskripsi;
        } else {
            $misi = Misi::find($misi_id);
            $kode_misi = $misi->kode;
            $deskripsi_misi = $misi->deskripsi;
        }

        $array = [
            'sasaran' => $kode_sasaran . '. ' . $deskripsi_sasaran,
            'tujuan' => $kode_tujuan . '. '. $deskripsi_tujuan,
            'misi' => $kode_misi . '. '.$deskripsi_misi,
        ];

        return response()->json(['result' => $array]);
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sasaran_id' => 'required',
            'urusan_id' => 'required',
            'program_id' => 'required',
            'status_program' => 'required',
            'opd_id' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_rpjmd = new ProgramRpjmd;
        $program_rpjmd->program_id = $request->program_id;
        $program_rpjmd->sasaran_id = $request->sasaran_id;
        $program_rpjmd->status_program = $request->status_program;
        $program_rpjmd->opd_id = $request->opd_id;
        $program_rpjmd->save();

        return response()->json(['success' => 'berhasil']);
    }

    public function edit($id)
    {
        $program_rpjmd = ProgramRpjmd::find($id);
        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $program_rpjmd->sasaran_id)->latest()->first();
        if($cek_perubahan_sasaran)
        {
            $tujuan_id = $cek_perubahan_sasaran->tujuan_id;
            $kode_sasaran = $cek_perubahan_sasaran->kode;
            $deskripsi_sasaran = $cek_perubahan_sasaran->deskripsi;
        } else {
            $sasaran = Sasaran::find($id);
            $tujuan_id = $sasaran->tujuan_id;
            $kode_sasaran = $sasaran->kode;
            $deskripsi_sasaran = $sasaran->deskripsi;
        }

        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $tujuan_id)->latest()->first();
        if($cek_perubahan_tujuan)
        {
            $misi_id = $cek_perubahan_tujuan->misi_id;
            $kode_tujuan = $cek_perubahan_tujuan->kode;
            $deskripsi_tujuan = $cek_perubahan_tujuan->deskripsi;
        } else {
            $tujuan = Tujuan::find($tujuan_id);
            $misi_id = $tujuan->misi_id;
            $kode_tujuan = $tujuan->kode;
            $deskripsi_tujuan = $tujuan->deskripsi;
        }

        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
        if($cek_perubahan_misi)
        {
            $kode_misi = $cek_perubahan_misi->kode;
            $deskripsi_misi =  $cek_perubahan_misi->deskripsi;
        } else {
            $misi = Misi::find($misi_id);
            $kode_misi = $misi->kode;
            $deskripsi_misi = $misi->deskripsi;
        }

        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_rpjmd->program_id)->latest()->first();
        if($cek_perubahan_program)
        {
            $urusan_id = $cek_perubahan_program->urusan_id;
        } else {
            $program = Program::find($program_rpjmd->program_id);
            $urusan_id = $program->urusan_id;
        }

        $array = [
            'sasaran' => $kode_sasaran . '. ' . $deskripsi_sasaran,
            'tujuan' => $kode_tujuan . '. '. $deskripsi_tujuan,
            'misi' => $kode_misi . '. '.$deskripsi_misi,
            'sasaran_id' => $program_rpjmd->sasaran_id,
            'program_id' => $program_rpjmd->program_id,
            'urusan_id' => $urusan_id,
            'status_program' => $program_rpjmd->status_program,
            'opd_id' => $program_rpjmd->opd_id,
        ];

        return response()->json(['result' => $array]);
    }

    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sasaran_id' => 'required',
            'urusan_id' => 'required',
            'program_id' => 'required',
            'status_program' => 'required',
            'opd_id' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_rpjmd = ProgramRpjmd::find($request->hidden_id);
        $program_rpjmd->program_id = $request->program_id;
        $program_rpjmd->sasaran_id = $request->sasaran_id;
        $program_rpjmd->status_program = $request->status_program;
        $program_rpjmd->opd_id = $request->opd_id;
        $program_rpjmd->save();

        return response()->json(['success' => 'berhasil']);
    }

    public function destroy($id)
    {
        ProgramRpjmd::find($id)->delete();

        return response()->json(['success' => 'Berhasil']);
    }
}
