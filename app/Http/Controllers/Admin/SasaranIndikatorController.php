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
use App\Models\Visi;
use App\Models\PivotPerubahanVisi;
use App\Models\Misi;
use App\Models\PivotPerubahanMisi;
use App\Models\Tujuan;
use App\Models\PivotPerubahanTujuan;
use App\Models\Sasaran;
use App\Models\PivotPerubahanSasaran;
use App\Models\PivotSasaranIndikator;
use App\Imports\SasaranIndikatorImport;

class SasaranIndikatorController extends Controller
{
    public function index($sasaran_id)
    {
        if(request()->ajax())
        {
            $data = PivotSasaranIndikator::where('sasaran_id', request()->id)->latest()->get();
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
                ->rawColumns(['aksi'])
                ->make(true);
        }
        $cek_sasaran = PivotPerubahanSasaran::where('sasaran_id', $sasaran_id)->latest()->first();
        if($cek_sasaran)
        {
            $sasaran = $cek_sasaran;
        } else {
            $sasaran = Sasaran::find($sasaran_id);
        }
        return view('admin.sasaran.indikator.index', [
            'sasaran_id' => $sasaran_id,
            'sasaran' => $sasaran
        ]);
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator' => 'required',
            'target' => 'required',
            'satuan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sasaran_indikator = new PivotSasaranIndikator;
        $sasaran_indikator->sasaran_id = $request->sasaran_id;
        $sasaran_indikator->indikator = $request->indikator;
        $sasaran_indikator->target = $request->target;
        $sasaran_indikator->satuan = $request->satuan;
        $sasaran_indikator->save();

        return response()->json(['success' => 'Berhasil Menambahkan Sasaran Indikator']);
    }

    public function show($id)
    {
        $data = PivotSasaranIndikator::find($id);
        return response()->json(['result' => $data]);
    }

    public function edit($id)
    {
        $data = PivotSasaranIndikator::find($id);
        return response()->json(['result' => $data]);
    }

    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator' => 'required',
            'target' => 'required',
            'satuan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sasaran_indikator = PivotSasaranIndikator::find($request->hidden_id);
        $sasaran_indikator->indikator = $request->indikator;
        $sasaran_indikator->target = $request->target;
        $sasaran_indikator->satuan = $request->satuan;
        $sasaran_indikator->save();

        return response()->json(['success' => 'Berhasil Merubah Sasaran Indikator']);
    }

    public function destroy($id)
    {
        PivotSasaranIndikator::find($id)->delete();

        return response()->json(['success' => 'Berhasil Menghapus']);
    }

    public function impor(Request $request)
    {
        $sasaran_id = $request->sasaran_id;
        $file = $request->file('impor_sasaran_indikator');
        Excel::import(new SasaranIndikatorImport($sasaran_id), $file->store('temp'));
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
