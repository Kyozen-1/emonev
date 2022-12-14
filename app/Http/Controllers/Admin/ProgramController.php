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

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax())
        {
            $data = Program::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="detail" id="'.$data->id.'" class="detail btn btn-icon waves-effect btn-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="edit" id="'.$data->id.'"
                    class="edit btn btn-icon waves-effect btn-warning" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    // $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    // $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    $button = $button_show . ' ' . $button_edit;
                    return $button;
                })
                ->editColumn('urusan_id', function($data){
                    if($data->urusan_id)
                    {
                        return $data->urusan->kode;
                    } else {
                        return 'Segera Edit Data!';
                    }
                })
                ->editColumn('kode', function($data){
                    $cek_perubahan = PivotPerubahanProgram::where('program_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->kode;
                    } else {
                        return $data->kode;
                    }
                })
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan = PivotPerubahanProgram::where('program_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->deskripsi;
                    } else {
                        return $data->deskripsi;
                    }
                })
                ->addColumn('indikator', function($data){
                    $jumlah = PivotProgramIndikator::whereHas('program', function($q) use ($data){
                        $q->where('program_id', $data->id);
                    })->count();

                    return '<a href="'.url('/admin/program/'.$data->id.'/indikator').'" >'.$jumlah.'</a>';;
                })
                ->editColumn('pagu', function($data){
                    $cek_perubahan = PivotPerubahanProgram::where('program_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return 'Rp. '.number_format($cek_perubahan->pagu, 2);
                    } else {
                        return 'Rp. '.number_format($data->pagu, 2);
                    }
                })
                ->rawColumns(['aksi', 'indikator'])
                ->make(true);
        }
        $urusan = Urusan::pluck('deskripsi', 'id');
        return view('admin.program.index', [
            'urusan' => $urusan
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'program_urusan_id' => 'required',
            'program_kode' => 'required',
            'program_deskripsi' => 'required',
            'program_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $cek_perubahan_urusan = PivotPerubahanUrusan::where('id', $request->program_urusan_id)->latest()->first();
        if($cek_perubahan_urusan)
        {
            $urusan_id = $cek_perubahan_urusan->urusan_id;
        } else {
            $urusan_id = $request->program_urusan_id;
        }

        $cek_program = Program::where('kode', $request->kode)->first();
        if($cek_program)
        {
            $pivot = new PivotPerubahanProgram;
            $pivot->program_id = $cek_program->id;
            $pivot->urusan_id = $urusan_id;
            $pivot->kode = $request->program_kode;
            $pivot->deskripsi = $request->program_deskripsi;
            $pivot->tahun_perubahan = $request->program_tahun_perubahan;
            if($request->status_perubahan > 2020)
            {
                $pivot->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot->status_aturan = 'Sebelum Perubahan';
            }
            $pivot->kabupaten_id = 62;
            $pivot->save();
        } else {
            $program = new Program;
            $program->urusan_id = $urusan_id;
            $program->kode = $request->program_kode;
            $program->deskripsi = $request->program_deskripsi;
            $program->tahun_perubahan = $request->program_tahun_perubahan;
            if($request->status_perubahan > 2020)
            {
                $program->status_aturan = 'Sesudah Perubahan';
            } else {
                $program->status_aturan = 'Sebelum Perubahan';
            }
            $program->kabupaten_id = 62;
            $program->save();
        }

        return response()->json(['success' => 'Berhasil Menambahkan Program']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $tahun)
    {
        if($tahun == 'semua')
        {
            $data = Program::find($id);

            $cek_perubahan = PivotPerubahanProgram::where('program_id', $id)->orderBy('tahun_perubahan', 'desc')->first();
            $html = '<div>';

            if($cek_perubahan)
            {
                $get_perubahans = PivotPerubahanProgram::where('program_id', $id)->orderBy('tahun_perubahan', 'asc')->get();
                $html .= '<ul>';
                $html .= '<li><p>
                                Kode Urusan: '.$data->urusan->kode.' <br>
                                Kode: '.$data->kode.'<br>
                                Deskripsi: '.$data->deskripsi.'<br>
                                Tahun Perubahan: '.$data->tahun_perubahan.'<br>
                                Status: <span class="text-primary">Sebelum Perubahan</span>
                            </p></li>';
                $a = 1;
                foreach ($get_perubahans as $get_perubahan) {
                    $html .= '<li><p>
                                Kode Urusan: '.$get_perubahan->program->urusan->kode.' <br>
                                Kode: '.$get_perubahan->kode.'<br>
                                Deskripsi: '.$get_perubahan->deskripsi.'<br>
                                Tahun Perubahan: '.$get_perubahan->tahun_perubahan.'<br>
                                Status: <span class="text-warning">Perubahan '.$a++.'</span>
                            </p></li>';
                }
                $html .= '</ul>';
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $cek_perubahan->urusan_id)
                                            ->orderBy('tahun_perubahan', 'desc')
                                            ->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $program_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $urusan = Urusan::find($cek_perubahan->urusan_id);
                    $program_urusan = $urusan->deskripsi;
                }
                $program_deskripsi = $cek_perubahan->deskripsi;
                $program_kode = $cek_perubahan->kode;
                $program_tahun_perubahan = $cek_perubahan->tahun_perubahan;
            } else {
                $html .= '<p>Tidak ada</p>';
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $data->urusan_id)
                                            ->orderBy('tahun_perubahan', 'desc')
                                            ->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $program_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $urusan = Urusan::find($data->urusan_id);
                    $program_urusan = $urusan->deskripsi;
                }
                $program_deskripsi = $data->deskripsi;
                $program_kode = $data->kode;
                $program_tahun_perubahan = $data->tahun_perubahan;
            }
        } else {
            $data = Program::find($id);

            $cek_perubahan = PivotPerubahanProgram::where('program_id', $id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
            $html = '<div>';

            if($cek_perubahan)
            {
                $get_perubahans = PivotPerubahanProgram::where('program_id', $id)->orderBy('tahun_perubahan', 'desc')->get();
                $html .= '<ul>';
                $html .= '<li><p>
                                Kode Urusan: '.$data->urusan->kode.' <br>
                                Kode: '.$data->kode.'<br>
                                Deskripsi: '.$data->deskripsi.'<br>
                                Tahun Perubahan: '.$data->tahun_perubahan.'<br>
                                Status: <span class="text-primary">Sebelum Perubahan</span>
                            </p></li>';
                $a = 1;
                foreach ($get_perubahans as $get_perubahan) {
                    $html .= '<li><p>
                                Kode Urusan: '.$get_perubahan->program->urusan->kode.' <br>
                                Kode: '.$get_perubahan->kode.'<br>
                                Deskripsi: '.$get_perubahan->deskripsi.'<br>
                                Tahun Perubahan: '.$get_perubahan->tahun_perubahan.'<br>
                                Status: <span class="text-warning">Perubahan '.$a++.'</span>
                            </p></li>';
                }
                $html .= '</ul>';
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $cek_perubahan->urusan_id)
                                            ->orderBy('tahun_perubahan', 'desc')
                                            ->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $program_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $urusan = Urusan::find($cek_perubahan->urusan_id);
                    $program_urusan = $urusan->deskripsi;
                }
                $program_deskripsi = $data->deskripsi;
                $program_kode = $data->kode;
                $program_tahun_perubahan = $data->tahun_perubahan;
            } else {
                $html .= '<p>Tidak ada</p>';
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $data->urusan_id)
                                            ->orderBy('tahun_perubahan', 'desc')
                                            ->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $program_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $urusan = Urusan::find($data->urusan_id);
                    $program_urusan = $urusan->deskripsi;
                }
                $program_deskripsi = $data->deskripsi;
                $program_kode = $data->kode;
                $program_tahun_perubahan = $data->tahun_perubahan;
            }
        }

        $array = [
            'urusan' => $program_urusan,
            'kode' => $program_kode,
            'deskripsi' => $program_deskripsi,
            'tahun_perubahan' => $program_tahun_perubahan,
            'pivot_perubahan_program' => $html
        ];

        return response()->json(['result' => $array]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $tahun)
    {
        if($tahun == 'semua')
        {
            $data = Program::find($id);
            $cek_perubahan = PivotPerubahanProgram::where('program_id', $id)->latest()->first();
            if($cek_perubahan)
            {
                $array = [
                    'id' => $cek_perubahan->id,
                    'urusan_id' => $cek_perubahan->urusan_id,
                    'kode' => $cek_perubahan->kode,
                    'deskripsi' => $cek_perubahan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan->tahun_perubahan
                ];
            } else {
                $array = [
                    'id' => $data->id,
                    'urusan_id' => $data->urusan_id,
                    'kode' => $data->kode,
                    'deskripsi' => $data->deskripsi,
                    'tahun_perubahan' => $data->tahun_perubahan
                ];
            }
        } else {
            $data = Program::find($id);
            $array = [
                'id' => $data->id,
                'urusan_id' => $data->urusan_id,
                'kode' => $data->kode,
                'deskripsi' => $data->deskripsi,
                'tahun_perubahan' => $data->tahun_perubahan
            ];
        }

        return response()->json(['result' => $array]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'program_kode' => 'required',
            'program_deskripsi' => 'required',
            'program_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $cek_pivot = PivotPerubahanProgram::where('program_id', $request->program_hidden_id)
                        ->where('urusan_id', $request->program_urusan_id)
                        ->where('kode', $request->program_kode)
                        ->where('tahun_perubahan', $request->program_tahun_perubahan)
                        ->first();
        if($cek_pivot)
        {
            PivotPerubahanProgram::find($cek_pivot->id)->delete();

            $pivot = new PivotPerubahanProgram;
            $pivot->program_id = $request->program_hidden_id;
            $pivot->urusan_id = $request->program_urusan_id;
            $pivot->kode = $request->program_kode;
            $pivot->deskripsi = $request->program_deskripsi;
            $pivot->tahun_perubahan = $request->program_tahun_perubahan;
            $pivot->kabupaten_id = 62;
            if($request->status_perubahan > 2020)
            {
                $pivot->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot->status_aturan = 'Sebelum Perubahan';
            }
            $pivot->save();
        } else {
            $pivot = new PivotPerubahanProgram;
            $pivot->program_id = $request->program_hidden_id;
            $pivot->urusan_id = $request->program_urusan_id;
            $pivot->kode = $request->program_kode;
            $pivot->deskripsi = $request->program_deskripsi;
            $pivot->tahun_perubahan = $request->program_tahun_perubahan;
            $pivot->kabupaten_id = 62;
            if($request->status_perubahan > 2020)
            {
                $pivot->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot->status_aturan = 'Sebelum Perubahan';
            }
            $pivot->save();
        }

        return response()->json(['success' => 'Berhasil Merubah Program']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function impor(Request $request)
    {
        $urusan_id = $request->program_impor_urusan_id;
        $file = $request->file('impor_program');
        Excel::import(new ProgramImport($urusan_id), $file->store('temp'));
        $msg = [session('import_status'), session('import_message')];
        if ($msg[0]) {
            Alert::success('Berhasil', $msg[1]);
            return back();
        } else {
            Alert::error('Gagal', $msg[1]);
            return back();
        }
    }

    public function indikator_kinerja_tambah(Request $request)
    {
        $indikator_kinerja = new ProgramIndikatorKinerja;
        $indikator_kinerja->program_id = $request->indikator_kinerja_program_program_id;
        $indikator_kinerja->deskripsi = $request->indikator_kinerja_program_deskripsi;
        $indikator_kinerja->satuan = $request->indikator_kinerja_program_satuan;
        $indikator_kinerja->kondisi_target_kinerja_awal = $request->indikator_kinerja_program_kondisi_target_kinerja_awal;
        $indikator_kinerja->kondisi_target_anggaran_awal = $request->indikator_kinerja_program_kondisi_target_anggaran_awal;
        $indikator_kinerja->save();

        $opd_id = $request->indikator_kinerja_program_opd_id;

        for ($i=0; $i < count($opd_id); $i++) {
            $opd_program_indikator_kinerja = new OpdProgramIndikatorKinerja;
            $opd_program_indikator_kinerja->program_indikator_kinerja_id = $indikator_kinerja->id;
            $opd_program_indikator_kinerja->opd_id = $opd_id[$i];
            $opd_program_indikator_kinerja->save();
        }

        Alert::success('Berhasil', 'Berhasil Menambahkan Indikator Kinerja untuk Program');
        return redirect()->route('admin.nomenklatur.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $program_indikator = ProgramIndikatorKinerja::find($request->program_indikator_kinerja_id);

        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator->id)->get();

        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
            $program_target_satuan_rp_realisasis = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)->get();
            foreach ($program_target_satuan_rp_realisasis as $program_target_satuan_rp_realisasi) {
                ProgramTargetSatuanRpRealisasi::find($program_target_satuan_rp_realisasi->id)->delete();
            }
            OpdProgramIndikatorKinerja::find($get_opd_program_indikator_kinerja->id)->delete();
        }

        $program_indikator = $program_indikator->delete();

        return response()->json(['success' => 'Berhasil Menghapus Indikator Kinerja untuk Program']);
    }

    public function indikator_kinerja_edit($id)
    {
        $data = ProgramIndikatorKinerja::find($id);

        return response()->json(['result' => $data]);
    }

    public function indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'edit_program_indikator_kinerja_id' => 'required',
            'edit_program_indikator_kinerja_deskripsi' => 'required',
            'edit_program_indikator_kinerja_satuan' => 'required',
            'edit_program_indikator_kinerja_kondisi_target_kinerja_awal' => 'required',
            'edit_program_indikator_kinerja_kondisi_target_anggaran_awal' => 'required',
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal!', $errors->errors()->all());
            return back();
        }

        $program_indikator_kinerja = ProgramIndikatorKinerja::find($request->edit_program_indikator_kinerja_id);
        $program_indikator_kinerja->deskripsi = $request->edit_program_indikator_kinerja_deskripsi;
        $program_indikator_kinerja->satuan = $request->edit_program_indikator_kinerja_satuan;
        $program_indikator_kinerja->kondisi_target_kinerja_awal = $request->edit_program_indikator_kinerja_kondisi_target_kinerja_awal;
        $program_indikator_kinerja->kondisi_target_anggaran_awal = $request->edit_program_indikator_kinerja_kondisi_target_anggaran_awal;
        $program_indikator_kinerja->save();

        Alert::success('Berhasil', 'Berhasil Menambahkan Indikator Kinerja untuk Program');
        return redirect()->route('admin.nomenklatur.index');
    }

    public function opd_indikator_kinerja_edit($id)
    {
        $datas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $id)->get();
        $html = '';
        foreach ($datas as $data) {
            $html .= '<div class="alert alert-secondary alert-dismissable fade show" role="alert">
                '.$data->opd->nama.'
                <button type="button" class="btn btn-close text-body hapus-opd-program-indikator-kinerja" data-bs-dismiss="alert" data-id="'.$data->id.'" aria-hidden="true"></button>
            </div>';
        }

        return response()->json(['html' => $html]);
    }

    public function opd_indikator_kinerja_hapus(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'id' => 'required|array',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $id = $request->id;
        for ($i=0; $i < count($id); $i++) {
            $get_target_realisasies = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $id[$i])->get();
            foreach ($get_target_realisasies as $get_target_realisasi) {
                ProgramTargetSatuanRpRealisasi::find($get_target_realisasi->id)->delete();
            }
            OpdProgramIndikatorKinerja::find($id[$i])->delete();
        }

        return response()->json(['success' => 'Berhasil menghapus opd']);
    }

    public function opd_indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tambah_opd_indikator_program_program_indikator_kinerja_id' => 'required',
            'tambah_opd_indikator_program_opd_id' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        // Cek Data
        $cek = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $request->tambah_opd_indikator_program_program_indikator_kinerja_id)
                ->where('opd_id', $request->tambah_opd_indikator_program_opd_id)->first();
        if($cek)
        {
            return response()->json(['errors' => 'Data sudah ada !, Tidak dapat menyimpan']);
        }

        $opd = new OpdProgramIndikatorKinerja;
        $opd->program_indikator_kinerja_id = $request->tambah_opd_indikator_program_program_indikator_kinerja_id;
        $opd->opd_id = $request->tambah_opd_indikator_program_opd_id;
        $opd->save();

        return response()->json(['success' => 'Berhasil menyimpan data']);
    }

    public function hapus(Request $request)
    {
        if($request->hapus_program_tahun == 'semua')
        {
            $get_perubahan_programs = PivotPerubahanProgram::where('program_id', $request->hapus_program_id)->get();
            foreach ($get_perubahan_programs as $get_perubahan_program) {
                PivotPerubahanProgram::find($get_perubahan_program->id)->delete();
            }

            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $request->hapus_program_id)->get();
            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                    $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->get();
                    foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                        $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->get();
                        foreach ($program_tw_realisasies as $program_tw_realisasi) {
                            ProgramTwRealisasi::find($program_tw_realisasi->id)->delete();
                        }

                        ProgramTargetSatuanRpRealisasi::find($program_target_satuan_rp_realisasi->id)->delete();
                    }

                    OpdProgramIndikatorKinerja::find($opd_program_indikator_kinerja->id)->delete();
                }

                ProgramIndikatorKinerja::find($program_indikator_kinerja->id)->delete();
            }

            $program_rpjmds = ProgramRpjmd::where('program_id', $request->hapus_program_id)->get();
            foreach ($program_rpjmds as $program_rpjmd) {
                $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)->get();
                foreach ($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd) {
                    PivotSasaranIndikatorProgramRpjmd::find($pivot_sasaran_indikator_program_rpjmd->id)->delete();
                }

                $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)->get();
                foreach ($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd) {
                    SasaranPdProgramRpjmd::find($sasaran_pd_program_rpjmd->id)->delete();
                }

                ProgramRpjmd::find($program_rpjmd->id)->delete();
            }

            Program::find($request->hapus_program_id)->delete();
        } else {
            $cek_perubahan_program_1 = PivotPerubahanProgram::where('program_id', $request->hapus_program_id)->where('tahun_perubahan', $request->hapus_program_tahun)->first();
            if($cek_perubahan_program_1)
            {
                PivotPerubahanProgram::find($cek_perubahan_program_1->id)->delete();
            } else {
                $cek_perubahan_program_2 = PivotPerubahanProgram::where('program_id', $request->hapus_program_id)->first();
                if(!$cek_perubahan_program_2)
                {
                    $get_perubahan_programs = PivotPerubahanProgram::where('program_id', $request->hapus_program_id)->get();
                    foreach ($get_perubahan_programs as $get_perubahan_program) {
                        PivotPerubahanProgram::find($get_perubahan_program->id)->delete();
                    }

                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $request->hapus_program_id)->get();
                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                        $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                        foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->get();
                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->get();
                                foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                    ProgramTwRealisasi::find($program_tw_realisasi->id)->delete();
                                }

                                ProgramTargetSatuanRpRealisasi::find($program_target_satuan_rp_realisasi->id)->delete();
                            }

                            OpdProgramIndikatorKinerja::find($opd_program_indikator_kinerja->id)->delete();
                        }

                        ProgramIndikatorKinerja::find($program_indikator_kinerja->id)->delete();
                    }

                    $program_rpjmds = ProgramRpjmd::where('program_id', $request->hapus_program_id)->get();
                    foreach ($program_rpjmds as $program_rpjmd) {
                        $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)->get();
                        foreach ($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd) {
                            PivotSasaranIndikatorProgramRpjmd::find($pivot_sasaran_indikator_program_rpjmd->id)->delete();
                        }

                        $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)->get();
                        foreach ($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd) {
                            SasaranPdProgramRpjmd::find($sasaran_pd_program_rpjmd->id)->delete();
                        }

                        ProgramRpjmd::find($program_rpjmd->id)->delete();
                    }

                    Program::find($request->hapus_program_id)->delete();
                }
            }
        }

        Alert::success('Berhasil', 'Berhasil Menghapus Program');
        return redirect()->route('admin.nomenklatur.index');
    }
}
