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

class UrusanController extends Controller
{
    public function index()
    {
        if(request()->ajax())
        {
            $data = Urusan::latest()->get();
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
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan = PivotPerubahanUrusan::where('urusan_id',$data->id)->latest()->first();
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
        return view('admin.urusan.index');
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'kode' => 'required',
            'deskripsi' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $urusan = new Urusan;
        $urusan->kode = $request->kode;
        $urusan->deskripsi = $request->deskripsi;
        $urusan->kabupaten_id = 62;
        $urusan->save();

        return response()->json(['success' => 'Berhasil Menambahkan Urusan']);
    }

    public function show($id)
    {
        $data = Urusan::find($id);

        $cek_perubahan = PivotPerubahanUrusan::where('urusan_id', $id)->first();
        $html = '<div>';

        if($cek_perubahan)
        {
            $get_perubahans = PivotPerubahanUrusan::where('urusan_id', $id)->get();
            $html .= '<ul>';
            $html .= '<li>'.$data->deskripsi.' (Sebelum Perubahan)</li>';
            $a = 1;
            foreach ($get_perubahans as $get_perubahan) {
                $html .= '<li>'.$get_perubahan->deskripsi.' (Perubahan '.$a++.'), '.$get_perubahan->tanggal.'</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>Tidak ada</p>';
        }

        $html .='</div>';

        $array = [
            'kode' => $data->kode,
            'deskripsi' => $data->deskripsi,
            'pivot_perubahan_urusan' => $html
        ];

        return response()->json(['result' => $array]);
    }

    public function edit($id)
    {
        $data = Urusan::find($id);
        $cek_perubahan = PivotPerubahanUrusan::where('urusan_id', $id)->latest()->first();
        if($cek_perubahan)
        {
            $deskripsi = $cek_perubahan->deskripsi;
        } else {
            $deskripsi = $data->deskripsi;
        }

        $array = [
            'id' => $data->id,
            'kode' => $data->kode,
            'deskripsi' => $deskripsi
        ];

        return response()->json(['result' => $array]);
    }

    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'kode' => 'required',
            'deskripsi' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot = new PivotPerubahanUrusan;
        $pivot->urusan_id = $request->hidden_id;
        $pivot->deskripsi = $request->deskripsi;
        $pivot->tanggal = Carbon::now();
        $urusan->kabupaten_id = 62;
        $pivot->save();

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
}
