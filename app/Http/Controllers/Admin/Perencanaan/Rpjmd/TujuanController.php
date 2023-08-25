<?php

namespace App\Http\Controllers\Admin\Perencanaan\Rpjmd;

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
use App\Models\TujuanPdRealisasiRenja;
use App\Models\SasaranPdRealisasiRenja;
use App\Models\SasaranPdProgramRpjmd;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\SubKegiatanIndikatorKinerja;
use App\Models\OpdSubKegiatanIndikatorKinerja;
use App\Models\SubKegiatanTargetSatuanRpRealisasi;
use App\Models\SubKegiatanTwRealisasi;
use App\Models\TujuanIndikatorKinerja;
use App\Models\TujuanTargetSatuanRpRealisasi;
use App\Models\SasaranTargetSatuanRpRealisasi;
use App\Models\TujuanTwRealisasi;
use App\Models\SasaranTwRealisasi;

class TujuanController extends Controller
{
    public function tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_target_satuan_rp_realisasi_id' => 'required',
            'tw_id' => 'required',
            'realisasi' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $tujuan_tw_realisasi = new TujuanTwRealisasi;
        $tujuan_tw_realisasi->tujuan_target_satuan_rp_realisasi_id = $request->tujuan_target_satuan_rp_realisasi_id;
        $tujuan_tw_realisasi->tw_id = $request->tw_id;
        $tujuan_tw_realisasi->realisasi = $request->realisasi;
        $tujuan_tw_realisasi->save();

        return response()->json(['success' => 'Berhasil menambahkan data']);
    }

    public function ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_tw_realisasi_id' => 'required',
            'tujuan_edit_realisasi' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $tujuan_tw_realisasi = TujuanTwRealisasi::find($request->tujuan_tw_realisasi_id);
        $tujuan_tw_realisasi->realisasi = $request->tujuan_edit_realisasi;
        $tujuan_tw_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubah Realisasi Tujuan');
        return redirect()->route('admin.perencanaan.index');
    }
}
