<?php

namespace App\Http\Controllers\Opd;

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
    public function update_program_target_satuan_rp_realisasi(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'program_target_satuan_rp_realisasi' => 'required',
            'program_edit_realisasi' => 'required',
            'program_edit_realisasi_rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::find($request->program_target_satuan_rp_realisasi);
        $program_target_satuan_rp_realisasi->realisasi = $request->program_edit_realisasi;
        $program_target_satuan_rp_realisasi->realisasi_rp = $request->program_edit_realisasi_rp;
        $program_target_satuan_rp_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubahan Target Program');
        return redirect()->route('opd.renstra.index');
    }
}
