<?php

namespace App\Http\Controllers\Opd\Renja;

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
use Auth;
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
use App\Models\TargetRpPertahunRenstraKegiatan;
use App\Models\SasaranIndikatorKinerja;
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
use App\Models\MasterTw;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\SubKegiatanTwRealisasi;

class ProgramTwController extends Controller
{
    public function tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'program_target_satuan_rp_realisasi_id' => 'required',
            'tw_id' => 'required',
            'realisasi' => 'required',
            'realisasi_rp' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_tw_realisasi = new ProgramTwRealisasi;
        $program_tw_realisasi->program_target_satuan_rp_realisasi_id = $request->program_target_satuan_rp_realisasi_id;
        $program_tw_realisasi->tw_id = $request->tw_id;
        $program_tw_realisasi->realisasi = $request->realisasi;
        $program_tw_realisasi->realisasi_rp = $request->realisasi_rp;
        $program_tw_realisasi->save();

        return response()->json(['success' => 'Berhasil menambahkan data']);
    }

    public function ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'program_tw_realisasi_id' => 'required',
            'program_edit_realisasi' => 'required',
            'program_edit_realisasi_rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_tw_realisasi = ProgramTwRealisasi::find($request->program_tw_realisasi_id);
        $program_tw_realisasi->realisasi = $request->program_edit_realisasi;
        $program_tw_realisasi->realisasi_rp = $request->program_edit_realisasi_rp;
        $program_tw_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubah Realisasi Program');
        return redirect()->route('opd.renja.index');
    }

    public function target_satuan_realisasi_ubah(Request $request)
    {
        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::find($request->program_target_satuan_rp_realisasi_id);
        $program_target_satuan_rp_realisasi->target_rp_renja = $request->program_edit_target_rp_renja;
        $program_target_satuan_rp_realisasi->target_anggaran_perubahan = $request->program_edit_target_anggaran_perubahan;
        $program_target_satuan_rp_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubah Data');
        return redirect()->route('opd.renja.index');
    }
}
