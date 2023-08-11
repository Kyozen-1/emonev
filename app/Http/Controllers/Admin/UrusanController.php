<?php

namespace App\Http\Controllers\Admin;

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
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Imports\UrusanImport;
use App\Models\TahunPeriode;
use App\Models\SubKegiatanTwRealisasi;
use App\Models\SubKegiatanTargetSatuanRpRealisasi;
use App\Models\OpdSubKegiatanIndikatorKinerja;
use App\Models\SubKegiatanIndikatorKinerja;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\SubKegiatan;
use App\Models\KegiatanTwRealisasi;
use App\Models\KegiatanTargetSatuanRpRealisasi;
use App\Models\OpdKegiatanIndikatorKinerja;
use App\Models\KegiatanIndikatoKinerja;
use App\Models\PivotPerubahanKegiatan;
use App\Models\Kegiatan;
use App\Models\SasaranPdProgramRpjmd;
use App\Models\PivotSasaranIndikatorProgramRpjmd;
use App\Models\ProgramRpjmd;
use App\Models\ProgramTwRealisasi;
use App\Models\ProgramTargetSatuanRpRealisasi;
use App\Models\OpdProgramIndikatorKinerja;
use App\Models\ProgramIndikatorKinerja;
use App\Models\PivotPerubahanProgram;
use App\Models\Program;

