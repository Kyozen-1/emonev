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

class RenstraController extends Controller
{
    public function index()
    {
        $get_misis = Misi::all();
        $misis = [];
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->latest()->first();
            if($cek_perubahan_misi)
            {
                $misis[] = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi
                ];
            } else {
                $misi = Misi::find($get_misi->id);
                $misis[] = [
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

    public function tambah_target_rp_pertahun_sasaran(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'target_rp_pertahun_sasaran_tahun' => 'required',
            'target_rp_pertahun_sasaran_sasaran_indikator_id' => 'required',
            'target_rp_pertahun_sasaran_target' => 'required',
            'target_rp_pertahun_sasaran_rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $target_rp_pertahun_sasaran = new TargetRpPertahunSasaran;
        $target_rp_pertahun_sasaran->pivot_sasaran_indikator_id = $request->target_rp_pertahun_sasaran_sasaran_indikator_id;
        $target_rp_pertahun_sasaran->target = $request->target_rp_pertahun_sasaran_target;
        $target_rp_pertahun_sasaran->rp = $request->target_rp_pertahun_sasaran_rp;
        $target_rp_pertahun_sasaran->tahun = $request->target_rp_pertahun_sasaran_tahun;
        $target_rp_pertahun_sasaran->save();

        return response()->json(['success' => 'Berhasil menyimpan']);
    }

    public function edit_target_rp_pertahun_sasaran($id)
    {
        $data = TargetRpPertahunSasaran::find($id);
        return response()->json(['result' => $data]);
    }

    public function update_target_rp_pertahun_sasaran(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'target_rp_pertahun_sasaran_target' => 'required',
            'target_rp_pertahun_sasaran_rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $target_rp_pertahun_sasaran = TargetRpPertahunSasaran::find($request->target_rp_pertahun_sasaran_hidden_id);
        $target_rp_pertahun_sasaran->target = $request->target_rp_pertahun_sasaran_target;
        $target_rp_pertahun_sasaran->rp = $request->target_rp_pertahun_sasaran_rp;
        $target_rp_pertahun_sasaran->save();

        return response()->json(['success' => 'Berhasil merubah']);
    }
}
