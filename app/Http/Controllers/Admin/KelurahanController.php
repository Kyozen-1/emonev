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
use Carbon\Carbon;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

class KelurahanController extends Controller
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
            $data = Kelurahan::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="detail" id="'.$data->id.'" class="detail btn btn-icon waves-effect btn-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="edit" id="'.$data->id.'"
                    class="edit btn btn-icon waves-effect btn-warning" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    return $button;
                })
                ->addColumn('provinsi', function($data){
                    return $data->kecamatan->kabupaten->provinsi->nama;
                })
                ->addColumn('kabupaten', function($data){
                    return $data->kecamatan->kabupaten->nama;
                })
                ->editColumn('kecamatan_id', function($data){
                    return $data->kecamatan->nama;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
        $kecamatan = Kecamatan::pluck('nama', 'id');
        return view('admin.kelurahan.index', [
            'kecamatan' => $kecamatan
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
            'kecamatan_id' => 'required',
            'nama' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $kelurahan = new Kelurahan;
        $kelurahan->kecamatan_id = $request->kecamatan_id;
        $kelurahan->nama = $request->nama;
        $kelurahan->save();

        return response()->json(['success' => 'Berhasil Menambahkan Kelurahan '.$kelurahan->nama]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Kelurahan::find($id);

        $array = [
            'provinsi' => $data->kecamatan->kabupaten->provinsi->nama,
            'kabupaten' => $data->kecamatan->kabupaten->nama,
            'kecamatan' => $data->kecamatan->nama,
            'nama' => $data->nama
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
        $data = Kelurahan::find($id);
        return response()->json(['result' => $data]);
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
            'kecamatan_id' => 'required',
            'nama' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $kelurahan = Kelurahan::find($request->hidden_id);
        $kelurahan->kecamatan_id = $request->kecamatan_id;
        $kelurahan->nama = $request->nama;
        $kelurahan->save();

        return response()->json(['success' => 'Berhasil Merubah Nama Kelurahan Menjadi '.$kelurahan->nama]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Kelurahan::find($id)->delete();

        return response()->json(['success' => 'Berhasil Menghapus']);
    }
}
