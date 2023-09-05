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
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\ProgramRpjmd;
use App\Models\SasaranPd;
use App\Models\PivotPerubahanSasaranPd;
use App\Models\Sasaran;
use App\Models\PivotPerubahanSasaran;
use App\Models\SasaranPdIndikatorKinerja;
use App\Models\SasaranPdTargetSatuanRpRealisasi;
use App\Models\SasaranPdProgramRpjmd;

class SasaranPdController extends Controller
{
    public function tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tambah_sasaran_pd_sasaran_id' => 'required',
            'tambah_sasaran_pd_kode' => 'required',
            'tambah_sasaran_pd_deskripsi' => 'required',
            'tambah_sasaran_pd_tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $sasaran_pd = new SasaranPd;
        $sasaran_pd->sasaran_id = $request->tambah_sasaran_pd_sasaran_id;
        $sasaran_pd->kode = $request->tambah_sasaran_pd_kode;
        $sasaran_pd->deskripsi = $request->tambah_sasaran_pd_deskripsi;
        $sasaran_pd->opd_id = Auth::user()->opd->opd_id;
        $sasaran_pd->tahun_perubahan = $request->tambah_sasaran_pd_tahun_perubahan;
        $sasaran_pd->save();

        Alert::success('Berhasil', 'Berhasil menambahkan Sasaran PD');
        return redirect()->route('opd.renstra.index');
    }

    public function edit($id)
    {
        $data = SasaranPd::find($id);
        $array = [];
        $cek_perubahan_sasaran_pd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $id)->latest()->first();
        if($cek_perubahan_sasaran_pd)
        {
            $array = [
                'kode' => $cek_perubahan_sasaran_pd->kode,
                'deskripsi' => $cek_perubahan_sasaran_pd->deskripsi,
                'tahun_perubahan' => $cek_perubahan_sasaran_pd->tahun_perubahan,
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
            'edit_sasaran_pd_sasaran_pd_id' => 'required',
            'edit_sasaran_pd_sasaran_id' => 'required',
            'edit_sasaran_pd_kode' => 'required',
            'edit_sasaran_pd_deskripsi' => 'required',
            'edit_sasaran_pd_tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $cek_sasaran_pd = SasaranPd::where('id', $request->edit_sasaran_pd_sasaran_pd_id)
                            ->where('tahun_perubahan', $request->edit_sasaran_pd_tahun_perubahan)
                            ->first();
        if($cek_sasaran_pd)
        {
            $update_sasaran_pd = SasaranPd::find($request->edit_sasaran_pd_sasaran_pd_id);
            $update_sasaran_pd->kode = $request->edit_sasaran_pd_kode;
            $update_sasaran_pd->deskripsi = $request->edit_sasaran_pd_deskripsi;
            $update_sasaran_pd->tahun_perubahan = $request->edit_sasaran_pd_tahun_perubahan;
            $update_sasaran_pd->save();
        } else {
            $cek_pivot = PivotPerubahanSasaranPd::where('sasaran_pd_id', $request->edit_sasaran_pd_sasaran_pd_id)
                            ->where('tahun_perubahan', $request->edit_sasaran_pd_tahun_perubahan)->first();
            if($cek_pivot)
            {
                PivotPerubahanSasaranPd::find($cek_pivot->id)->delete();

                $pivot = new PivotPerubahanSasaranPd;
                $pivot->sasaran_pd_id = $request->edit_sasaran_pd_sasaran_pd_id;
                $pivot->sasaran_id = $request->edit_sasaran_pd_sasaran_id;
                $pivot->kode = $request->edit_sasaran_pd_kode;
                $pivot->deskripsi = $request->edit_sasaran_pd_deskripsi;
                $pivot->opd_id = Auth::user()->opd->opd_id;
                $pivot->tahun_perubahan = $request->edit_sasaran_pd_tahun_perubahan;
                $pivot->save();
            } else {
                $pivot = new PivotPerubahanSasaranPd;
                $pivot->sasaran_pd_id = $request->edit_sasaran_pd_sasaran_pd_id;
                $pivot->sasaran_id = $request->edit_sasaran_pd_sasaran_id;
                $pivot->kode = $request->edit_sasaran_pd_kode;
                $pivot->deskripsi = $request->edit_sasaran_pd_deskripsi;
                $pivot->opd_id = Auth::user()->opd->opd_id;
                $pivot->tahun_perubahan = $request->edit_sasaran_pd_tahun_perubahan;
                $pivot->save();
            }
        }

        Alert::success('Berhasil', 'Berhasil merubah data Sasaran PD');
        return redirect()->route('opd.renstra.index');
    }

    public function hapus(Request $request)
    {
        $pivot_perubahan_sasaran_pds = PivotPerubahanSasaranPd::where('sasaran_pd_id', $request->sasaran_pd_id)->get();
        foreach ($pivot_perubahan_sasaran_pds as $pivot_perubahan_sasaran_pd) {
            PivotPerubahanSasaranPd::find($pivot_perubahan_sasaran_pd->id)->delete();
        }

        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $request->sasaran_pd_id)->get();
        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
            $sasaran_pd_target_satuan_rp_realisasis = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->get();
            foreach ($sasaran_pd_target_satuan_rp_realisasis as $sasaran_pd_target_satuan_rp_realisasi) {
                SasaranPdTargetSatuanRpRealisasi::find($sasaran_pd_target_satuan_rp_realisasi->id)->delete();
            }

            SasaranPdIndikatorKinerja::find($sasaran_pd_indikator_kinerja->id)->delete();
        }

        SasaranPd::find($request->sasaran_pd_id)->delete();

        return response()->json(['success' => 'Berhasil menghapus data']);
    }

    public function indikator_kinerja_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_sasaran_pd_sasaran_pd_id' => 'required',
            'indikator_kinerja_sasaran_pd_deskripsi' => 'required',
            'indikator_kinerja_sasaran_pd_status_indikator' => 'required',
            'indikator_kinerja_sasaran_pd_satuan' => 'required',
            'indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Berhasil', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $sasaran_pd_indikator_kinerja = new SasaranPdIndikatorKinerja;
        $sasaran_pd_indikator_kinerja->sasaran_pd_id = $request->indikator_kinerja_sasaran_pd_sasaran_pd_id;
        $sasaran_pd_indikator_kinerja->deskripsi = $request->indikator_kinerja_sasaran_pd_deskripsi;
        $sasaran_pd_indikator_kinerja->status_indikator = $request->indikator_kinerja_sasaran_pd_status_indikator;
        $sasaran_pd_indikator_kinerja->satuan = $request->indikator_kinerja_sasaran_pd_satuan;
        $sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal = $request->indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal;
        $sasaran_pd_indikator_kinerja->save();

        Alert::success('Berhasil', 'Berhasil menambahkan Indikator Kinerja Sasaran PD');
        return redirect()->route('opd.renstra.index');
    }

    public function indikator_kinerja_edit($id)
    {
        $data = SasaranPdIndikatorKinerja::find($id);

        return response()->json(['result' => $data]);
    }

    public function indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_sasaran_pd_id' => 'required',
            'edit_indikator_kinerja_sasaran_pd_deskripsi' => 'required',
            'edit_indikator_kinerja_sasaran_pd_status_indikator' => 'required',
            'edit_indikator_kinerja_sasaran_pd_satuan' => 'required',
            'edit_indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Berhasil', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $sasaran_pd_indikator_kinerja = SasaranPdIndikatorKinerja::find($request->indikator_kinerja_sasaran_pd_id);
        $sasaran_pd_indikator_kinerja->deskripsi = $request->edit_indikator_kinerja_sasaran_pd_deskripsi;
        $sasaran_pd_indikator_kinerja->status_indikator = $request->edit_indikator_kinerja_sasaran_pd_status_indikator;
        $sasaran_pd_indikator_kinerja->satuan = $request->edit_indikator_kinerja_sasaran_pd_satuan;
        $sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal = $request->edit_indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal;
        $sasaran_pd_indikator_kinerja->save();

        Alert::success('Berhasil', 'Berhasil Merubah Indikator Kinerja Sasaran PD');
        return redirect()->route('opd.renstra.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $sasaran_pd_target_satuan_rp_realisasis = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $request->sasaran_pd_indikator_kinerja_id)->get();
        foreach ($sasaran_pd_target_satuan_rp_realisasis as $sasaran_pd_target_satuan_rp_realisasi) {
            SasaranPdTargetSatuanRpRealisasi::find($sasaran_pd_target_satuan_rp_realisasi->id)->delete();
        }

        SasaranPdIndikatorKinerja::find($request->sasaran_pd_indikator_kinerja_id)->delete();

        return response()->json(['success' => 'Berhasil menghapus data']);
    }

    public function target_satuan_realisasi_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'sasaran_pd_indikator_kinerja_id' => 'required',
            'target' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sasaran_pd_target_satuan_rp_realisasi = new SasaranPdTargetSatuanRpRealisasi;
        $sasaran_pd_target_satuan_rp_realisasi->sasaran_pd_indikator_kinerja_id = $request->sasaran_pd_indikator_kinerja_id;
        $sasaran_pd_target_satuan_rp_realisasi->target = $request->target;
        $sasaran_pd_target_satuan_rp_realisasi->tahun = $request->tahun;
        $sasaran_pd_target_satuan_rp_realisasi->save();

        return response()->json(['success' => 'Berhasil menambahkan data']);
    }

    public function target_satuan_realisasi_ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sasaran_pd_target_satuan_rp_realisasi' => 'required',
            'sasaran_pd_edit_target' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::find($request->sasaran_pd_target_satuan_rp_realisasi);
        $sasaran_pd_target_satuan_rp_realisasi->target = $request->sasaran_pd_edit_target;
        $sasaran_pd_target_satuan_rp_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubah Target Sasaran PD');
        return redirect()->route('opd.renstra.index');
    }

    public function sasaran_pd_program_rpjmd_get_program_rpjmd(Request $request)
    {
        $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($request){
            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($request){
                $q->whereHas('sasaran', function($q) use ($request){
                    $q->where('id', $request->sasaran_id);
                });
            });
        })->whereHas('program', function($q){
            $q->whereHas('program_indikator_kinerja', function($q){
                $q->whereHas('opd_program_indikator_kinerja', function($q){
                    $q->where('opd_id', Auth::user()->opd->opd_id);
                });
            });
        })->whereDoesntHave('sasaran_pd_program_rpjmd', function($q){
            $q->whereHas('sasaran_pd', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();

        $programs = [];

        foreach ($get_program_rpjmds as $get_program_rpjmd) {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)->latest()->first();
            if($cek_perubahan_program)
            {
                $programs[] = [
                    'id' => $get_program_rpjmd->id,
                    'deskripsi' => $cek_perubahan_program->deskripsi
                ];
            } else {
                $programs[] = [
                    'id' => $get_program_rpjmd->id,
                    'deskripsi' => $get_program_rpjmd->program->deskripsi
                ];
            }
        }

        return response()->json($programs);
    }

    public function sasaran_pd_program_rpjmd_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sasaran_pd_program_rpjmd_sasaran_pd_id' => 'required',
            'sasaran_pd_program_rpjmd_program_rpjmd_id' => 'required | array',
            'sasaran_pd_program_rpjmd_program_rpjmd_id.*' => 'required'
        ]);

        if($errors -> fails())
        {
            return back()->with('errors', $errors->message()->all())->withInput();
        }

        $program_rpjmd_id = $request->sasaran_pd_program_rpjmd_program_rpjmd_id;
        for ($i=0; $i < count($program_rpjmd_id); $i++) {
            $sasaran_pd_program_rpjmd = new SasaranPdProgramRpjmd;
            $sasaran_pd_program_rpjmd->sasaran_pd_id = $request->sasaran_pd_program_rpjmd_sasaran_pd_id;
            $sasaran_pd_program_rpjmd->program_rpjmd_id = $program_rpjmd_id[$i];
            $sasaran_pd_program_rpjmd->save();
        }

        Alert::success('Berhasil', 'Berhasil mengelompokan Program di Sasaran PD');
        return redirect()->route('opd.renstra.index');
    }

    public function sasaran_pd_program_rpjmd_hapus(Request $request)
    {
        SasaranPdProgramRpjmd::find($request->sasaran_pd_program_rpjmd_id)->delete();

        return response()->json(['success' => 'Berhasil menghapus program dari kelompok sasaran pd']);
    }
}
