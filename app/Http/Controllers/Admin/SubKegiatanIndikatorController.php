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
use App\Imports\SubKegiatanIndikatorImport;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotPerubahanUrusan;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\PivotSubKegiatanIndikator;

class SubKegiatanIndikatorController extends Controller
{
    public function index($sub_kegiatan_id)
    {
        if(request()->ajax())
        {
            $data = PivotSubKegiatanIndikator::where('sub_kegiatan_id', request()->id)->latest()->get();
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
        $cek_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $sub_kegiatan_id)->latest()->first();
        if($cek_sub_kegiatan)
        {
            $sub_kegiatan = $cek_sub_kegiatan;
        } else {
            $sub_kegiatan = SubKegiatan::find($sub_kegiatan_id);
        }
        return view('admin.sub-kegiatan.indikator.index', [
            'sub_kegiatan_id' => $sub_kegiatan_id,
            'sub_kegiatan' => $sub_kegiatan
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

        $sub_kegiatan_indikator = new PivotSubKegiatanIndikator;
        $sub_kegiatan_indikator->sub_kegiatan_id = $request->sub_kegiatan_id;
        $sub_kegiatan_indikator->indikator = $request->indikator;
        $sub_kegiatan_indikator->target = $request->target;
        $sub_kegiatan_indikator->satuan = $request->satuan;
        $sub_kegiatan_indikator->save();

        return response()->json(['success' => 'Berhasil Menambahkan Sub Kegiatan Indikator']);
    }

    public function show($id)
    {
        $data = PivotSubKegiatanIndikator::find($id);
        return response()->json(['result' => $data]);
    }

    public function edit($id)
    {
        $data = PivotSubKegiatanIndikator::find($id);
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

        $sub_kegiatan_indikator = PivotSubKegiatanIndikator::find($request->hidden_id);
        $sub_kegiatan_indikator->indikator = $request->indikator;
        $sub_kegiatan_indikator->target = $request->target;
        $sub_kegiatan_indikator->satuan = $request->satuan;
        $sub_kegiatan_indikator->save();

        return response()->json(['success' => 'Berhasil Merubah Sub Kegiatan Indikator']);
    }

    public function destroy($id)
    {
        PivotSubKegiatanIndikator::find($id)->delete();

        return response()->json(['success' => 'Berhasil Menghapus']);
    }

    public function impor(Request $request)
    {
        $sub_kegiatan_id = $request->sub_kegiatan_id;
        $file = $request->file('impor_sub_kegiatan_indikator');
        Excel::import(new SubKegiatanIndikatorImport($sub_kegiatan_id), $file->store('temp'));
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
