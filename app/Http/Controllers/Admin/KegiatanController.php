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
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\PivotPerubahanKegiatan;
use App\Models\PivotKegiatanIndikator;
use App\Imports\KegiatanImport;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotPerubahanUrusan;

class KegiatanController extends Controller
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
            $data = Kegiatan::latest()->get();
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
                ->addColumn('urusan', function($data){
                    if($data->program_id)
                    {
                        $cek_perubahan = PivotPerubahanUrusan::where('urusan_id',$data->program->urusan->id)->latest()->first();
                        if($cek_perubahan)
                        {
                            return $cek_perubahan->kode;
                        } else {
                            return $data->program->urusan->kode;
                        }
                    } else {
                        return 'Segera Edit Data!';
                    }
                })
                ->addColumn('program_id', function($data){
                    if($data->program_id)
                    {
                        $cek_perubahan = PivotPerubahanProgram::where('program_id',$data->program_id)->latest()->first();
                        if($cek_perubahan)
                        {
                            return $cek_perubahan->kode;
                        } else {
                            return $data->program->kode;
                        }
                    } else {
                        return 'Segera Edit Data!';
                    }
                })
                ->editColumn('kode', function($data){
                    $cek_perubahan = PivotPerubahanKegiatan::where('kegiatan_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->kode;
                    } else {
                        return $data->kode;
                    }
                })
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan = PivotPerubahanKegiatan::where('kegiatan_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->deskripsi;
                    } else {
                        return $data->deskripsi;
                    }
                })
                ->addColumn('indikator', function($data){
                    $jumlah = PivotKegiatanIndikator::whereHas('kegiatan', function($q) use ($data){
                        $q->where('kegiatan_id', $data->id);
                    })->count();

                    return '<a href="'.url('/admin/kegiatan/'.$data->id.'/indikator').'" >'.$jumlah.'</a>';;
                })
                ->rawColumns(['aksi', 'indikator'])
                ->make(true);
        }
        $urusan = Urusan::pluck('deskripsi', 'id');
        return view('admin.kegiatan.index', [
            'urusan' => $urusan
        ]);
    }

    public function get_program(Request $request)
    {
        $program = Program::where('urusan_id', $request->id)->pluck('deskripsi', 'id');
        return response()->json($program);
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
            'program_id' => 'required',
            'kode' => 'required',
            'deskripsi' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $kegiatan = new Kegiatan;
        $kegiatan->program_id = $request->program_id;
        $kegiatan->kode = $request->kode;
        $kegiatan->deskripsi = $request->deskripsi;
        $kegiatan->tanggal = Carbon::now();
        $kegiatan->kabupaten_id = 62;
        $kegiatan->save();

        return response()->json(['success' => 'Berhasil Menambahkan Kegiatan']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Kegiatan::find($id);
        $deskripsi_program = '';
        $deskripsi_urusan = '';

        $cek_perubahan = PivotPerubahanKegiatan::where('kegiatan_id', $id)->latest()->first();
        $html = '<div>';

        if($cek_perubahan)
        {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $cek_perubahan->program_id)->latest()->first();
            if($cek_perubahan_program)
            {
                $kode_program = $cek_perubahan_program->kode;
                $urusan_id = $cek_perubahan_program->urusan_id;
            } else {
                $kode_program = $data->program->kode;
                $urusan_id = $data->program->urusan_id;
            }
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $kode_urusan = $cek_perubahan_urusan->kode;
            } else {
                $get_urusan = Urusan::find($urusan_id);
                $kode_urusan = $get_urusan->kode;
            }
            $get_perubahans = PivotPerubahanKegiatan::where('program_id', $id)->get();
            $html .= '<ul>';
            $html .= '<li><p>
                            Kode Urusan: '.$kode_urusan.' <br>
                            Kode Program: '.$kode_program.' <br>
                            Kode: '.$data->kode.'<br>
                            Deskripsi: '.$data->deskripsi.'<br>
                            Status: <span class="text-primary">Sebelum Perubahan</span>
                        </p></li>';
            $a = 1;
            foreach ($get_perubahans as $get_perubahan) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_perubahan->program_id)->latest()->first();
                if($cek_perubahan_program)
                {
                    $kode_program = $cek_perubahan_program->kode;
                    $urusan_id = $cek_perubahan_program->urusan_id;
                    $deskripsi_program = $cek_perubahan_program->deskripsi;
                } else {
                    $kode_program = $data->program->kode;
                    $urusan_id = $data->program->urusan_id;
                    $deskripsi_program = $data->program->deskripsi;
                }
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $kode_urusan = $cek_perubahan_urusan->kode;
                    $deskripsi_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $get_urusan = Urusan::find($urusan_id);
                    $kode_urusan = $get_urusan->kode;
                    $deskripsi_urusan = $get_urusan->deskripsi;
                }
                $html .= '<li><p>
                            Kode Urusan: '.$kode_urusan.' <br>
                            Kode Program: '.$kode_program.' <br>
                            Kode: '.$get_perubahan->kode.'<br>
                            Deskripsi: '.$get_perubahan->deskripsi.'<br>
                            Status: <span class="text-warning">Perubahan '.$a++.'</span>
                        </p></li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>Tidak ada</p>';
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $data->program_id)->latest()->first();
            if($cek_perubahan_program)
            {
                $urusan_id = $cek_perubahan_program->urusan_id;
                $deskripsi_program = $cek_perubahan_program->deskripsi;
            } else {
                $urusan_id = $data->program->urusan_id;
                $deskripsi_program = $data->program->deskripsi;
            }
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $deskripsi_urusan = $cek_perubahan_urusan->deskripsi;
            } else {
                $get_urusan = Urusan::find($urusan_id);
                $deskripsi_urusan = $get_urusan->deskripsi;
            }
        }

        $html .='</div>';

        $cek_indikator = PivotKegiatanIndikator::where('kegiatan_id', $id)->first();
        $indikator = '<div>';

        if($cek_indikator){
            $get_indikators = PivotKegiatanIndikator::where('kegiatan_id', $id)->get();
            $indikator .= '<ul>';
            foreach ($get_indikators as $get_indikator) {
                $indikator .= '<li>'.$get_indikator->indikator.'</li>';
            }
            $indikator .= '</ul>';
        } else {
            $indikator .= '<p>Tidak ada</p>';
        }

        $indikator .='</div>';

        $array = [
            'urusan' => $deskripsi_urusan,
            'program' => $deskripsi_program,
            'kode' => $data->kode,
            'deskripsi' => $data->deskripsi,
            'pivot_kegiatan_indikator' => $indikator,
            'pivot_perubahan_kegiatan' => $html
        ];

        return response()->json(['result' => $array]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Kegiatan::find($id);

        $cek_perubahan = PivotPerubahanKegiatan::where('kegiatan_id', $id)->latest()->first();
        if($cek_perubahan)
        {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $cek_perubahan->program_id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program_id = $cek_perubahan_program->program_id;
                $urusan_id = $cek_perubahan_program->urusan_id;
            } else {
                $program_id = $data->program_id;
                $urusan_id = $data->program->urusan_id;
            }

            $array = [
                'urusan_id' => $urusan_id,
                'program_id' => $program_id,
                'kode' => $cek_perubahan->kode,
                'deskripsi' => $cek_perubahan->deskripsi
            ];
        } else {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $data->program_id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program_id = $cek_perubahan_program->program_id;
                $urusan_id = $cek_perubahan_program->urusan_id;
            } else {
                $program_id = $data->program_id;
                $urusan_id = $data->program->urusan_id;
            }

            $array = [
                'urusan_id' => $urusan_id,
                'program_id' => $program_id,
                'kode' => $data->kode,
                'deskripsi' => $data->deskripsi
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
            'program_id' => 'required',
            'kode' => 'required',
            'deskripsi' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot_perubahan_kegiatan = new PivotPerubahanKegiatan;
        $pivot_perubahan_kegiatan->kegiatan_id = $request->hidden_id;
        $pivot_perubahan_kegiatan->program_id = $request->program_id;
        $pivot_perubahan_kegiatan->kode = $request->kode;
        $pivot_perubahan_kegiatan->deskripsi = $request->deskripsi;
        $pivot_perubahan_kegiatan->tanggal = Carbon::now();
        $pivot_perubahan_kegiatan->kabupaten_id = 62;
        $pivot_perubahan_kegiatan->save();

        return response()->json(['success' => 'Berhasil Menambahkan Kegiatan']);
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
        $file = $request->file('impor_kegiatan');
        Excel::import(new KegiatanImport, $file->store('temp'));
        $msg = [session('import_status'), session('import_message')];
        if ($msg[0]) {
            Alert::success('Berhasil', $msg[1]);
            return back();
        } else {
            Alert::error('Gagal', $msg[1]);
            return back();
        }
    }
}
