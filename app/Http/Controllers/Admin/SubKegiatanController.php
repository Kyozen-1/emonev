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
use App\Imports\SubKegiatanImport;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotPerubahanUrusan;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\PivotSubKegiatanIndikator;
use App\Models\SubKegiatanIndikatorKinerja;

class SubKegiatanController extends Controller
{
    public function index()
    {
        if(request()->ajax())
        {
            $data = SubKegiatan::latest()->get();
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
                    $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $data->id)->latest()->first();
                    if($cek_perubahan_sub_kegiatan)
                    {
                        $kegiatan_id = $cek_perubahan_sub_kegiatan->kegiatan_id;
                    } else {
                        $kegiatan_id = $data->kegiatan_id;
                    }
                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $kegiatan_id)->latest()->first();
                    if($cek_perubahan_kegiatan)
                    {
                        $program_id = $cek_perubahan_kegiatan->program_id;
                    } else {
                        $kegiatan = Kegiatan::find($kegiatan_id);
                        $program_id = $kegiatan->program_id;
                    }
                    $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_id)->latest()->first();
                    if($cek_perubahan_program){
                        $urusan_id = $cek_perubahan_program->urusan_id;
                    } else {
                        $program = Program::find($program_id);
                        $urusan_id = $program->urusan_id;
                    }
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id',$urusan_id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        return $cek_perubahan_urusan->kode;
                    } else {
                        $urusan = Urusan::find($urusan_id);
                        return $urusan->kode;
                    }
                })
                ->addColumn('program', function($data){
                    $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $data->id)->latest()->first();
                    if($cek_perubahan_sub_kegiatan)
                    {
                        $kegiatan_id = $cek_perubahan_sub_kegiatan->kegiatan_id;
                    } else {
                        $kegiatan_id = $data->kegiatan_id;
                    }
                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $kegiatan_id)->latest()->first();
                    if($cek_perubahan_kegiatan)
                    {
                        $program_id = $cek_perubahan_kegiatan->program_id;
                    } else {
                        $kegiatan = Kegiatan::find($kegiatan_id);
                        $program_id = $kegiatan->program_id;
                    }
                    $cek_perubahan_program = PivotPerubahanProgram::where('program_id',$program_id)->latest()->first();
                    if($cek_perubahan_program)
                    {
                        return $cek_perubahan_program->kode;
                    } else {
                        $program = Program::find($program_id);
                        return $program->kode;
                    }
                })
                ->addColumn('kegiatan_id', function($data){
                    $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $data->id)->latest()->first();
                    if($cek_perubahan_sub_kegiatan)
                    {
                        $kegiatan_id = $cek_perubahan_sub_kegiatan->kegiatan_id;
                    } else {
                        $kegiatan_id = $data->kegiatan_id;
                    }
                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id',$kegiatan_id)->latest()->first();
                    if($cek_perubahan_kegiatan)
                    {
                        return $cek_perubahan_kegiatan->kode;
                    } else {
                        $kegiatan = Kegiatan::find($kegiatan_id);
                        return $kegiatan->kode;
                    }
                })
                ->editColumn('kode', function($data){
                    $cek_perubahan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->kode;
                    } else {
                        return $data->kode;
                    }
                })
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->deskripsi;
                    } else {
                        return $data->deskripsi;
                    }
                })
                ->addColumn('indikator', function($data){
                    $jumlah = PivotSubKegiatanIndikator::whereHas('sub_kegiatan', function($q) use ($data){
                        $q->where('sub_kegiatan_id', $data->id);
                    })->count();

                    return '<a href="'.url('/admin/sub-kegiatan/'.$data->id.'/indikator').'" >'.$jumlah.'</a>';;
                })
                ->rawColumns(['aksi', 'indikator'])
                ->make(true);
        }
        $urusan = Urusan::pluck('deskripsi', 'id');
        return view('admin.sub-kegiatan.index', [
            'urusan' => $urusan
        ]);
    }

    public function get_program(Request $request)
    {
        $get_programs = Program::select('id', 'deskripsi')->where('urusan_id', $request->id)->get();
        $program = [];
        foreach ($get_programs as $get_program) {
            $cek_perubahan_program = PivotPerubahanProgram::select('program_id', 'deskripsi')->where('program_id', $get_program->id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program[] = [
                    'id' => $cek_perubahan_program->program_id,
                    'deskripsi' => $cek_perubahan_program->deskripsi
                ];
            } else {
                $program[] = [
                    'id' => $get_program->id,
                    'deskripsi' => $get_program->deskripsi
                ];
            }
        }
        return response()->json($program);
    }

    public function get_kegiatan(Request $request)
    {
        $get_kegiatans = Kegiatan::select('id', 'deskripsi')->where('program_id', $request->id)->get();
        $kegiatan = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::select('kegiatan_id', 'deskripsi')->where('kegiatan_id', $get_kegiatan->id)->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatan[] = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                ];
            } else {
                $kegiatan[] = [
                    'id' => $get_kegiatan->id,
                    'deskripsi' => $get_kegiatan->deskripsi
                ];
            }
        }
        return response()->json($kegiatan);
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sub_kegiatan_kegiatan_id' => 'required',
            'sub_kegiatan_kode' => 'required',
            'sub_kegiatan_deskripsi' => 'required',
            'sub_kegiatan_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        $cek_sub_kegiatan = SubKegiatan::where('kode', $request->sub_kegiatan_kode)
                                ->where('kegiatan_id', $request->sub_kegiatan_kegiatan_id)
                                ->first();
        if($cek_sub_kegiatan)
        {
            $pivot = new PivotPerubahanSubKegiatan;
            $pivot->sub_kegiatan_id = $cek_sub_kegiatan->id;
            $pivot->kegiatan_id = $request->sub_kegiatan_kegiatan_id;
            $pivot->kode = $request->sub_kegiatan_kode;
            $pivot->deskripsi = $request->sub_kegiatan_deskripsi;
            $pivot->tahun_perubahan = $request->sub_kegiatan_tahun_perubahan;
            if($request->sub_kegiatan_tahun_perubahan > 2020)
            {
                $pivot->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot->status_aturan = 'Sebelum Perubahan';
            }
            $pivot->kabupaten_id = 62;
            $pivot->save();
        } else {
            $sub_kegiatan = new SubKegiatan;
            $sub_kegiatan->kegiatan_id = $request->sub_kegiatan_kegiatan_id;
            $sub_kegiatan->kode = $request->sub_kegiatan_kode;
            $sub_kegiatan->deskripsi = $request->sub_kegiatan_deskripsi;
            $sub_kegiatan->tahun_perubahan = $request->sub_kegiatan_tahun_perubahan;
            if($request->sub_kegiatan_tahun_perubahan > 2020)
            {
                $sub_kegiatan->status_aturan = 'Sesudah Perubahan';
            } else {
                $sub_kegiatan->status_aturan = 'Sebelum Perubahan';
            }
            $sub_kegiatan->kabupaten_id = 62;
            $sub_kegiatan->save();
        }

        return response()->json(['success' => 'Berhasil Menambahkan Kegiatan']);
    }

    public function show($id)
    {
        $data = SubKegiatan::find($id);
        $deskripsi_kegiatan = '';
        $deskripsi_program = '';
        $deskripsi_urusan = '';

        $cek_perubahan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $id)->latest()->first();
        $html = '<div>';

        if($cek_perubahan)
        {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $cek_perubahan->kegiatan_id)->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kode_kegiatan = $cek_perubahan_kegiatan->kode;
                $program_id = $cek_perubahan_kegiatan->program_id;
            } else {
                $kode_kegiatan = $data->kegiatan->kode;
                $program_id = $data->kegiatan->program_id;
            }
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_id)->latest()->first();
            if($cek_perubahan_program)
            {
                $kode_program = $cek_perubahan_program->kode;
                $urusan_id = $cek_perubahan_program->urusan_id;
            } else {
                $program = Program::find($program_id);
                $kode_program = $program->kode;
                $urusan_id = $program->urusan_id;
            }
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $kode_urusan = $cek_perubahan_urusan->kode;
            } else {
                $get_urusan = Urusan::find($urusan_id);
                $kode_urusan = $get_urusan->kode;
            }
            $get_perubahans = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $id)
                                ->where('kegiatan_id', $cek_perubahan->kegiatan_id)->get();
            $html .= '<ul>';
            $html .= '<li><p>
                            Kode Urusan: '.$kode_urusan.' <br>
                            Kode Program: '.$kode_program.' <br>
                            Kode Kegiatan: '.$kode_kegiatan.' <br>
                            Kode: '.$data->kode.'<br>
                            Deskripsi: '.$data->deskripsi.'<br>
                            Tahun Perubahan: '.$data->tahun_perubahan.'<br>
                            Status: <span class="text-primary">Sebelum Perubahan</span>
                        </p></li>';
            $a = 1;
            foreach ($get_perubahans as $get_perubahan) {
                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_perubahan->kegiatan_id)->latest()->first();
                if($cek_perubahan_kegiatan)
                {
                    $kode_kegiatan = $cek_perubahan_kegiatan->kode;
                    $program_id = $cek_perubahan_kegiatan->program_id;
                    $deskripsi_kegiatan = $cek_perubahan_kegiatan->deskripsi;
                } else {
                    $kegiatan = Kegiatan::find($get_perubahan->kegiatan_id);
                    $kode_kegiatan = $kegiatan->kode;
                    $program_id = $kegiatan->program_id;
                    $deskripsi_kegiatan = $kegiatan->deskripsi;
                }
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_id)->latest()->first();
                if($cek_perubahan_program)
                {
                    $kode_program = $cek_perubahan_program->kode;
                    $urusan_id = $cek_perubahan_program->urusan_id;
                    $deskripsi_program = $cek_perubahan_program->deskripsi;
                } else {
                    $program = Program::find($program_id);
                    $kode_program = $program->kode;
                    $urusan_id = $program->urusan_id;
                    $deskripsi_program = $program->deskripsi;
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
                            Kode Kegiatan: '.$kode_kegiatan.' <br>
                            Kode: '.$get_perubahan->kode.'<br>
                            Deskripsi: '.$get_perubahan->deskripsi.'<br>
                            Tahun Perubahan: '.$get_perubahan->tahun_perubahan.'<br>
                            Status: <span class="text-warning">Perubahan '.$a++.'</span>
                        </p></li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>Tidak ada</p>';
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $data->kegiatan_id)->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $program_id = $cek_perubahan_kegiatan->program_id;
                $deskripsi_kegiatan = $cek_perubahan_kegiatan->deskripsi;
            } else {
                $kegiatan = Kegiatan::find($data->kegiatan_id);
                $program_id = $kegiatan->program_id;
                $deskripsi_kegiatan = $kegiatan->deskripsi;
            }
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_id)->latest()->first();
            if($cek_perubahan_program)
            {
                $urusan_id = $cek_perubahan_program->urusan_id;
                $deskripsi_program = $cek_perubahan_program->deskripsi;
            } else {
                $program = Program::find($program_id);
                $urusan_id = $program->urusan_id;
                $deskripsi_program = $program->deskripsi;
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

        // $html .='</div>';

        // $cek_indikator = PivotSubKegiatanIndikator::where('sub_kegiatan_id', $id)->first();
        // $indikator = '<div>';

        // if($cek_indikator){
        //     $get_indikators = PivotSubKegiatanIndikator::where('sub_kegiatan_id', $id)->get();
        //     $indikator .= '<ul>';
        //     foreach ($get_indikators as $get_indikator) {
        //         $indikator .= '<li>'.$get_indikator->indikator.'</li>';
        //     }
        //     $indikator .= '</ul>';
        // } else {
        //     $indikator .= '<p>Tidak ada</p>';
        // }

        // $indikator .='</div>';

        $array = [
            'urusan' => $deskripsi_urusan,
            'program' => $deskripsi_program,
            'kegiatan' => $deskripsi_kegiatan,
            'kode' => $data->kode,
            'deskripsi' => $data->deskripsi,
            'tahun_perubahan' => $data->tahun_perubahan,
            'pivot_perubahan_sub_kegiatan' => $html
        ];

        return response()->json(['result' => $array]);
    }

    public function edit($id)
    {
        $data = SubKegiatan::find($id);

        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $id)->latest()->first();
        if($cek_perubahan_sub_kegiatan)
        {
            $kegiatan_id = $cek_perubahan_sub_kegiatan->kegiatan_id;
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $kegiatan_id)->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $program_id = $cek_perubahan_kegiatan->program_id;
            } else {
                $kegiatan = Kegiatan::find($kegiatan_id);
                $program_id = $kegiatan->program_id;
            }

            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_id)->latest()->first();
            if($cek_perubahan_program)
            {
                $urusan_id = $cek_perubahan_program->urusan_id;
            } else {
                $program = Program::find($program_id);
                $urusan_id = $program->urusan_id;
            }

            $array = [
                'urusan_id' => $urusan_id,
                'program_id' => $program_id,
                'kegiatan_id' => $kegiatan_id,
                'kode' => $cek_perubahan_sub_kegiatan->kode,
                'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi,
                'tahun_perubahan' => $cek_perubahan_sub_kegiatan->tahun_perubahan,
            ];
        } else {
            $kegiatan_id = $data->kegiatan_id;
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $kegiatan_id)->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $program_id = $cek_perubahan_kegiatan->program_id;
            } else {
                $kegiatan = Kegiatan::find($kegiatan_id);
                $program_id = $kegiatan->program_id;
            }

            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_id)->latest()->first();
            if($cek_perubahan_program)
            {
                $urusan_id = $cek_perubahan_program->urusan_id;
            } else {
                $program = Program::find($program_id);

                $urusan_id = $program->urusan_id;
            }

            $array = [
                'urusan_id' => $urusan_id,
                'program_id' => $program_id,
                'kegiatan_id' => $kegiatan_id,
                'kode' => $data->kode,
                'deskripsi' => $data->deskripsi,
                'tahun_perubahan' => $data->tahun_perubahan
            ];
        }

        return response()->json(['result' => $array]);
    }

    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sub_kegiatan_kegiatan_id' => 'required',
            'sub_kegiatan_kode' => 'required',
            'sub_kegiatan_deskripsi' => 'required',
            'sub_kegiatan_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot_perubahan_sub_kegiatan = new PivotPerubahanSubKegiatan;
        $pivot_perubahan_sub_kegiatan->sub_kegiatan_id = $request->sub_kegiatan_hidden_id;
        $pivot_perubahan_sub_kegiatan->kegiatan_id = $request->sub_kegiatan_kegiatan_id;
        $pivot_perubahan_sub_kegiatan->kode = $request->sub_kegiatan_kode;
        $pivot_perubahan_sub_kegiatan->deskripsi = $request->sub_kegiatan_deskripsi;
        $pivot_perubahan_sub_kegiatan->tahun_perubahan = $request->sub_kegiatan_tahun_perubahan;
        $pivot_perubahan_sub_kegiatan->kabupaten_id = 62;
        if($request->sub_kegiatan_tahun_perubahan > 2020)
        {
            $pivot_perubahan_sub_kegiatan->status_aturan = 'Sesudah Perubahan';
        } else {
            $pivot_perubahan_sub_kegiatan->status_aturan = 'Sebelum Perubahan';
        }
        $pivot_perubahan_sub_kegiatan->save();

        return response()->json(['success' => 'Berhasil Merubah Sub Kegiatan']);
    }

    public function impor(Request $request)
    {
        $file = $request->file('impor_sub_kegiatan');
        Excel::import(new SubKegiatanImport, $file->store('temp'));
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
        $deskripsis = json_decode($request->indikator_kinerja_sub_kegiatan_deskripsi, true);
        foreach ($deskripsis as $deskripsi) {
            $indikator_kinerja = new SubKegiatanIndikatorKinerja;
            $indikator_kinerja->sub_kegiatan_id = $request->indikator_kinerja_sub_kegiatan_sub_kegiatan_id;
            $indikator_kinerja->deskripsi = $deskripsi['value'];
            $indikator_kinerja->save();
        }

        Alert::success('Berhasil', 'Berhasil Menambahkan Indikator Kinerja untuk SubKegiatan');
        return redirect()->route('admin.nomenklatur.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        SubKegiatanIndikatorKinerja::find($request->sub_kegiatan_indikator_kinerja_id)->delete();

        return response()->json(['success' => 'Berhasil Menghapus Indikator Kinerja untuk SuKegiatan']);
    }
}
