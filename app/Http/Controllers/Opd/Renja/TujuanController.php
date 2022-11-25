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
use App\Models\TujuanPdRealisasiRenja;
use App\Models\SasaranPdRealisasiRenja;
use App\Models\SasaranPdProgramRpjmd;

class TujuanController extends Controller
{
    public function realisasi_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_pd_target_satuan_rp_realisasi_id' => 'required',
            'realisasi' => 'required'
        ]);

        if($errors -> fails())
        {
            // return back()->with('errors', $errors->message()->all())->withInput();
            return response()->json(['errors' => $errors->message()->all()]);
        }

        $tujuan_pd_realisasi_renja = new TujuanPdRealisasiRenja;
        $tujuan_pd_realisasi_renja->tujuan_pd_target_satuan_rp_realisasi_id = $request->tujuan_pd_target_satuan_rp_realisasi_id;
        $tujuan_pd_realisasi_renja->realisasi = $request->realisasi;
        $tujuan_pd_realisasi_renja->save();

        return response()->json(['success' => 'Berhasil menambah realisasi']);
    }

    public function realisasi_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_pd_realisasi_renja_id' => 'required',
            'tujuan_pd_edit_realisasi' => 'required'
        ]);

        if($errors -> fails())
        {
            return back()->with('errors', $errors->message()->all())->withInput();
        }

        $tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::find($request->tujuan_pd_realisasi_renja_id);
        $tujuan_pd_realisasi_renja->realisasi = $request->tujuan_pd_edit_realisasi;
        $tujuan_pd_realisasi_renja->save();

        Alert::success('Berhasil', 'Berhasil merubah realisasi Tujuan PD');
        return redirect()->route('opd.renja.index');
    }
}
