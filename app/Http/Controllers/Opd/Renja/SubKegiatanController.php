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
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\SubKegiatanIndikatorKinerja;
use App\Models\OpdSubKegiatanIndikatorKinerja;
use App\Models\SubKegiatanTargetSatuanRpRealisasi;

class SubKegiatanController extends Controller
{
    public function indikator_kinerja_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_sub_kegiatan_sub_kegiatan_id' => 'required',
            'indikator_kinerja_sub_kegiatan_deskripsi' => 'required',
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $deskripsis = json_decode($request->indikator_kinerja_sub_kegiatan_deskripsi, true);
        foreach ($deskripsis as $deskripsi) {
            $sub_kegiatan_indikator_kinerja = new SubKegiatanIndikatorKinerja;
            $sub_kegiatan_indikator_kinerja->sub_kegiatan_id = $request->indikator_kinerja_sub_kegiatan_sub_kegiatan_id;
            $sub_kegiatan_indikator_kinerja->deskripsi = $deskripsi['value'];
            $sub_kegiatan_indikator_kinerja->save();

            $opd_sub_kegiatan_indikator_kinerja = new OpdSubKegiatanIndikatorKinerja;
            $opd_sub_kegiatan_indikator_kinerja->sub_kegiatan_indikator_kinerja_id = $sub_kegiatan_indikator_kinerja->id;
            $opd_sub_kegiatan_indikator_kinerja->opd_id = Auth::user()->opd->opd_id;
            $opd_sub_kegiatan_indikator_kinerja->save();
        }

        Alert::success('Berhasil', 'Berhasil menambahkan Indikator Kinerja Indikator');
        return redirect()->route('opd.renja.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $request->sub_kegiatan_indikator_kinerja_id)->get();
        foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja) {
            $sub_kegiatan_target_satuan_rp_realisasis = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)->get();
            foreach ($sub_kegiatan_target_satuan_rp_realisasis as $sub_kegiatan_target_satuan_rp_realisasi) {
                $sub_kegiatan_tw_realisasis = SubKegiatanTwRealisasis::where('sub_kegiatan_target_satuan_rp_realisasi_id', $sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                foreach ($sub_kegiatan_tw_realisasis as $sub_kegiatan_tw_realisasi) {
                    SubKegiatanTwRealisasis::find($sub_kegiatan_tw_realisasi->id)->delete();
                }
                SubKegiatanTargetSatuanRpRealisasi::find($sub_kegiatan_target_satuan_rp_realisasi->id)->delete();
            }
            OpdSubKegiatanIndikatorKinerja::find($opd_sub_kegiatan_indikator_kinerja->id)->delete();
        }
        SubKegiatanIndikatorKinerja::find($request->sub_kegiatan_indikator_kinerja_id)->delete();

        return response()->json(['success' => 'Berhasil menghapus data']);
    }

    public function target_satuan_realisasi_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'opd_sub_kegiatan_indikator_kinerja_id' => 'required',
            'target' => 'required',
            'satuan' => 'required',
            'target_rp' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        $sub_kegiatan_target_satuan_rp_realisasi = new SubKegiatanTargetSatuanRpRealisasi;
        $sub_kegiatan_target_satuan_rp_realisasi->opd_sub_kegiatan_indikator_kinerja_id = $request->opd_sub_kegiatan_indikator_kinerja_id;
        $sub_kegiatan_target_satuan_rp_realisasi->target = $request->target;
        $sub_kegiatan_target_satuan_rp_realisasi->satuan = $request->satuan;
        $sub_kegiatan_target_satuan_rp_realisasi->target_rp = $request->target_rp;
        $sub_kegiatan_target_satuan_rp_realisasi->tahun = $request->tahun;
        $sub_kegiatan_target_satuan_rp_realisasi->save();

        return response()->json(['success' => 'Berhasil menambahkan data']);
    }

    public function target_satuan_realisasi_ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sub_kegiatan_target_satuan_rp_realisasi' => 'required',
            'sub_kegiatan_edit_target' => 'required',
            'sub_kegiatan_edit_satuan' => 'required',
            'sub_kegiatan_edit_target_rp' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::find($request->sub_kegiatan_target_satuan_rp_realisasi);
        $sub_kegiatan_target_satuan_rp_realisasi->target = $request->sub_kegiatan_edit_target;
        $sub_kegiatan_target_satuan_rp_realisasi->satuan = $request->sub_kegiatan_edit_satuan;
        $sub_kegiatan_target_satuan_rp_realisasi->target_rp = $request->sub_kegiatan_edit_target_rp;
        $sub_kegiatan_target_satuan_rp_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubah Target Sub Kegiatan');
        return redirect()->route('opd.renja.index');
    }

    public function tw_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tw_id' => 'required',
            'sub_kegiatan_target_satuan_rp_realisasi_id' => 'required',
            'realisasi' => 'required',
            'realisasi_rp' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        $sub_kegiatan_tw_realisasi = new SubKegiatanTwRealisasi;
        $sub_kegiatan_tw_realisasi->sub_kegiatan_target_satuan_rp_realisasi_id = $request->sub_kegiatan_target_satuan_rp_realisasi_id;
        $sub_kegiatan_tw_realisasi->tw_id = $request->tw_id;
        $sub_kegiatan_tw_realisasi->realisasi = $request->realisasi;
        $sub_kegiatan_tw_realisasi->realisasi_rp = $request->realisasi_rp;
        $sub_kegiatan_tw_realisasi->save();

        return response()->json(['success' => 'Berhasil menambahkan data']);
    }

    public function tw_ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sub_kegiatan_tw_realisasi_id' => 'required',
            'sub_kegiatan_edit_realisasi' => 'required',
            'sub_kegiatan_edit_realisasi_rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::find($request->sub_kegiatan_tw_realisasi_id);
        $sub_kegiatan_tw_realisasi->realisasi = $request->sub_kegiatan_edit_realisasi;
        $sub_kegiatan_tw_realisasi->realisasi_rp = $request->sub_kegiatan_edit_realisasi_rp;
        $sub_kegiatan_tw_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubah Target Sub Kegiatan');
        return redirect()->route('opd.renja.index');
    }
}