class UrusanController extends Controller
{
    public function index()
    {
        if(request()->ajax())
        {
            $data = Urusan::orderBy('kode', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="urusan_detail" id="'.$data->id.'" class="urusan_detail btn btn-icon waves-effect btn-success" title="Detail Data" data-tahun="semua"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="urusan_edit" id="'.$data->id.'"
                    class="urusan_edit btn btn-icon waves-effect btn-warning" title="Edit Data" data-tahun="semua"><i class="fas fa-edit"></i></button>';
                    $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="urusan_delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    // $button = $button_show . ' ' . $button_edit;
                    return $button;
                })
                ->editColumn('kode', function($data){
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $data->id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        return $cek_perubahan_urusan->kode;
                    } else {
                        return $data->kode;
                    }
                })
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan = PivotPerubahanUrusan::where('urusan_id',$data->id)->orderBy('tahun_perubahan', 'desc')->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->deskripsi;
                    } else {
                        return $data->deskripsi;
                    }
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        return view('admin.urusan.index', [
            'tahuns' => $tahuns
        ]);
    }

    public function get_urusan($tahun)
    {
        if(request()->ajax())
        {
            $data = Urusan::where('tahun_perubahan', $tahun)->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data) use ($tahun){
                    $button_show = '<button type="button" name="urusan_detail" id="'.$data->id.'" class="urusan_detail btn btn-icon waves-effect btn-success" title="Detail Data" data-tahun="'.$tahun.'"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="urusan_edit" id="'.$data->id.'"
                    class="urusan_edit btn btn-icon waves-effect btn-warning" title="Edit Data" data-tahun="'.$tahun.'"><i class="fas fa-edit"></i></button>';
                    // $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    // $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    $button = $button_show . ' ' . $button_edit;
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'urusan_kode' => 'required',
            'urusan_deskripsi' => 'required',
            'urusan_tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $cek_urusan =  Urusan::where('kode', $request->urusan_kode)->first();
        if($cek_urusan)
        {
            $cek_pivot_urusan = PivotPerubahanUrusan::where('kode', $request->urusan_kode)
                                ->where('tahun_perubahan', $request->urusan_tahun_perubahan)
                                ->where('urusan_id', $cek_urusan->id)
                                ->first();
            if($cek_pivot_urusan)
            {
                PivotPerubahanUrusan::find($cek_pivot_urusan->id)->delete();

                $pivot = new PivotPerubahanUrusan;
                $pivot->urusan_id = $cek_urusan->id;
                $pivot->kode = $request->urusan_kode;
                $pivot->deskripsi = $request->urusan_deskripsi;
                $pivot->tahun_perubahan = $request->urusan_tahun_perubahan;
                if($request->urusan_tahun_perubahan > 2020)
                {
                    $pivot->status_aturan = 'Sesudah Perubahan';
                } else {
                    $pivot->status_aturan = 'Sebelum Perubahan';
                }
                $pivot->kabupaten_id = 62;
                $pivot->save();
            } else {
                $pivot = new PivotPerubahanUrusan;
                $pivot->urusan_id = $cek_urusan->id;
                $pivot->kode = $request->urusan_kode;
                $pivot->deskripsi = $request->urusan_deskripsi;
                $pivot->tahun_perubahan = $request->urusan_tahun_perubahan;
                if($request->urusan_tahun_perubahan > 2020)
                {
                    $pivot->status_aturan = 'Sesudah Perubahan';
                } else {
                    $pivot->status_aturan = 'Sebelum Perubahan';
                }
                $pivot->kabupaten_id = 62;
                $pivot->save();
            }
        } else {
            $urusan = new Urusan;
            $urusan->kode = $request->urusan_kode;
            $urusan->deskripsi = $request->urusan_deskripsi;
            $urusan->tahun_perubahan = $request->urusan_tahun_perubahan;
            if($request->urusan_tahun_perubahan > 2020)
            {
                $urusan->status_aturan = 'Sesudah Perubahan';
            } else {
                $urusan->status_aturan = 'Sebelum Perubahan';
            }
            $urusan->kabupaten_id = 62;
            $urusan->save();
        }

        return response()->json(['success' => 'Berhasil Menambahkan Urusan']);
    }

    public function show($id, $tahun)
    {
        if($tahun == 'semua')
        {
            $data = Urusan::find($id);

            $cek_perubahan = PivotPerubahanUrusan::where('urusan_id', $id)->first();
            $html = '<div>';

            if($cek_perubahan)
            {
                $get_perubahans = PivotPerubahanUrusan::where('urusan_id', $id)->orderBy('tahun_perubahan', 'asc')->get();
                $html .= '<ul>';
                $html .= '<li>'.$data->deskripsi.', Tahun '.$data->tahun_perubahan.'</li>';
                $a = 1;
                foreach ($get_perubahans as $get_perubahan) {
                    $html .= '<li>'.$get_perubahan->deskripsi.', Tahun '.$get_perubahan->tahun_perubahan.' (Tahun '.$a++.'), '.$get_perubahan->created_at.'</li>';
                }
                $html .= '</ul>';
            } else {
                $html .= '<p>Tidak ada</p>';
            }

            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $data->id)->orderBy('tahun_perubahan', 'desc')->first();
            if($cek_perubahan_urusan)
            {
                $tahun_perubahan = $cek_perubahan_urusan->tahun_perubahan;
            } else {
                $tahun_perubahan = $data->tahun_perubahan;
            }

            $html .='</div>';

            $array = [
                'kode' => $data->kode,
                'deskripsi' => $data->deskripsi,
                'tahun_perubahan' => $tahun_perubahan,
                'pivot_perubahan_urusan' => $html
            ];
        } else {
            $cek_data = Urusan::where('id', $id)->where('tahun_perubahan', $tahun)->first();
            $kode = '';
            $deskripsi = '';
            $tahun_perubahan = '';
            if($cek_data)
            {
                $kode = $cek_data->kode;
                $deskripsi = $cek_data->deskripsi;
                $tahun_perubahan = $cek_data->tahun_perubahan;
            } else {
                $perubahahan_urusan = PivotPerubahanUrusan::where('urusan_id', $id)->where('tahun_perubahan', $tahun)->first();
                $kode = $perubahahan_urusan->kode;
                $deskripsi = $perubahahan_urusan->deskripsi;
                $tahun_perubahan = $perubahahan_urusan->tahun_perubahan;
            }

            $cek_perubahan = PivotPerubahanUrusan::where('urusan_id', $id)->first();
            $html = '<div>';

            if($cek_perubahan)
            {
                $data = Urusan::find($id);
                $get_perubahans = PivotPerubahanUrusan::where('urusan_id', $id)->orderBy('tahun_perubahan', 'asc')->get();
                $html .= '<ul>';
                $html .= '<li>'.$data->deskripsi.', Tahun '.$data->tahun_perubahan.'</li>';
                $a = 1;
                foreach ($get_perubahans as $get_perubahan) {
                    $html .= '<li>'.$get_perubahan->deskripsi.', Tahun '.$get_perubahan->tahun_perubahan.' (Tahun '.$a++.'), '.$get_perubahan->created_at.'</li>';
                }
                $html .= '</ul>';
            } else {
                $html .= '<p>Tidak ada</p>';
            }

            $html .='</div>';

            $array = [
                'kode' => $kode,
                'deskripsi' => $deskripsi,
                'tahun_perubahan' => $tahun_perubahan,
                'pivot_perubahan_urusan' => $html
            ];
        }

        return response()->json(['result' => $array]);
    }

    public function edit($id, $tahun)
    {
        if($tahun == 'semua')
        {
            $data = Urusan::find($id);
            $cek_perubahan = PivotPerubahanUrusan::where('urusan_id', $id)->orderBy('tahun_perubahan', 'desc')->first();
            if($cek_perubahan)
            {
                $deskripsi = $cek_perubahan->deskripsi;
            } else {
                $deskripsi = $data->deskripsi;
            }

            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $data->id)->orderBy('tahun_perubahan', 'desc')->first();
            if($cek_perubahan_urusan)
            {
                $tahun_perubahan = $cek_perubahan_urusan->tahun_perubahan;
            } else {
                $tahun_perubahan = $data->tahun_perubahan;
            }

            $array = [
                'id' => $data->id,
                'kode' => $data->kode,
                'deskripsi' => $deskripsi,
                'tahun_perubahan' => $tahun_perubahan
            ];
        } else {
            $cek_data = Urusan::where('id', $id)->where('tahun_perubahan', $tahun)->first();
            $kode = '';
            $deskripsi = '';
            $tahun_perubahan = '';
            if($cek_data)
            {
                $kode = $cek_data->kode;
                $deskripsi = $cek_data->deskripsi;
                $tahun_perubahan = $cek_data->tahun_perubahan;
            } else {
                $perubahahan_urusan = PivotPerubahanUrusan::where('urusan_id', $id)->where('tahun_perubahan', $tahun)->first();
                $kode = $perubahahan_urusan->kode;
                $deskripsi = $perubahahan_urusan->deskripsi;
                $tahun_perubahan = $perubahahan_urusan->tahun_perubahan;
            }
            $array = [
                'id' => $id,
                'kode' => $kode,
                'deskripsi' => $deskripsi,
                'tahun_perubahan' => $tahun_perubahan
            ];
        }

        return response()->json(['result' => $array]);
    }

    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'urusan_kode' => 'required',
            'urusan_deskripsi' => 'required',
            'urusan_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $cek_pivot = PivotPerubahanUrusan::where('urusan_id', $request->urusan_hidden_id)
                        ->where('kode', $request->urusan_kode)
                        ->where('tahun_perubahan', $request->urusan_tahun_perubahan)
                        ->first();
        if($cek_pivot)
        {
            PivotPerubahanUrusan::find($cek_pivot->id)->delete();

            $pivot = new PivotPerubahanUrusan;
            $pivot->urusan_id = $request->urusan_hidden_id;
            $pivot->kode = $request->urusan_kode;
            $pivot->deskripsi = $request->urusan_deskripsi;
            $pivot->tahun_perubahan = $request->urusan_tahun_perubahan;
            if($request->urusan_tahun_perubahan > 2020)
            {
                $pivot->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot->status_aturan = 'Sebelum Perubahan';
            }
            $pivot->kabupaten_id = 62;
            $pivot->save();
        } else {
            $pivot = new PivotPerubahanUrusan;
            $pivot->urusan_id = $request->urusan_hidden_id;
            $pivot->kode = $request->urusan_kode;
            $pivot->deskripsi = $request->urusan_deskripsi;
            $pivot->tahun_perubahan = $request->urusan_tahun_perubahan;
            if($request->urusan_tahun_perubahan > 2020)
            {
                $pivot->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot->status_aturan = 'Sebelum Perubahan';
            }
            $pivot->kabupaten_id = 62;
            $pivot->save();
        }

        return response()->json(['success' => 'Berhasil Merubah Data']);
    }

    public function impor(Request $request)
    {
        $file = $request->file('impor_urusan');
        Excel::import(new UrusanImport, $file->store('temp'));
        $msg = [session('import_status'), session('import_message')];
        if ($msg[0]) {
            Alert::success('Berhasil', $msg[1]);
            return back();
        } else {
            Alert::error('Gagal', $msg[1]);
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            $programs = Program::where('urusan_id', $id)->get();
            foreach ($programs as $program) {
                $kegiatans = Kegiatan::where('program_id', $program->id)->get();
                foreach ($kegiatans as $kegiatan) {
                    $sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan->id)->get();
                    foreach ($sub_kegiatans as $sub_kegiatan) {
                        // Hapus Sub Kegiatan Start
                        $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan->id)->get();
                        foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                            $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)->get();
                            foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja) {
                                $sub_kegiatan_target_satuan_rp_realisasis = SubKegiatanTargetSatuanRpRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $opd_sub_kegiatan_indikator_kinerja->id)->get();
                                foreach ($sub_kegiatan_target_satuan_rp_realisasis as $sub_kegiatan_target_satuan_rp_realisasi) {
                                    $sub_kegiatan_tw_realisasis = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                    foreach ($sub_kegiatan_tw_realisasis as $sub_kegiatan_tw_realisasi) {
                                        SubKegiatanTwRealisasi::find($sub_kegiatan_tw_realisasi->id)->delete();
                                    }

                                    SubKegiatanTargetSatuanRpRealisasi::find($sub_kegiatan_target_satuan_rp_realisasi->id)->delete();
                                }

                                OpdSubKegiatanIndikatorKinerja::find($opd_sub_kegiatan_indikator_kinerja->id)->delete();
                            }

                            SubKegiatanIndikatorKinerja::find($sub_kegiatan_indikator_kinerja->id)->delete();
                        }

                        $pivot_perubahan_sub_kegiatans = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $sub_kegiatan->id)->get();
                        foreach ($pivot_perubahan_sub_kegiatans as $pivot_perubahan_sub_kegiatan) {
                            PivotPerubahanSubKegiatan::find($pivot_perubahan_sub_kegiatan->id)->delete();
                        }

                        SubKegiatan::find($sub_kegiatan->id)->delete();
                        // Hapus Sub Kegiatan End
                    }

                    // Hapus Kegiatan Start
                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan->id)->get();
                    foreach ($kegiatan_indikator_kinerjass as $kegiatan_indikator_kinerja) {
                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                        foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                            $kegiatan_target_satuan_rp_realisasis = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->get();
                            foreach ($kegiatan_target_satuan_rp_realisasis as $kegiatan_target_satuan_rp_realisasi) {
                                $kegiatan_renja_tw_realisasis = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->get();
                                foreach ($kegiatan_renja_tw_realisasis as $kegiatan_renja_tw_realisasi) {
                                    KegiatanTwRealisasi::find($kegiatan_renja_tw_realisasi->id)->delete();
                                }

                                KegiatanTargetSatuanRpRealisasi::find($kegiatan_target_satuan_rp_realisasi->id)->delete();
                            }

                            OpdKegiatanIndikatorKinerja::find($opd_kegiatan_indikator_kinerja->id)->delete();
                        }

                        KegiatanIndikatorKinerja::find($kegiatan_indikator_kinerja->id)->delete();
                    }

                    $pivot_perubahan_kegiatans = PivotPerubahanKegiatan::where('kegiatan_id', $kegiatan->id)->get();
                    foreach ($pivot_perubahan_kegiatans as $pivot_perubahan_kegiatan) {
                        PivotPerubahanKegiatan::find($pivot_perubahan_kegiatan->id)->delete();
                    }
                    Kegiatan::find($kegiatan->id)->delete();
                    // Hapus Kegiatan End
                }

                // Hapus Program Start
                $program_rpjmds = ProgramRpjmd::where('program_id', $program->id)->get();
                foreach ($program_rpjmds as $program_rpjmd) {

                    $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)->get();
                    foreach ($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd) {
                        PivotSasaranIndikatorProgramRpjmd::find($pivot_sasaran_indikator_program_rpjmd->id)->delete();
                    }

                    $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)->delete();
                    foreach ($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd) {
                        SasaranPdProgramRpjmd::find($sasaran_pd_program_rpjmd->id)->delete();
                    }

                    ProgramRpjmd::find($program_rpjmd->id)->delete();
                }

                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id')->get();
                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                    $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                    foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                        $program_target_satuan_rp_realisasis = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->get();
                        foreach ($program_target_satuan_rp_realisasis as $program_target_satuan_rp_realisasi) {
                            $program_tw_realisasi_renjas = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->get();
                            foreach ($program_tw_realisasi_renjas as $program_tw_realisasi_renja) {
                                ProgramTwRealisasi::find($program_tw_realisasi_renja->id)->delete();
                            }

                            ProgramTargetSatuanRpRealisasi::find($program_target_satuan_rp_realisasi->id)->delete();
                        }

                        OpdProgramIndikatorKinerja::find($opd_program_indikator_kinerja->id)->delete();
                    }

                    ProgramIndikatorKinerja::find($program_indikator_kinerja->id)->delete();
                }

                $pivot_perubahan_programs = PivotPerubahanProgram::where('program_id', $program->id)->get();
                foreach ($pivot_perubahan_programs as $pivot_perubahan_program) {
                    PivotPerubahanProgram::find($pivot_perubahan_program->id)->delete();
                }

                Program::find($program->id)->delete();
                // Hapus Program End
            }

            // Hapus Urusan Start
            $perubahan_urusans = PivotPerubahanUrusan::where('urusan_id', $id)->get();
            foreach ($perubahan_urusans as $perubahan_urusan) {
                PivotPerubahanUrusan::find($perubahan_urusan->id)->delete();
            }
            Urusan::find($id)->delete();
            // Hapus Urusan End

            return response()->json(['success' => 'berhasil hapus']);
        } catch (\Throwable $th) {
            return response()->json(['errors' => $th->getMessage()]);
        }
    }
}
