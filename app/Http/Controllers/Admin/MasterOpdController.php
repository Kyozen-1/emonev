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
use App\Models\JenisOpd;
use App\Models\MasterOpd;

class MasterOpdController extends Controller
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
            $data = MasterOpd::latest()->get();
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
                ->editColumn('jenis_opd_id', function($data){
                    if($data->jenis_opd_id)
                    {
                        return $data->jenis_opd->nama;
                    } else {
                        return '';
                    }
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
        $jenis_opd = JenisOpd::pluck('nama', 'id');
        return view('admin.master-opd.index', [
            'jenis_opd' => $jenis_opd
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
            'jenis_opd_id' => 'required',
            'nama' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $master_opd = new MasterOpd;
        $master_opd->jenis_opd_id = $request->jenis_opd_id;
        $master_opd->nama = $request->nama;
        $master_opd->save();

        return response()->json(['success' => 'Berhasil Menambahkan Master OPD '.$master_opd->nama]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = MasterOpd::find($id);

        $array = [
            'jenis_opd' => $data->jenis_opd->nama,
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
        $data = MasterOpd::find($id);
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
            'jenis_opd_id' => 'required',
            'nama' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $master_opd = MasterOpd::find($request->hidden_id);
        $master_opd->jenis_opd_id = $request->jenis_opd_id;
        $master_opd->nama = $request->nama;
        $master_opd->save();

        return response()->json(['success' => 'Berhasil Merubah Master OPD Menjadi '.$master_opd->nama]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cek_master_opd = MasterOpd::where('id',$id);
        $cek_master_opd = $cek_master_opd->where(function($q){
            $q->whereHas('program_rpjmd')->orWhereHas('opd');
        })->first();
        if($cek_master_opd)
        {
            return response()->json(['errors' => 'Tidak dapat menghapus! Data ini memiliki relasi data']);
        } else {
            MasterOpd::find($id)->delete();

            return response()->json(['success' => 'Berhasil Menghapus']);
        }
    }
}
