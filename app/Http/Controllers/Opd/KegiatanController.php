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
use DataTables;
use Excel;
use Auth;
use App\Models\Tujuan;
use App\Models\PivotPerubahanTujuan;
use App\Models\KegiatanIndikatorKinerja;
use App\Models\OpdKegiatanIndikatorKinerja;
use App\Models\KegiatanTargetSatuanRpRealisasi;

class KegiatanController extends Controller
{
    public function indikator_kinerja_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_kegiatan_kegiatan_id' => 'required',
            'indikator_kinerja_kegiatan_deskripsi' => 'required',
            'indikator_kinerja_kegiatan_satuan' => 'required',
            'indikator_kinerja_kegiatan_kondisi_target_kinerja_awal' => 'required',
            'indikator_kinerja_kegiatan_kondisi_target_anggaran_awal' => 'required',
            'indikator_kinerja_kegiatan_status_indikator' => 'required'
        ]);

        if($errors -> fails())
        {
            return back()->with('errors', $errors->message()->all())->withInput();
        }

        $kegiatan_indikator_kinerja = new KegiatanIndikatorKinerja;
        $kegiatan_indikator_kinerja->kegiatan_id = $request->indikator_kinerja_kegiatan_kegiatan_id;
        $kegiatan_indikator_kinerja->deskripsi = $request->indikator_kinerja_kegiatan_deskripsi;
        $kegiatan_indikator_kinerja->satuan = $request->indikator_kinerja_kegiatan_satuan;
        $kegiatan_indikator_kinerja->kondisi_target_kinerja_awal = $request->indikator_kinerja_kegiatan_kondisi_target_kinerja_awal;
        $kegiatan_indikator_kinerja->kondisi_target_anggaran_awal = $request->indikator_kinerja_kegiatan_kondisi_target_anggaran_awal;
        $kegiatan_indikator_kinerja->status_indikator = $request->indikator_kinerja_kegiatan_status_indikator;
        $kegiatan_indikator_kinerja->save();

        $opd = new OpdKegiatanIndikatorKinerja;
        $opd->kegiatan_indikator_kinerja_id = $kegiatan_indikator_kinerja->id;
        $opd->opd_id = Auth::user()->opd->opd_id;
        $opd->save();

        Alert::success('Berhasil', 'Berhasil menambahkan Indikator Kinerja Kegiatan');
        return redirect()->route('opd.renstra.index');
    }

    public function indikator_kinerja_edit($id)
    {
        $data = KegiatanIndikatorKinerja::find($id);

        return response()->json(['result' => $data]);
    }

    public function indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_kegiatan_id' => 'required',
            'edit_indikator_kinerja_kegiatan_deskripsi' => 'required',
            'edit_indikator_kinerja_kegiatan_satuan' => 'required',
            'edit_indikator_kinerja_kegiatan_kondisi_target_kinerja_awal' => 'required',
            'edit_indikator_kinerja_kegiatan_kondisi_target_anggaran_awal' => 'required',
            'edit_indikator_kinerja_kegiatan_status_indikator' => 'required'
        ]);

        if($errors -> fails())
        {
            return back()->with('errors', $errors->message()->all())->withInput();
        }

        $kegiatan_indikator_kinerja = KegiatanIndikatorKinerja::find($request->indikator_kinerja_kegiatan_id);
        $kegiatan_indikator_kinerja->deskripsi = $request->edit_indikator_kinerja_kegiatan_deskripsi;
        $kegiatan_indikator_kinerja->satuan = $request->edit_indikator_kinerja_kegiatan_satuan;
        $kegiatan_indikator_kinerja->kondisi_target_kinerja_awal = $request->edit_indikator_kinerja_kegiatan_kondisi_target_kinerja_awal;
        $kegiatan_indikator_kinerja->kondisi_target_anggaran_awal = $request->edit_indikator_kinerja_kegiatan_kondisi_target_anggaran_awal;
        $kegiatan_indikator_kinerja->status_indikator = $request->edit_indikator_kinerja_kegiatan_status_indikator;
        $kegiatan_indikator_kinerja->save();

        Alert::success('Berhasil', 'Berhasil merubah Indikator Kinerja Kegiatan');
        return redirect()->route('opd.renstra.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $kegiatan_indikator = KegiatanIndikatorKinerja::find($request->kegiatan_indikator_kinerja_id);

        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator->id)->get();

        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
            $kegiatan_target_satuan_rp_realisasis = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)->get();
            foreach ($kegiatan_target_satuan_rp_realisasis as $kegiatan_target_satuan_rp_realisasi) {
                KegiatanTargetSatuanRpRealisasi::find($kegiatan_target_satuan_rp_realisasi->id)->delete();
            }
            OpdKegiatanIndikatorKinerja::find($get_opd_kegiatan_indikator_kinerja->id)->delete();
        }

        $kegiatan_indikator = $kegiatan_indikator->delete();

        return response()->json(['success' => 'Berhasil Menghapus Indikator Kinerja untuk Program']);
    }

    public function target_satuan_realisasi_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'kegiatan_indikator_kinerja_id' => 'required',
            'target' => 'required',
            'target_rp' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $get_opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $request->kegiatan_indikator_kinerja_id)
                                                ->where('opd_id', Auth::user()->opd->opd_id)->first();

        $kegiatan_target_satuan_rp_realisasi = new KegiatanTargetSatuanRpRealisasi;
        $kegiatan_target_satuan_rp_realisasi->opd_kegiatan_indikator_kinerja_id = $get_opd_kegiatan_indikator_kinerja->id;
        $kegiatan_target_satuan_rp_realisasi->target = $request->target;
        $kegiatan_target_satuan_rp_realisasi->target_rp = $request->target_rp;
        $kegiatan_target_satuan_rp_realisasi->tahun = $request->tahun;
        $kegiatan_target_satuan_rp_realisasi->save();

        return response()->json(['success' => 'Berhasil menambahkan data']);
    }

    public function target_satuan_realisasi_ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'kegiatan_target_satuan_rp_realisasi' => 'required',
            'kegiatan_edit_target' => 'required',
            'kegiatan_edit_target_rp' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::find($request->kegiatan_target_satuan_rp_realisasi);
        $kegiatan_target_satuan_rp_realisasi->target = $request->kegiatan_edit_target;
        $kegiatan_target_satuan_rp_realisasi->target_rp = $request->kegiatan_edit_target_rp;
        $kegiatan_target_satuan_rp_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubah Target Kegiatan');
        return redirect()->route('opd.renstra.index');
    }
}
