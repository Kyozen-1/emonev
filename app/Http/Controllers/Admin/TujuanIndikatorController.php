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
use App\Models\PivotTujuanIndikator;
use App\Imports\TujuanIndikatorImport;

class TujuanIndikatorController extends Controller
{
    public function index($tujuan_id)
    {
        if(request()->ajax())
        {
            $data = PivotTujuanIndikator::where('tujuan_id', request()->id)->latest()->get();
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
        $cek_tujuan = PivotPerubahanTujuan::where('tujuan_id', $tujuan_id)->latest()->first();
        if($cek_tujuan)
        {
            $tujuan = $cek_tujuan;
        } else {
            $tujuan = Tujuan::find($tujuan_id);
        }
        return view('admin.tujuan.indikator.index', [
            'tujuan_id' => $tujuan_id,
            'tujuan' => $tujuan
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

        $tujuan_indikator = new PivotTujuanIndikator;
        $tujuan_indikator->tujuan_id = $request->tujuan_id;
        $tujuan_indikator->indikator = $request->indikator;
        $tujuan_indikator->target = $request->target;
        $tujuan_indikator->satuan = $request->satuan;
        $tujuan_indikator->save();

        return response()->json(['success' => 'Berhasil Menambahkan Tujuan Indikator']);
    }

    public function show($id)
    {
        $data = PivotTujuanIndikator::find($id);
        return response()->json(['result' => $data]);
    }

    public function edit($id)
    {
        $data = PivotTujuanIndikator::find($id);
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

        $tujuan_indikator = PivotTujuanIndikator::find($request->hidden_id);
        $tujuan_indikator->indikator = $request->indikator;
        $tujuan_indikator->target = $request->target;
        $tujuan_indikator->satuan = $request->satuan;
        $tujuan_indikator->save();

        return response()->json(['success' => 'Berhasil Merubah Tujuan Indikator']);
    }

    public function destroy($id)
    {
        PivotTujuanIndikator::find($id)->delete();

        return response()->json(['success' => 'Berhasil Menghapus']);
    }

    public function impor(Request $request)
    {
        $tujuan_id = $request->tujuan_id;
        $file = $request->file('impor_tujuan_indikator');
        Excel::import(new TujuanIndikatorImport($tujuan_id), $file->store('temp'));
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
