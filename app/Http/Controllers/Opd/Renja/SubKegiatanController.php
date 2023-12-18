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
    public function get_data(Request $request)
    {
        $getSubKegiatans = SubKegiatan::where('kegiatan_id', $request->kegiatan_id)->get();
        $subKegiatan = [];
        foreach ($getSubKegiatans as $getSubKegiatan) {
            $cekSubKegiatan = SubKegiatan::where('id', $getSubKegiatan->id)->whereHas('sub_kegiatan_indikator_kinerja', function($q){
                $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q){
                    $q->where('opd_id', Auth::user()->opd->opd_id);
                });
            })->first();

            if(!$cekSubKegiatan)
            {
                $subKegiatan[] = [
                    'id' => $getSubKegiatan->id,
                    'deskripsi' => $getSubKegiatan->deskripsi
                ];
            }
        }
        return response()->json($subKegiatan);
    }

    public function tambah(Request $request)
    {
        $sub_kegiatan_id = $request->sub_kegiatan_id;
        for ($i=0; $i < count($sub_kegiatan_id); $i++) {
            $sub_kegiatan_indikator_kinerja = new SubKegiatanIndikatorKinerja;
            $sub_kegiatan_indikator_kinerja->sub_kegiatan_id = $sub_kegiatan_id[$i];
            $sub_kegiatan_indikator_kinerja->deskripsi = 'Silahkan Edit';
            $sub_kegiatan_indikator_kinerja->satuan = 'Silahkan Edit';
            $sub_kegiatan_indikator_kinerja->save();

            $opd_sub_kegiatan_indikator_kinerja = new OpdSubKegiatanIndikatorKinerja;
            $opd_sub_kegiatan_indikator_kinerja->sub_kegiatan_indikator_kinerja_id = $sub_kegiatan_indikator_kinerja->id;
            $opd_sub_kegiatan_indikator_kinerja->opd_id = Auth::user()->opd->opd_id;
            $opd_sub_kegiatan_indikator_kinerja->save();
        }

        Alert::success('Berhasil', 'Berhasil menambahkan Sub Kegiatan');
        return redirect()->route('opd.renja.index');
    }

    public function indikator_kinerja_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_sub_kegiatan_sub_kegiatan_id' => 'required',
            'indikator_kinerja_sub_kegiatan_deskripsi' => 'required',
            'indikator_kinerja_sub_kegiatan_satuan' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renja.index');
        }

        $sub_kegiatan_indikator_kinerja = new SubKegiatanIndikatorKinerja;
        $sub_kegiatan_indikator_kinerja->sub_kegiatan_id = $request->indikator_kinerja_sub_kegiatan_sub_kegiatan_id;
        $sub_kegiatan_indikator_kinerja->deskripsi = $request->indikator_kinerja_sub_kegiatan_deskripsi;
        $sub_kegiatan_indikator_kinerja->satuan = $request->indikator_kinerja_sub_kegiatan_satuan;
        $sub_kegiatan_indikator_kinerja->save();

        $opd_sub_kegiatan_indikator_kinerja = new OpdSubKegiatanIndikatorKinerja;
        $opd_sub_kegiatan_indikator_kinerja->sub_kegiatan_indikator_kinerja_id = $sub_kegiatan_indikator_kinerja->id;
        $opd_sub_kegiatan_indikator_kinerja->opd_id = Auth::user()->opd->opd_id;
        $opd_sub_kegiatan_indikator_kinerja->save();

        Alert::success('Berhasil', 'Berhasil menambahkan Indikator Kinerja Indikator');
        return redirect()->route('opd.renja.index');
    }

    public function indikator_kinerja_edit($id)
    {
        $data = SubKegiatanIndikatorKinerja::find($id);

        return response()->json(['result' => $data]);
    }

    public function indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_sub_kegiatan_id' => 'required',
            'edit_indikator_kinerja_sub_kegiatan_deskripsi' => 'required',
            'edit_indikator_kinerja_sub_kegiatan_satuan' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renja.index');
        }

        $sub_kegiatan_indikator_kinerja = SubKegiatanIndikatorKinerja::find($request->indikator_kinerja_sub_kegiatan_id);
        $sub_kegiatan_indikator_kinerja->deskripsi = $request->edit_indikator_kinerja_sub_kegiatan_deskripsi;
        $sub_kegiatan_indikator_kinerja->satuan = $request->edit_indikator_kinerja_sub_kegiatan_satuan;
        $sub_kegiatan_indikator_kinerja->save();

        Alert::success('Sukses', 'Berhasil menyimpan perubahan');
        return redirect()->route('opd.renja.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $request->sub_kegiatan_indikator_kinerja_id)->get();
        foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja) {
            $sub_kegiatan_target_satuan_rp_realisasis = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)->get();
            foreach ($sub_kegiatan_target_satuan_rp_realisasis as $sub_kegiatan_target_satuan_rp_realisasi) {
                $sub_kegiatan_tw_realisasis = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                foreach ($sub_kegiatan_tw_realisasis as $sub_kegiatan_tw_realisasi) {
                    SubKegiatanTwRealisasi::find($sub_kegiatan_tw_realisasi->id)->delete();
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
            'sub_kegiatan_indikator_kinerja_id' => 'required',
            'target' => 'required',
            'target_anggaran_renja_awal' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        $get_opd = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $request->sub_kegiatan_indikator_kinerja_id)
                    ->where('opd_id', Auth::user()->opd->opd_id)->first();
        if(!$get_opd)
        {
            $opd_sub_kegiatan = new OpdSubKegiatanIndikatorKinerja;
            $opd_sub_kegiatan->sub_kegiatan_indikator_kinerja_id = $request->sub_kegiatan_indikator_kinerja_id;
            $opd_sub_kegiatan->opd_id = Auth::user()->opd->opd_id;
            $opd_sub_kegiatan->save();

            $opd_sub_kegiatan_id = $opd_sub_kegiatan->id;
        } else {
            $opd_sub_kegiatan_id = $get_opd->id;
        }
        $last_sub_kegiatan = SubKegiatanTargetSatuanRpRealisasi::orderBy('id', 'desc')->first();

        $sub_kegiatan_target_satuan_rp_realisasi = new SubKegiatanTargetSatuanRpRealisasi;
        $sub_kegiatan_target_satuan_rp_realisasi->id = $last_sub_kegiatan->id + 1;
        $sub_kegiatan_target_satuan_rp_realisasi->opd_sub_kegiatan_indikator_kinerja_id = $opd_sub_kegiatan_id;
        $sub_kegiatan_target_satuan_rp_realisasi->target = $request->target;
        $sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_awal = $request->target_anggaran_renja_awal;
        $sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan = $request->target_anggaran_renja_perubahan;
        $sub_kegiatan_target_satuan_rp_realisasi->tahun = $request->tahun;
        $sub_kegiatan_target_satuan_rp_realisasi->save();

        return response()->json(['success' => 'Berhasil menambahkan data']);
    }

    public function target_satuan_realisasi_ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sub_kegiatan_target_satuan_rp_realisasi_id' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::find($request->sub_kegiatan_target_satuan_rp_realisasi_id);
        $sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan = $request->sub_kegiatan_edit_target_anggaran_perubahan;
        $sub_kegiatan_target_satuan_rp_realisasi->save();

        $html = '';
        $tws = MasterTw::all();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_sub_kegiatans = SubKegiatan::whereHas('sub_kegiatan_indikator_kinerja', function($q){
            $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->where('id', $sub_kegiatan_target_satuan_rp_realisasi->opd_sub_kegiatan_indikator_kinerja->sub_kegiatan_indikator_kinerja->sub_kegiatan_id)->orderBy('kode', 'asc')->get();
        $sub_kegiatan = [];
        foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
            $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)->orderBy('tahun_perubahan', 'asc')->latest()->first();
            if($cek_perubahan_sub_kegiatan)
            {
                $sub_kegiatan = [
                    'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                    'kegiatan_id' => $cek_perubahan_sub_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_sub_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_sub_kegiatan->tahun_perubahan,
                ];
            } else {
                $sub_kegiatan = [
                    'id' => $get_sub_kegiatan->id,
                    'kegiatan_id' => $get_sub_kegiatan->kegiatan_id,
                    'kode' => $get_sub_kegiatan->kode,
                    'deskripsi' => $get_sub_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_sub_kegiatan->tahun_perubahan,
                ];
            }
        }

        $idKegiatan = SubKegiatan::find($sub_kegiatan['id'])->kegiatan_id;

        $get_kegiatans = Kegiatan::where('id', $idKegiatan)
                            ->whereHas('kegiatan_indikator_kinerja', function($q){
                                $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                });
                            })->get();
        $kegiatan = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                        ->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatan = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                ];
            } else {
                $kegiatan = [
                    'id' => $get_kegiatan->id,
                    'kode' => $get_kegiatan->kode,
                    'deskripsi' => $get_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                ];
            }
        }

        $idProgram = Kegiatan::find($kegiatan['id'])->program_id;

        $get_programs = Program::where('id', $idProgram)->whereHas('program_rpjmd', function($q){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) {
                $q->whereHas('sasaran_indikator_kinerja', function($q){
                    $q->whereHas('sasaran');
                });
            });
        })->whereHas('program_indikator_kinerja', function($q){
            $q->whereHas('opd_program_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();
        $program = [];
        foreach($get_programs as $get_program)
        {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program = [
                    'id' => $cek_perubahan_program->program_id,
                    'kode' => $cek_perubahan_program->kode,
                    'deskripsi' => $cek_perubahan_program->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan
                ];
            } else {
                $program = [
                    'id' => $get_program->id,
                    'kode' => $get_program->kode,
                    'deskripsi' => $get_program->deskripsi,
                    'tahun_perubahan' => $get_program->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($program){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($program){
                $q->whereHas('program_rpjmd', function($q) use ($program){
                    $q->whereHas('program', function($q) use ($program){
                        $q->where('id', $program['id']);
                        $q->whereHas('program_indikator_kinerja', function($q){
                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                $q->where('opd_id', Auth::user()->opd->opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $sasaran = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()->first();
            if($cek_perubahan_sasaran)
            {
                $sasaran = [
                    'id' => $cek_perubahan_sasaran->sasaran_id,
                    'kode' => $cek_perubahan_sasaran->kode,
                    'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan,
                ];
            } else {
                $sasaran = [
                    'id' => $get_sasaran->id,
                    'kode' => $get_sasaran->kode,
                    'deskripsi' => $get_sasaran->deskripsi,
                    'tahun_perubahan' => $get_sasaran->tahun_perubahan,
                ];
            }
        }

        $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q){
            $q->where('opd_id', Auth::user()->opd->opd_id);
        })->where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
        $no_sub_kegiatan_indikator_kinerja = 1;
        foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
            $html .= '<tr>';
                $html .= '<td>'.$no_sub_kegiatan_indikator_kinerja++.'</td>';
                $html .= '<td>'.$sub_kegiatan_indikator_kinerja->deskripsi.'</td>';
                $b = 1;
                foreach($tahuns as $tahun)
                {
                    $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                        $q->where('opd_id', Auth::user()->opd->opd_id);
                        $q->where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id);
                    })->where('tahun', $tahun)->first();

                    if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                    {
                        if($b == 1)
                        {
                                $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target-anggaran-renja-awal data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-target-anggaran-renja-awal="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_awal.'">Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_awal,2).'</span></td>';
                                $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target-anggaran-renja-perubahan data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-target-anggaran-renja-perubahan="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan.'">Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan,2).'</span></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td>
                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mr-2 button-sub-kegiatan-edit-target-satuan-rp-realisasi"
                                                    type="button"
                                                    data-sub-kegiatan-indikator-kinerja-id="'.$sub_kegiatan_indikator_kinerja->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                            <button type="button"
                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-sub-kegiatan-tw-realisasi '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                data-tahun="'.$tahun.'"
                                                data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                value="close"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#sub_kegiatan_indikator_'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                class="accordion-toggle">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                        </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="sub_kegiatan_indikator_'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                        <table class="table table-striped table-condesed">
                                            <thead>
                                                <tr>
                                                    <th width="15%">Target Kinerja</th>
                                                    <th width="15%">Satuan</th>
                                                    <th width="15%">Tahun</th>
                                                    <th width="15%">TW</th>
                                                    <th width="15%">Realisasi Kinerja</th>
                                                    <th width="15%">Realisasi Anggaran</th>
                                                    <th width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodySubKegiatanIndikator'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $c = 1;
                                                    foreach($tws as $tw)
                                                    {
                                                        if($c == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sub_kegiatan_tw_realisasi_renja)
                                                                {
                                                                    $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                    $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                                                                    type="button"
                                                                                    data-tw-id = "'.$tw->id.'"
                                                                                    data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                    data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'"
                                                                                    data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                                                                    $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                                                                    type="button"
                                                                                    data-tw-id = "'.$tw->id.'"
                                                                                    data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        } else {
                                                            $html .= '<tr>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sub_kegiatan_tw_realisasi_renja)
                                                                {
                                                                    $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                    $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                                                                    type="button"
                                                                                    data-tw-id = "'.$tw->id.'"
                                                                                    data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                    data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'"
                                                                                    data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                                                                    $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                                                                    type="button"
                                                                                    data-tw-id = "'.$tw->id.'"
                                                                                    data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        }
                                                        $c++;
                                                    }
                                            $html .= '</tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target-anggaran-renja-awal data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-target-anggaran-renja-awal="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_awal.'">Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_awal,2).'</span></td>';
                                $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target-anggaran-renja-perubahan data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-target-anggaran-renja-perubahan="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan.'">Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan,2).'</span></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td>
                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mr-2 button-sub-kegiatan-edit-target-satuan-rp-realisasi"
                                                    type="button"
                                                    data-sub-kegiatan-indikator-kinerja-id="'.$sub_kegiatan_indikator_kinerja->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                            <button type="button"
                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-sub-kegiatan-tw-realisasi '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                data-tahun="'.$tahun.'"
                                                data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                value="close"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#sub_kegiatan_indikator_'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                class="accordion-toggle">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                        </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="sub_kegiatan_indikator_'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                        <table class="table table-striped table-condesed">
                                            <thead>
                                                <tr>
                                                    <th width="15%">Target Kinerja</th>
                                                    <th width="15%">Satuan</th>
                                                    <th width="15%">Tahun</th>
                                                    <th width="15%">TW</th>
                                                    <th width="15%">Realisasi Kinerja</th>
                                                    <th width="15%">Realisasi Anggaran</th>
                                                    <th width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodySubKegiatanIndikator'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $c = 1;
                                                    foreach($tws as $tw)
                                                    {
                                                        if($c == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sub_kegiatan_tw_realisasi_renja)
                                                                {
                                                                    $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                    $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                                                                    type="button"
                                                                                    data-tw-id = "'.$tw->id.'"
                                                                                    data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                    data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'"
                                                                                    data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                                                                    $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                                                                    type="button"
                                                                                    data-tw-id = "'.$tw->id.'"
                                                                                    data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        } else {
                                                            $html .= '<tr>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sub_kegiatan_tw_realisasi_renja)
                                                                {
                                                                    $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                    $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                                                                    type="button"
                                                                                    data-tw-id = "'.$tw->id.'"
                                                                                    data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                    data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'"
                                                                                    data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                                                                    $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                                                                    type="button"
                                                                                    data-tw-id = "'.$tw->id.'"
                                                                                    data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        }
                                                        $c++;
                                                    }
                                            $html .= '</tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>';
                        }
                        $b++;
                    } else {
                        if($b == 1)
                        {
                                $html .= '<td></td>';
                                $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td>
                                            <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
                                        </td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td>
                                            <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
                                        </td>';
                            $html .='</tr>';
                        }
                        $b++;
                    }
                }
        }

        return response()->json(['success' => 'Berhasil Merubah Target Sub Kegiatan', 'html' => $html, 'sub_kegiatan_id' => $sub_kegiatan['id']]);
        // return redirect()->route('opd.renja.index');
    }

    public function tw_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tw_id' => 'required',
            'sub_kegiatan_target_satuan_rp_realisasi_id' => 'required',
            'realisasi' => 'required',
            'realisasi_rp' => 'required',
            'sasaran_id' => 'required',
            'program_id' => 'required',
            'kegiatan_id' => 'required'
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
        $sub_kegiatan_tw_realisasi->sasaran_id = $request->sasaran_id;
        $sub_kegiatan_tw_realisasi->program_id = $request->program_id;
        $sub_kegiatan_tw_realisasi->kegiatan_id = $request->kegiatan_id;
        $sub_kegiatan_tw_realisasi->save();

        $tahun = $request->tahun;
        $html = '';
        $tws = MasterTw::all();
        $idSubKegiatan = $sub_kegiatan_tw_realisasi->sub_kegiatan_target_satuan_rp_realisasi->opd_sub_kegiatan_indikator_kinerja->sub_kegiatan_indikator_kinerja->sub_kegiatan_id;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_sub_kegiatans = SubKegiatan::whereHas('sub_kegiatan_indikator_kinerja', function($q){
            $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->where('id', $idSubKegiatan)->orderBy('kode', 'asc')->get();
        $sub_kegiatan = [];
        foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
            $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)->orderBy('tahun_perubahan', 'asc')->latest()->first();
            if($cek_perubahan_sub_kegiatan)
            {
                $sub_kegiatan = [
                    'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                    'kegiatan_id' => $cek_perubahan_sub_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_sub_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_sub_kegiatan->tahun_perubahan,
                ];
            } else {
                $sub_kegiatan = [
                    'id' => $get_sub_kegiatan->id,
                    'kegiatan_id' => $get_sub_kegiatan->kegiatan_id,
                    'kode' => $get_sub_kegiatan->kode,
                    'deskripsi' => $get_sub_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_sub_kegiatan->tahun_perubahan,
                ];
            }
        }

        $idKegiatan = SubKegiatan::find($sub_kegiatan['id'])->kegiatan_id;

        $get_kegiatans = Kegiatan::where('id', $idKegiatan)->whereHas('kegiatan_indikator_kinerja', function($q){
            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();
        $kegiatan = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                        ->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatan = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                ];
            } else {
                $kegiatan = [
                    'id' => $get_kegiatan->id,
                    'kode' => $get_kegiatan->kode,
                    'deskripsi' => $get_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                ];
            }
        }
        $getKegiatan = Kegiatan::find($kegiatan['id']);
        $get_programs = Program::where('id', $getKegiatan->program_id)->whereHas('program_rpjmd', function($q){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) {
                $q->whereHas('sasaran_indikator_kinerja', function($q){
                    $q->whereHas('sasaran');
                });
            });
        })->whereHas('program_indikator_kinerja', function($q){
            $q->whereHas('opd_program_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();
        $program = [];
        foreach($get_programs as $get_program)
        {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program = [
                    'id' => $cek_perubahan_program->program_id,
                    'kode' => $cek_perubahan_program->kode,
                    'deskripsi' => $cek_perubahan_program->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan
                ];
            } else {
                $program = [
                    'id' => $get_program->id,
                    'kode' => $get_program->kode,
                    'deskripsi' => $get_program->deskripsi,
                    'tahun_perubahan' => $get_program->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($program){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($program){
                $q->whereHas('program_rpjmd', function($q) use ($program){
                    $q->whereHas('program', function($q) use ($program){
                        $q->where('id', $program['id']);
                        $q->whereHas('program_indikator_kinerja', function($q){
                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                $q->where('opd_id', Auth::user()->opd->opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $sasaran = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()->first();
            if($cek_perubahan_sasaran)
            {
                $sasaran = [
                    'id' => $cek_perubahan_sasaran->sasaran_id,
                    'kode' => $cek_perubahan_sasaran->kode,
                    'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan,
                ];
            } else {
                $sasaran = [
                    'id' => $get_sasaran->id,
                    'kode' => $get_sasaran->kode,
                    'deskripsi' => $get_sasaran->deskripsi,
                    'tahun_perubahan' => $get_sasaran->tahun_perubahan,
                ];
            }
        }

        $sub_kegiatan_indikator_kinerja = SubKegiatanIndikatorKinerja::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q){
            $q->where('opd_id', Auth::user()->opd->opd_id);
        })->where('id', $sub_kegiatan_tw_realisasi->sub_kegiatan_target_satuan_rp_realisasi->opd_sub_kegiatan_indikator_kinerja->sub_kegiatan_indikator_kinerja->id)->first();

        $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
            $q->where('opd_id', Auth::user()->opd->opd_id);
            $q->where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id);
        })->where('tahun', $tahun)->first();

        $html .= '<tr>';
            $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
            $html .= '<td>'.$tahun.'</td>';
            $c = 1;
            foreach($tws as $tw)
            {
                if($c == 1)
                {
                        $html .= '<td>'.$tw->nama.'</td>';
                        $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                            ->where('tw_id', $tw->id)->first();
                        if($cek_sub_kegiatan_tw_realisasi_renja)
                        {
                            $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                            $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                            $html .= '<td>
                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                            type="button"
                                            data-tw-id = "'.$tw->id.'"
                                            data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                            data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'"
                                            data-tahun="'.$tahun.'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                        </button>
                                    </td>';
                        } else {
                            $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                            $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                            $html .= '<td>
                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                            type="button"
                                            data-tw-id = "'.$tw->id.'"
                                            data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="'.$tahun.'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                        </button>
                                    </td>';
                        }
                    $html .= '</tr>';
                } else {
                    $html .= '<tr>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '<td>'.$tw->nama.'</td>';
                        $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                            ->where('tw_id', $tw->id)->first();
                        if($cek_sub_kegiatan_tw_realisasi_renja)
                        {
                            $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                            $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                            $html .= '<td>
                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                            type="button"
                                            data-tw-id = "'.$tw->id.'"
                                            data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                            data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'"
                                            data-tahun="'.$tahun.'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                        </button>
                                    </td>';
                        } else {
                            $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                            $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                            $html .= '<td>
                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                            type="button"
                                            data-tw-id = "'.$tw->id.'"
                                            data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="'.$tahun.'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                        </button>
                                    </td>';
                        }
                    $html .= '</tr>';
                }
                $c++;
            }

        return response()->json(['success' => 'Berhasil menambahkan data', 'html' => $html, 'sub_kegiatan_target_satuan_rp_realisasi_id' => $sub_kegiatan_tw_realisasi->sub_kegiatan_target_satuan_rp_realisasi_id, 'tahun' => $tahun]);
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

        $tahun = $request->sub_kegiatan_tw_tahun;
        $html = '';
        $tws = MasterTw::all();
        $idSubKegiatan = $sub_kegiatan_tw_realisasi->sub_kegiatan_target_satuan_rp_realisasi->opd_sub_kegiatan_indikator_kinerja->sub_kegiatan_indikator_kinerja->sub_kegiatan_id;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_sub_kegiatans = SubKegiatan::whereHas('sub_kegiatan_indikator_kinerja', function($q){
            $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->where('id', $idSubKegiatan)->orderBy('kode', 'asc')->get();
        $sub_kegiatan = [];
        foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
            $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)->orderBy('tahun_perubahan', 'asc')->latest()->first();
            if($cek_perubahan_sub_kegiatan)
            {
                $sub_kegiatan = [
                    'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                    'kegiatan_id' => $cek_perubahan_sub_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_sub_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_sub_kegiatan->tahun_perubahan,
                ];
            } else {
                $sub_kegiatan = [
                    'id' => $get_sub_kegiatan->id,
                    'kegiatan_id' => $get_sub_kegiatan->kegiatan_id,
                    'kode' => $get_sub_kegiatan->kode,
                    'deskripsi' => $get_sub_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_sub_kegiatan->tahun_perubahan,
                ];
            }
        }

        $idKegiatan = SubKegiatan::find($sub_kegiatan['id'])->kegiatan_id;

        $get_kegiatans = Kegiatan::where('id', $idKegiatan)->whereHas('kegiatan_indikator_kinerja', function($q){
            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();
        $kegiatan = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                        ->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatan = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                ];
            } else {
                $kegiatan = [
                    'id' => $get_kegiatan->id,
                    'kode' => $get_kegiatan->kode,
                    'deskripsi' => $get_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                ];
            }
        }
        $getKegiatan = Kegiatan::find($kegiatan['id']);
        $get_programs = Program::where('id', $getKegiatan->program_id)->whereHas('program_rpjmd', function($q){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) {
                $q->whereHas('sasaran_indikator_kinerja', function($q){
                    $q->whereHas('sasaran');
                });
            });
        })->whereHas('program_indikator_kinerja', function($q){
            $q->whereHas('opd_program_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();
        $program = [];
        foreach($get_programs as $get_program)
        {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program = [
                    'id' => $cek_perubahan_program->program_id,
                    'kode' => $cek_perubahan_program->kode,
                    'deskripsi' => $cek_perubahan_program->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan
                ];
            } else {
                $program = [
                    'id' => $get_program->id,
                    'kode' => $get_program->kode,
                    'deskripsi' => $get_program->deskripsi,
                    'tahun_perubahan' => $get_program->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($program){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($program){
                $q->whereHas('program_rpjmd', function($q) use ($program){
                    $q->whereHas('program', function($q) use ($program){
                        $q->where('id', $program['id']);
                        $q->whereHas('program_indikator_kinerja', function($q){
                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                $q->where('opd_id', Auth::user()->opd->opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $sasaran = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()->first();
            if($cek_perubahan_sasaran)
            {
                $sasaran = [
                    'id' => $cek_perubahan_sasaran->sasaran_id,
                    'kode' => $cek_perubahan_sasaran->kode,
                    'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan,
                ];
            } else {
                $sasaran = [
                    'id' => $get_sasaran->id,
                    'kode' => $get_sasaran->kode,
                    'deskripsi' => $get_sasaran->deskripsi,
                    'tahun_perubahan' => $get_sasaran->tahun_perubahan,
                ];
            }
        }

        $sub_kegiatan_indikator_kinerja = SubKegiatanIndikatorKinerja::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q){
            $q->where('opd_id', Auth::user()->opd->opd_id);
        })->where('id', $sub_kegiatan_tw_realisasi->sub_kegiatan_target_satuan_rp_realisasi->opd_sub_kegiatan_indikator_kinerja->sub_kegiatan_indikator_kinerja->id)->first();

        $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
            $q->where('opd_id', Auth::user()->opd->opd_id);
            $q->where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id);
        })->where('tahun', $tahun)->first();

        $html .= '<tr>';
            $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
            $html .= '<td>'.$tahun.'</td>';
            $c = 1;
            foreach($tws as $tw)
            {
                if($c == 1)
                {
                        $html .= '<td>'.$tw->nama.'</td>';
                        $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                            ->where('tw_id', $tw->id)->first();
                        if($cek_sub_kegiatan_tw_realisasi_renja)
                        {
                            $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                            $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                            $html .= '<td>
                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                            type="button"
                                            data-tw-id = "'.$tw->id.'"
                                            data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                            data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'"
                                            data-tahun="'.$tahun.'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                        </button>
                                    </td>';
                        } else {
                            $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                            $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                            $html .= '<td>
                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                            type="button"
                                            data-tw-id = "'.$tw->id.'"
                                            data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="'.$tahun.'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                        </button>
                                    </td>';
                        }
                    $html .= '</tr>';
                } else {
                    $html .= '<tr>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '<td>'.$tw->nama.'</td>';
                        $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                            ->where('tw_id', $tw->id)->first();
                        if($cek_sub_kegiatan_tw_realisasi_renja)
                        {
                            $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                            $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                            $html .= '<td>
                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                            type="button"
                                            data-tw-id = "'.$tw->id.'"
                                            data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                            data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'"
                                            data-tahun="'.$tahun.'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                        </button>
                                    </td>';
                        } else {
                            $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                            $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].' '.$kegiatan['id'].'"></td>';
                            $html .= '<td>
                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                            type="button"
                                            data-tw-id = "'.$tw->id.'"
                                            data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="'.$tahun.'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                        </button>
                                    </td>';
                        }
                    $html .= '</tr>';
                }
                $c++;
            }

        return response()->json(['success' => 'Berhasil Merubah Target Sub Kegiatan', 'html' => $html, 'sub_kegiatan_target_satuan_rp_realisasi_id' => $sub_kegiatan_tw_realisasi->sub_kegiatan_target_satuan_rp_realisasi_id, 'tahun' => $tahun]);

        // Alert::success('Berhasil', 'Berhasil Merubah Target Sub Kegiatan');
        // return redirect()->route('opd.renja.index');
    }
}
