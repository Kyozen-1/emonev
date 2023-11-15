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
use App\Models\TahunPeriode;

class TahunPeriodeController extends Controller
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
            $data = TahunPeriode::where('is_delete', '0')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_edit = '<button type="button" name="edit" id="'.$data->id.'"
                        class="edit btn btn-icon waves-effect btn-warning" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    $button_delete = '<button type="button" name="delete" id="'.$data->id.'"
                        class="delete btn btn-icon waves-effect btn-danger" title="Hapus Data"><i class="fas fa-trash"></i></button>';
                    $button = $button_edit .' '. $button_delete;
                    return $button;
                })
                ->addColumn('tahun_periode', function($data){
                    return $data->tahun_awal .' - '.$data->tahun_akhir;
                })
                ->addColumn('status_hubungan', function($data){
                    if($data->urusan)
                    {
                        return '<span class="text-success">Ada Hubungan Data</span>';
                    } else {
                        return '<span class="text-danger">Tidak Ada Hubungan Data</span>';
                    }
                })
                ->rawColumns(['aksi', 'tahun_periode', 'status_hubungan'])
                ->make(true);
        }
        return view('admin.tahun-periode.index');
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
            'tahun_awal' => 'required',
            'tahun_akhir' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $cek_tahun_periode = TahunPeriode::where('status', 'Aktif')->first();
        if($cek_tahun_periode)
        {
            return response()->json(['errors' => 'Tidak bisa menambahkan karena ada periode aktif']);
        }

        $tahun_periode = new TahunPeriode;
        $tahun_periode->tahun_awal = $request->tahun_awal;
        $tahun_periode->tahun_akhir = $request->tahun_akhir;
        $tahun_periode->save();

        return response()->json(['success' => 'Berhasil Menambahkan Tahun Periode ']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = TahunPeriode::find($id);
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
            'status' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        $cek_tahun_periode = TahunPeriode::where('status', 'Aktif')->first();
        if($cek_tahun_periode)
        {
            if($cek_tahun_periode->id != $request->hidden_id)
            {
                return response()->json(['errors' => 'Tidak bisa merubah status karena ada periode lain "Aktif"']);
            }
        }
        $tahun_periode = TahunPeriode::find($request->hidden_id);
        $tahun_periode->status = $request->status;
        $tahun_periode->save();

        return response()->json(['success' => 'Berhasil Merubah Status Tahun Periode']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $tahunPeriode = TahunPeriode::find($id);
            if(!$tahunPeriode)
            {
                return response()->json(['errors' => 'Tahun Periode Tidak ditemukan']);
            }

            if($tahunPeriode->status == 'Aktif')
            {
                return response()->json(['errors' => 'Tahun Periode Masih Aktif']);
            }

            $tahunPeriode->is_delete = '1';
            $tahunPeriode->save();

            return response()->json(['success' => 'Berhasil menghapus data']);
        } catch (\Throwable $th) {
            return response()->json(['errors' => $th]);
        }
    }
}
