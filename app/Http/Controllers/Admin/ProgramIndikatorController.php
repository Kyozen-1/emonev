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
use App\Models\PivotPerubahanProgram;
use App\Models\PivotProgramIndikator;
use App\Imports\ProgramIndikatorImport;
use App\Models\ProgramTargetSatuanRpRealisasi;

class ProgramIndikatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($program_id)
    {
        if(request()->ajax())
        {
            $data = PivotProgramIndikator::where('program_id', request()->id)->latest()->get();
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
        $program = Program::find($program_id);
        return view('admin.program.indikator.index', [
            'program_id' => $program_id,
            'program' => $program
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
            'indikator' => 'required',
            'target' => 'required',
            'satuan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_indikator = new PivotProgramIndikator;
        $program_indikator->program_id = $request->program_id;
        $program_indikator->indikator = $request->indikator;
        $program_indikator->target = $request->target;
        $program_indikator->satuan = $request->satuan;
        $program_indikator->save();

        return response()->json(['success' => 'Berhasil Menambahkan Program Indikator']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = PivotProgramIndikator::find($id);
        return response()->json(['result' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = PivotProgramIndikator::find($id);
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
            'indikator' => 'required',
            'target' => 'required',
            'satuan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_indikator = PivotProgramIndikator::find($request->hidden_id);
        $program_indikator->indikator = $request->indikator;
        $program_indikator->target = $request->target;
        $program_indikator->satuan = $request->satuan;
        $program_indikator->save();

        return response()->json(['success' => 'Berhasil Merubah Program Indikator']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PivotProgramIndikator::find($id)->delete();

        return response()->json(['success' => 'Berhasil Menghapus']);
    }

    public function impor(Request $request)
    {
        $program_id = $request->program_id;
        $file = $request->file('impor_program_indikator');
        Excel::import(new ProgramIndikatorImport($program_id), $file->store('temp'));
        $msg = [session('import_status'), session('import_message')];
        if ($msg[0]) {
            Alert::success('Berhasil', $msg[1]);
            return back();
        } else {
            Alert::error('Gagal', $msg[1]);
            return back();
        }
    }

    public function store_program_target_satuan_rp_realisasi(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'opd_program_indikator_kinerja_id' => 'required',
            'target' => 'required',
            'satuan' => 'required',
            'target_rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_target_satuan_rp_realisasi = new ProgramTargetSatuanRpRealisasi;
        $program_target_satuan_rp_realisasi->opd_program_indikator_kinerja_id = $request->opd_program_indikator_kinerja_id;
        $program_target_satuan_rp_realisasi->target = $request->target;
        $program_target_satuan_rp_realisasi->satuan = $request->satuan;
        $program_target_satuan_rp_realisasi->target_rp = $request->target_rp;
        $program_target_satuan_rp_realisasi->tahun = $request->tahun;
        $program_target_satuan_rp_realisasi->save();

        return response()->json(['success' => 'Berhasil menambahkan target']);
    }

    public function update_program_target_satuan_rp_realisasi(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'program_target_satuan_rp_realisasi' => 'required',
            'program_edit_target' => 'required',
            'program_edit_satuan' => 'required',
            'program_edit_target_rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::find($request->program_target_satuan_rp_realisasi);
        $program_target_satuan_rp_realisasi->target = $request->program_edit_target;
        $program_target_satuan_rp_realisasi->satuan = $request->program_edit_satuan;
        $program_target_satuan_rp_realisasi->target_rp = $request->program_edit_target_rp;
        $program_target_satuan_rp_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubahan Target Program');
        return redirect()->route('admin.nomenklatur.index');
    }
}
