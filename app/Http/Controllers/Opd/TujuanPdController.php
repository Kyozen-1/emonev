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
use App\Models\TujuanPd;
use App\Models\PivotPerubahanTujuanPd;
use App\Models\Tujuan;
use App\Models\PivotPerubahanTujuan;
use App\Models\TujuanPdIndikatorKinerja;
use App\Models\TujuanPdTargetSatuanRpRealisasi;

class TujuanPdController extends Controller
{
    public function tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tambah_tujuan_pd_tujuan_id' => 'required',
            'tambah_tujuan_pd_kode' => 'required',
            'tambah_tujuan_pd_deskripsi' => 'required',
            'tambah_tujuan_pd_tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $tujuan_pd = new TujuanPd;
        $tujuan_pd->tujuan_id = $request->tambah_tujuan_pd_tujuan_id;
        $tujuan_pd->kode = $request->tambah_tujuan_pd_kode;
        $tujuan_pd->deskripsi = $request->tambah_tujuan_pd_deskripsi;
        $tujuan_pd->opd_id = Auth::user()->opd->opd_id;
        $tujuan_pd->tahun_perubahan = $request->tambah_tujuan_pd_tahun_perubahan;
        $tujuan_pd->save();

        Alert::success('Berhasil', 'Berhasil menambahkan Tujuan PD');
        return redirect()->route('opd.renstra.index');
    }

    public function edit($id)
    {
        $data = TujuanPd::find($id);
        $array = [];
        $cek_perubahan_tujuan_pd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $id)->latest()->first();
        if($cek_perubahan_tujuan_pd)
        {
            $array = [
                'kode' => $cek_perubahan_tujuan_pd->kode,
                'deskripsi' => $cek_perubahan_tujuan_pd->deskripsi,
                'tahun_perubahan' => $cek_perubahan_tujuan_pd->tahun_perubahan,
            ];
        } else {
            $array = [
                'kode' => $data->kode,
                'deskripsi' => $data->deskripsi,
                'tahun_perubahan' => $data->tahun_perubahan,
            ];
        }

        return response()->json(['result' => $array]);
    }

    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'edit_tujuan_pd_tujuan_pd_id' => 'required',
            'edit_tujuan_pd_tujuan_id' => 'required',
            'edit_tujuan_pd_kode' => 'required',
            'edit_tujuan_pd_deskripsi' => 'required',
            'edit_tujuan_pd_tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $cek_tujuan_pd = TujuanPd::where('id', $request->edit_tujuan_pd_tujuan_pd_id)
                            ->where('tahun_perubahan', $request->edit_tujuan_pd_tahun_perubahan)
                            ->first();
        if($cek_tujuan_pd)
        {
            $update_tujuan_pd = TujuanPd::find($request->edit_tujuan_pd_tujuan_pd_id);
            $update_tujuan_pd->kode = $request->edit_tujuan_pd_kode;
            $update_tujuan_pd->deskripsi = $request->edit_tujuan_pd_deskripsi;
            $update_tujuan_pd->tahun_perubahan = $request->edit_tujuan_pd_tahun_perubahan;
            $update_tujuan_pd->save();
        } else {
            $cek_pivot = PivotPerubahanTujuanPd::where('tujuan_pd_id', $request->edit_tujuan_pd_tujuan_pd_id)
                            ->where('tahun_perubahan', $request->edit_tujuan_pd_tahun_perubahan)->first();
            if($cek_pivot)
            {
                PivotPerubahanTujuanPd::find($cek_pivot->id)->delete();

                $pivot = new PivotPerubahanTujuanPd;
                $pivot->tujuan_pd_id = $request->edit_tujuan_pd_tujuan_pd_id;
                $pivot->tujuan_id = $request->edit_tujuan_pd_tujuan_id;
                $pivot->kode = $request->edit_tujuan_pd_kode;
                $pivot->deskripsi = $request->edit_tujuan_pd_deskripsi;
                $pivot->opd_id = Auth::user()->opd->opd_id;
                $pivot->tahun_perubahan = $request->edit_tujuan_pd_tahun_perubahan;
                $pivot->save();
            } else {
                $pivot = new PivotPerubahanTujuanPd;
                $pivot->tujuan_pd_id = $request->edit_tujuan_pd_tujuan_pd_id;
                $pivot->tujuan_id = $request->edit_tujuan_pd_tujuan_id;
                $pivot->kode = $request->edit_tujuan_pd_kode;
                $pivot->deskripsi = $request->edit_tujuan_pd_deskripsi;
                $pivot->opd_id = Auth::user()->opd->opd_id;
                $pivot->tahun_perubahan = $request->edit_tujuan_pd_tahun_perubahan;
                $pivot->save();
            }
        }

        Alert::success('Berhasil', 'Berhasil merubah data Tujuan PD');
        return redirect()->route('opd.renstra.index');
    }

    public function hapus(Request $request)
    {
        $pivot_perubahan_tujuan_pds = PivotPerubahanTujuanPd::where('tujuan_pd_id', $request->tujuan_pd_id)->get();
        foreach ($pivot_perubahan_tujuan_pds as $pivot_perubahan_tujuan_pd) {
            PivotPerubahanTujuanPd::find($pivot_perubahan_tujuan_pd->id)->delete();
        }

        $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $request->tujuan_pd_id)->get();
        foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
            $tujuan_pd_target_satuan_rp_realisasis = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->get();
            foreach ($tujuan_pd_target_satuan_rp_realisasis as $tujuan_pd_target_satuan_rp_realisasi) {
                TujuanPdTargetSatuanRpRealisasi::find($tujuan_pd_target_satuan_rp_realisasi->id)->delete();
            }

            TujuanPdIndikatorKinerja::find($tujuan_pd_indikator_kinerja->id)->delete();
        }

        TujuanPd::find($request->tujuan_pd_id)->delete();

        return response()->json(['success' => 'Berhasil menghapus data']);
    }

    public function indikator_kinerja_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_tujuan_pd_tujuan_pd_id' => 'required',
            'indikator_kinerja_tujuan_pd_deskripsi' => 'required',
            'indikator_kinerja_tujuan_pd_satuan' => 'required',
            'indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal' => 'required',
            'indikator_kinerja_status_indikator' => 'required'
        ]);

        if($errors -> fails())
        {
            return back()->with('errors', $errors->message()->all())->withInput();
        }

        $tujuan_pd_indikator_kinerja = new TujuanPdIndikatorKinerja;
        $tujuan_pd_indikator_kinerja->tujuan_pd_id = $request->indikator_kinerja_tujuan_pd_tujuan_pd_id;
        $tujuan_pd_indikator_kinerja->deskripsi = $request->indikator_kinerja_tujuan_pd_deskripsi;
        $tujuan_pd_indikator_kinerja->satuan = $request->indikator_kinerja_tujuan_pd_satuan;
        $tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal = $request->indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal;
        $tujuan_pd_indikator_kinerja->status_indikator = $request->indikator_kinerja_tujuan_pd_status_indikator;
        $tujuan_pd_indikator_kinerja->save();

        Alert::success('Berhasil', 'Berhasil menambahkan Indikator Kinerja Tujuan PD');
        return redirect()->route('opd.renstra.index');
    }

    public function indikator_kinerja_edit($id)
    {
        $data = TujuanPdIndikatorKinerja::find($id);
        return response()->json(['result' => $data]);
    }

    public function indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_tujuan_pd_id' => 'required',
            'edit_indikator_kinerja_tujuan_pd_deskripsi' => 'required',
            'edit_indikator_kinerja_tujuan_pd_satuan' => 'required',
            'edit_indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal' => 'required',
            'edit_indikator_kinerja_tujuan_pd_status_indikator' => 'required'
        ]);

        if($errors -> fails())
        {
            return back()->with('errors', $errors->message()->all())->withInput();
        }

        $tujuan_pd_indikator_kinerja = TujuanPdIndikatorKinerja::find($request->indikator_kinerja_tujuan_pd_id);
        $tujuan_pd_indikator_kinerja->deskripsi = $request->edit_indikator_kinerja_tujuan_pd_deskripsi;
        $tujuan_pd_indikator_kinerja->satuan = $request->edit_indikator_kinerja_tujuan_pd_satuan;
        $tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal = $request->edit_indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal;
        $tujuan_pd_indikator_kinerja->status_indikator = $request->edit_indikator_kinerja_tujuan_pd_status_indikator;
        $tujuan_pd_indikator_kinerja->save();

        Alert::success('Berhasil', 'Berhasil Merubah Indikator Kinerja Tujuan PD');
        return redirect()->route('opd.renstra.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $tujuan_pd_target_satuan_rp_realisasis = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $request->tujuan_pd_indikator_kinerja_id)->get();
        foreach ($tujuan_pd_target_satuan_rp_realisasis as $tujuan_pd_target_satuan_rp_realisasi) {
            TujuanPdTargetSatuanRpRealisasi::find($tujuan_pd_target_satuan_rp_realisasi->id)->delete();
        }

        TujuanPdIndikatorKinerja::find($request->tujuan_pd_indikator_kinerja_id)->delete();

        return response()->json(['success' => 'Berhasil menghapus data']);
    }

    public function target_satuan_realisasi_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'tujuan_pd_indikator_kinerja_id' => 'required',
            'target' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $tujuan_pd_target_satuan_rp_realisasi = new TujuanPdTargetSatuanRpRealisasi;
        $tujuan_pd_target_satuan_rp_realisasi->tujuan_pd_indikator_kinerja_id = $request->tujuan_pd_indikator_kinerja_id;
        $tujuan_pd_target_satuan_rp_realisasi->target = $request->target;
        $tujuan_pd_target_satuan_rp_realisasi->tahun = $request->tahun;
        $tujuan_pd_target_satuan_rp_realisasi->save();

        return response()->json(['success' => 'Berhasil menambahkan data']);
    }

    public function target_satuan_realisasi_ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_pd_target_satuan_rp_realisasi' => 'required',
            'tujuan_pd_edit_target' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::find($request->tujuan_pd_target_satuan_rp_realisasi);
        $tujuan_pd_target_satuan_rp_realisasi->target = $request->tujuan_pd_edit_target;
        $tujuan_pd_target_satuan_rp_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubah Target Tujuan PD');
        return redirect()->route('opd.renstra.index');
    }
}
