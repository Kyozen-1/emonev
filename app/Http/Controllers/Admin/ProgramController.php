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
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
// use App\Models\PivotProgramIndikator;
use App\Imports\ProgramImport;

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
    public function show($id)
    {
        $data = Program::find($id);

        $cek_perubahan = PivotPerubahanProgram::where('program_id', $id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
        $html = '<div>';

        if($cek_perubahan)
        {
            $get_perubahans = PivotPerubahanProgram::where('program_id', $id)->get();
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

        // $html .='</div>';

        // $cek_indikator = PivotProgramIndikator::where('program_id', $id)->first();
        // $indikator = '<div>';

        // if($cek_indikator){
        //     $get_indikators = PivotProgramIndikator::where('program_id', $id)->get();
        //     $indikator .= '<ul>';
        //     foreach ($get_indikators as $get_indikator) {
        //         $indikator .= '<li>'.$get_indikator->indikator.'</li>';
        //     }
        //     $indikator .= '</ul>';
        // } else {
        //     $indikator .= '<p>Tidak ada</p>';
        // }

        // $indikator .='</div>';

        // $array = [
        //     'urusan' => $data->urusan->deskripsi,
        //     'kode' => $data->kode,
        //     'deskripsi' => $data->deskripsi,
        //     'pagu' => 'Rp. '.number_format($data->pagu, 2),
        //     'pivot_program_indikator' => $indikator,
        //     'pivot_perubahan_program' => $html
        // ];

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
    public function edit($id)
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
                'tahun_perubahan' => $cek_perubahan->tahun_perubahan
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
}
