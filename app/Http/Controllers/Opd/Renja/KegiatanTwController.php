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
use App\Models\OpdKegiatanIndikatorKinerja;

class KegiatanTwController extends Controller
{
    public function tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'kegiatan_target_satuan_rp_realisasi_id' => 'required',
            'tw_id' => 'required',
            'realisasi' => 'required',
            'realisasi_rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $kegiatan_tw_realisasi = new KegiatanTwRealisasi;
        $kegiatan_tw_realisasi->kegiatan_target_satuan_rp_realisasi_id = $request->kegiatan_target_satuan_rp_realisasi_id;
        $kegiatan_tw_realisasi->tw_id = $request->tw_id;
        $kegiatan_tw_realisasi->realisasi = $request->realisasi;
        $kegiatan_tw_realisasi->realisasi_rp = $request->realisasi_rp;
        $kegiatan_tw_realisasi->save();

        return response()->json(['success' => 'Berhasil menambahkan data']);
    }

    public function ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'kegiatan_tw_realisasi_id' => 'required',
            'kegiatan_edit_realisasi' => 'required',
            'kegiatan_edit_realisasi_rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $kegiatan_tw_realisasi = KegiatanTwRealisasi::find($request->kegiatan_tw_realisasi_id);
        $kegiatan_tw_realisasi->realisasi = $request->kegiatan_edit_realisasi;
        $kegiatan_tw_realisasi->realisasi_rp = $request->kegiatan_edit_realisasi_rp;
        $kegiatan_tw_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubah Target Tujuan PD');
        return redirect()->route('opd.renja.index');
    }
}
