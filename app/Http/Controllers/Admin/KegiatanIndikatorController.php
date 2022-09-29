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
use App\Imports\KegiatanIndikatorImport;

class KegiatanIndikatorController extends Controller
{
    public function index($kegiatan_id)
    {
        if(request()->ajax())
        {
            $data = PivotKegiatanIndikator::where('kegiatan_id', request()->id)->latest()->get();
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
        $cek_program = PivotPerubahanKegiatan::where('kegiatan_id', $kegiatan_id)->latest()->first();
        if($cek_program)
        {
            $kegiatan = $cek_program;
        } else {
            $kegiatan = Kegiatan::find($kegiatan_id);
        }
        return view('admin.kegiatan.indikator.index', [
            'kegiatan_id' => $kegiatan_id,
            'kegiatan' => $kegiatan
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

        $kegiatan_indikator = new PivotKegiatanIndikator;
        $kegiatan_indikator->kegiatan_id = $request->kegiatan_id;
        $kegiatan_indikator->indikator = $request->indikator;
        $kegiatan_indikator->target = $request->target;
        $kegiatan_indikator->satuan = $request->satuan;
        $kegiatan_indikator->save();

        return response()->json(['success' => 'Berhasil Menambahkan Kegiatan Indikator']);
    }

    public function show($id)
    {
        $data = PivotKegiatanIndikator::find($id);
        return response()->json(['result' => $data]);
    }

    public function edit($id)
    {
        $data = PivotKegiatanIndikator::find($id);
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

        $kegiatan_indikator = PivotKegiatanIndikator::find($request->hidden_id);
        $kegiatan_indikator->indikator = $request->indikator;
        $kegiatan_indikator->target = $request->target;
        $kegiatan_indikator->satuan = $request->satuan;
        $kegiatan_indikator->save();

        return response()->json(['success' => 'Berhasil Merubah Kegiatan Indikator']);
    }

    public function destroy($id)
    {
        PivotKegiatanIndikator::find($id)->delete();

        return response()->json(['success' => 'Berhasil Menghapus']);
    }

    public function impor(Request $request)
    {
        $kegiatan_id = $request->kegiatan_id;
        $file = $request->file('impor_kegiatan_indikator');
        Excel::import(new KegiatanIndikatorImport($kegiatan_id), $file->store('temp'));
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
