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
use App\Models\TahunPeriode;
use App\Models\Visi;
use App\Models\PivotPerubahanVisi;
use App\Models\Misi;
use App\Models\PivotPerubahanMisi;
use App\Models\Tujuan;
use App\Models\PivotPerubahanTujuan;
use App\Models\Sasaran;
use App\Models\PivotPerubahanSasaran;
use App\Models\PivotSasaranIndikator;
use App\Models\ProgramRpjmd;
use App\Models\PivotOpdProgramRpjmd;
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Models\MasterOpd;
use App\Models\PivotSasaranIndikatorProgramRpjmd;
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotProgramKegiatanRenstra;
use App\Models\TargetRpPertahunProgram;

class ProgramRpjmdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $get_urusans = Urusan::all();
        $urusans = [];
        foreach ($get_urusans as $get_urusan) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $urusans[] = [
                    'id' => $cek_perubahan_urusan->urusan_id,
                    'deskripsi' => $cek_perubahan_urusan->deskripsi
                ];
            } else {
                $urusan = Urusan::find($get_urusan->id);
                $urusans[] = [
                    'id' => $urusan->id,
                    'deskripsi' => $urusan->deskripsi
                ];
            }
        }

        $master_opd = MasterOpd::pluck('nama', 'id');

        $sasarans = Sasaran::all();
        return view('admin.program-rpjmd.index', [
            'urusans' => $urusans,
            'master_opd' => $master_opd,
            'sasarans' => $sasarans
        ]);
    }

    // public function get_program(Request $request)
    // {
    //     $get_programs = Program::select('id', 'deskripsi')->where('urusan_id', $request->id)->get();
    //     $program = [];
    //     foreach ($get_programs as $get_program) {
    //         $cek_perubahan_program = PivotPerubahanProgram::select('program_id', 'deskripsi')->where('program_id', $get_program->id)->latest()->first();
    //         if($cek_perubahan_program)
    //         {
    //             $program[] = [
    //                 'id' => $cek_perubahan_program->program_id,
    //                 'deskripsi' => $cek_perubahan_program->deskripsi
    //             ];
    //         } else {
    //             $program[] = [
    //                 'id' => $get_program->id,
    //                 'deskripsi' => $get_program->deskripsi
    //             ];
    //         }
    //     }
    //     return response()->json($program);
    // }

    // public function get_sasaran($id)
    // {
    //     $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $id)->latest()->first();
    //     if($cek_perubahan_sasaran)
    //     {
    //         $tujuan_id = $cek_perubahan_sasaran->tujuan_id;
    //         $kode_sasaran = $cek_perubahan_sasaran->kode;
    //         $deskripsi_sasaran = $cek_perubahan_sasaran->deskripsi;
    //     } else {
    //         $sasaran = Sasaran::find($id);
    //         $tujuan_id = $sasaran->tujuan_id;
    //         $kode_sasaran = $sasaran->kode;
    //         $deskripsi_sasaran = $sasaran->deskripsi;
    //     }

    //     $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $tujuan_id)->latest()->first();
    //     if($cek_perubahan_tujuan)
    //     {
    //         $misi_id = $cek_perubahan_tujuan->misi_id;
    //         $kode_tujuan = $cek_perubahan_tujuan->kode;
    //         $deskripsi_tujuan = $cek_perubahan_tujuan->deskripsi;
    //     } else {
    //         $tujuan = Tujuan::find($tujuan_id);
    //         $misi_id = $tujuan->misi_id;
    //         $kode_tujuan = $tujuan->kode;
    //         $deskripsi_tujuan = $tujuan->deskripsi;
    //     }

    //     $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
    //     if($cek_perubahan_misi)
    //     {
    //         $kode_misi = $cek_perubahan_misi->kode;
    //         $deskripsi_misi =  $cek_perubahan_misi->deskripsi;
    //     } else {
    //         $misi = Misi::find($misi_id);
    //         $kode_misi = $misi->kode;
    //         $deskripsi_misi = $misi->deskripsi;
    //     }

    //     $array = [
    //         'sasaran' => $kode_sasaran . '. ' . $deskripsi_sasaran,
    //         'tujuan' => $kode_tujuan . '. '. $deskripsi_tujuan,
    //         'misi' => $kode_misi . '. '.$deskripsi_misi,
    //     ];

    //     return response()->json(['result' => $array]);
    // }

    // public function store(Request $request)
    // {
    //     $errors = Validator::make($request->all(), [
    //         'sasaran_id' => 'required',
    //         'urusan_id' => 'required',
    //         'program_id' => 'required',
    //         'status_program' => 'required',
    //         'opd_id' => 'required'
    //     ]);

    //     if($errors -> fails())
    //     {
    //         return response()->json(['errors' => $errors->errors()->all()]);
    //     }

    //     $program_rpjmd = new ProgramRpjmd;
    //     $program_rpjmd->program_id = $request->program_id;
    //     $program_rpjmd->sasaran_id = $request->sasaran_id;
    //     $program_rpjmd->status_program = $request->status_program;
    //     $program_rpjmd->opd_id = $request->opd_id;
    //     $program_rpjmd->save();

    //     return response()->json(['success' => 'berhasil']);
    // }

    // public function edit($id)
    // {
    //     $program_rpjmd = ProgramRpjmd::find($id);
    //     $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $program_rpjmd->sasaran_id)->latest()->first();
    //     if($cek_perubahan_sasaran)
    //     {
    //         $tujuan_id = $cek_perubahan_sasaran->tujuan_id;
    //         $kode_sasaran = $cek_perubahan_sasaran->kode;
    //         $deskripsi_sasaran = $cek_perubahan_sasaran->deskripsi;
    //     } else {
    //         $sasaran = Sasaran::find($id);
    //         $tujuan_id = $sasaran->tujuan_id;
    //         $kode_sasaran = $sasaran->kode;
    //         $deskripsi_sasaran = $sasaran->deskripsi;
    //     }

    //     $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $tujuan_id)->latest()->first();
    //     if($cek_perubahan_tujuan)
    //     {
    //         $misi_id = $cek_perubahan_tujuan->misi_id;
    //         $kode_tujuan = $cek_perubahan_tujuan->kode;
    //         $deskripsi_tujuan = $cek_perubahan_tujuan->deskripsi;
    //     } else {
    //         $tujuan = Tujuan::find($tujuan_id);
    //         $misi_id = $tujuan->misi_id;
    //         $kode_tujuan = $tujuan->kode;
    //         $deskripsi_tujuan = $tujuan->deskripsi;
    //     }

    //     $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
    //     if($cek_perubahan_misi)
    //     {
    //         $kode_misi = $cek_perubahan_misi->kode;
    //         $deskripsi_misi =  $cek_perubahan_misi->deskripsi;
    //     } else {
    //         $misi = Misi::find($misi_id);
    //         $kode_misi = $misi->kode;
    //         $deskripsi_misi = $misi->deskripsi;
    //     }

    //     $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_rpjmd->program_id)->latest()->first();
    //     if($cek_perubahan_program)
    //     {
    //         $urusan_id = $cek_perubahan_program->urusan_id;
    //     } else {
    //         $program = Program::find($program_rpjmd->program_id);
    //         $urusan_id = $program->urusan_id;
    //     }

    //     $array = [
    //         'sasaran' => $kode_sasaran . '. ' . $deskripsi_sasaran,
    //         'tujuan' => $kode_tujuan . '. '. $deskripsi_tujuan,
    //         'misi' => $kode_misi . '. '.$deskripsi_misi,
    //         'sasaran_id' => $program_rpjmd->sasaran_id,
    //         'program_id' => $program_rpjmd->program_id,
    //         'urusan_id' => $urusan_id,
    //         'status_program' => $program_rpjmd->status_program,
    //         'opd_id' => $program_rpjmd->opd_id,
    //     ];

    //     return response()->json(['result' => $array]);
    // }

    // public function update(Request $request)
    // {
    //     $errors = Validator::make($request->all(), [
    //         'sasaran_id' => 'required',
    //         'urusan_id' => 'required',
    //         'program_id' => 'required',
    //         'status_program' => 'required',
    //         'opd_id' => 'required'
    //     ]);

    //     if($errors -> fails())
    //     {
    //         return response()->json(['errors' => $errors->errors()->all()]);
    //     }

    //     $program_rpjmd = ProgramRpjmd::find($request->hidden_id);
    //     $program_rpjmd->program_id = $request->program_id;
    //     $program_rpjmd->sasaran_id = $request->sasaran_id;
    //     $program_rpjmd->status_program = $request->status_program;
    //     $program_rpjmd->opd_id = $request->opd_id;
    //     $program_rpjmd->save();

    //     return response()->json(['success' => 'berhasil']);
    // }

    // public function destroy($id)
    // {
    //     ProgramRpjmd::find($id)->delete();

    //     return response()->json(['success' => 'Berhasil']);
    // }

    public function get_program(Request $request)
    {
        $get_programs = Program::select('id', 'deskripsi', 'kode')->where('urusan_id', $request->id)->get();
        $program = [];
        foreach ($get_programs as $get_program) {
            $cek_perubahan_program = PivotPerubahanProgram::select('program_id', 'deskripsi', 'kode')->where('program_id', $get_program->id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program[] = [
                    'id' => $cek_perubahan_program->program_id,
                    'deskripsi' => $cek_perubahan_program->deskripsi,
                    'kode' => $cek_perubahan_program->kode
                ];
            } else {
                $program[] = [
                    'id' => $get_program->id,
                    'deskripsi' => $get_program->deskripsi,
                    'kode' => $get_program->kode
                ];
            }
        }
        return response()->json($program);
    }

    public function get_tujuan(Request $request)
    {
        $get_tujuans = Tujuan::select('id', 'deskripsi', 'kode')->where('misi_id', $request->id)->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::select('tujuan_id', 'deskripsi', 'kode')
                                    ->where('tujuan_id', $get_tujuan->id)
                                    ->latest()->first();
            if($cek_perubahan_tujuan)
            {
                $tujuan[] = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                    'kode' => $cek_perubahan_tujuan->kode
                ];
            } else {
                $tujuan[] = [
                    'id' => $get_tujuan->id,
                    'deskripsi' => $get_tujuan->deskripsi,
                    'kode' => $get_tujuan->kode
                ];
            }
        }
        return response()->json($tujuan);
    }

    public function get_sasaran(Request $request)
    {
        $get_sasarans = Sasaran::select('id', 'deskripsi', 'kode')->where('tujuan_id', $request->id)->get();
        $sasaran = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::select('sasaran_id', 'deskripsi', 'kode')
                                    ->where('sasaran_id', $get_sasaran->id)
                                    ->latest()->first();
            if($cek_perubahan_sasaran)
            {
                $sasaran[] = [
                    'id' => $cek_perubahan_sasaran->sasaran_id,
                    'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                    'kode' => $cek_perubahan_sasaran->kode
                ];
            } else {
                $sasaran[] = [
                    'id' => $get_sasaran->id,
                    'deskripsi' => $get_sasaran->deskripsi,
                    'kode' => $get_sasaran->kode
                ];
            }
        }
        return response()->json($sasaran);
    }

    public function get_sasaran_indikator(Request $request)
    {
        $sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $request->id)->get();
        return response()->json($sasaran_indikators);
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'program_urusan_id' => 'required',
            'program_program_id' => 'required',
            'program_status_program' => 'required',
            'program_pagu' => 'required',
            'program_misi_id' => 'required',
            'program_tujuan_id' => 'required',
            'program_sasaran_id' => 'required',
            'program_sasaran_indikator_id.*' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_rpjmd = new ProgramRpjmd;
        $program_rpjmd->program_id = $request->program_program_id;
        $program_rpjmd->status_program = $request->program_status_program;
        $program_rpjmd->pagu = $request->program_pagu;
        $program_rpjmd->save();

        $sasaran_indikator_id = $request->program_sasaran_indikator_id;
        $opd_id = $request->program_opd_id;

        for ($i=0; $i < count($sasaran_indikator_id); $i++) {
            $pivot_sasaran_indikator_program_rpjmd = new PivotSasaranIndikatorProgramRpjmd;
            $pivot_sasaran_indikator_program_rpjmd->program_rpjmd_id = $program_rpjmd->id;
            $pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_id = $sasaran_indikator_id[$i];
            $pivot_sasaran_indikator_program_rpjmd->save();
        }

        for ($i=0; $i < count($opd_id); $i++) {
            $pivot_opd_program_rpjmd = new PivotOpdProgramRpjmd;
            $pivot_opd_program_rpjmd->program_rpjmd_id = $program_rpjmd->id;
            $pivot_opd_program_rpjmd->opd_id = $opd_id[$i];
            $pivot_opd_program_rpjmd->save();
        }

        $get_visis = Visi::all();
        $visis = [];
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $tahun_sekarang)
                                    ->latest()->first();
            if($cek_perubahan_visi)
            {
                $visis[] = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'deskripsi' => $cek_perubahan_visi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_visi->tahun_perubahan
                ];
            } else {
                $visis[] = [
                    'id' => $get_visi->id,
                    'deskripsi' => $get_visi->deskripsi,
                    'tahun_perubahan' => $get_visi->tahun_perubahan
                ];
            }
        }

        $html = '<div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="onOffTaggingProgram" checked>
                            <label class="form-check-label" for="onOffTaggingProgram">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="95%">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase program-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class=" collapse" id="program_visi'.$visi['id'].'">
                                            <table class="table table-condensed table-striped">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                    $misis = [];
                                                    foreach ($get_misis as $get_misi) {
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->where('tahun_perubahan', $tahun_sekarang)
                                                                                ->latest()->first();
                                                        if($cek_perubahan_misi)
                                                        {
                                                            $misis[] = [
                                                                'id' => $cek_perubahan_misi->misi_id,
                                                                'kode' => $cek_perubahan_misi->kode,
                                                                'deskripsi' => $cek_perubahan_misi->deskripsi,
                                                                'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan,
                                                            ];
                                                        } else {
                                                            $misis[] = [
                                                                'id' => $get_misi->id,
                                                                'kode' => $get_misi->kode,
                                                                'deskripsi' => $get_misi->deskripsi,
                                                                'tahun_perubahan' => $get_misi->tahun_perubahan,
                                                            ];
                                                        }
                                                    }
                                                    $a = 1;
                                                    foreach ($misis as $misi) {
                                                        $html .= '<tr>
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle">
                                                                        '.$misi['deskripsi'].'
                                                                        <br>';
                                                                        if($a == 1 || $a == 2)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Aman]</span>';
                                                                        }
                                                                        if($a == 3)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Mandiri]</span>';
                                                                        }
                                                                        if($a == 4)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Sejahtera]</span>';
                                                                        }
                                                                        if($a == 5)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Berahlak]</span>';
                                                                        }
                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class=" collapse" id="program_misi'.$misi['id'].'">
                                                                            <table class="table table-condensed table-striped">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan', $tahun_sekarang)
                                                                                                                ->latest()
                                                                                                                ->first();
                                                                                        if($cek_perubahan_tujuan)
                                                                                        {
                                                                                            $tujuans[] = [
                                                                                                'id' => $cek_perubahan_tujuan->tujuan_id,
                                                                                                'kode' => $cek_perubahan_tujuan->kode,
                                                                                                'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                                                                                                'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan,
                                                                                            ];
                                                                                        } else {
                                                                                            $tujuans[] = [
                                                                                                'id' => $get_tujuan->id,
                                                                                                'kode' => $get_tujuan->kode,
                                                                                                'deskripsi' => $get_tujuan->deskripsi,
                                                                                                'tahun_perubahan' => $get_tujuan->tahun_perubahan,
                                                                                            ];
                                                                                        }
                                                                                    }
                                                                                    foreach ($tujuans as $tujuan) {
                                                                                        $html .= '<tr>
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                                                                                                        '.$tujuan['deskripsi'].'
                                                                                                        <br>';
                                                                                                        if($a == 1 || $a == 2)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Aman]</span>';
                                                                                                        }
                                                                                                        if($a == 3)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Mandiri]</span>';
                                                                                                        }
                                                                                                        if($a == 4)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Sejahtera]</span>';
                                                                                                        }
                                                                                                        if($a == 5)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Berahlak]</span>';
                                                                                                        }
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class=" collapse" id="program_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $tahun_sekarang)
                                                                                                                                                    ->latest()->first();
                                                                                                                        if($cek_perubahan_sasaran)
                                                                                                                        {
                                                                                                                            $sasarans[] = [
                                                                                                                                'id' => $cek_perubahan_sasaran->sasaran_id,
                                                                                                                                'kode' => $cek_perubahan_sasaran->kode,
                                                                                                                                'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                                                                                                                                'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan,
                                                                                                                            ];
                                                                                                                        } else {
                                                                                                                            $sasarans[] = [
                                                                                                                                'id' => $get_sasaran->id,
                                                                                                                                'kode' => $get_sasaran->kode,
                                                                                                                                'deskripsi' => $get_sasaran->deskripsi,
                                                                                                                                'tahun_perubahan' => $get_sasaran->tahun_perubahan,
                                                                                                                            ];
                                                                                                                        }
                                                                                                                    }
                                                                                                                    foreach ($sasarans as $sasaran) {
                                                                                                                        $html .= '<tr>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="95%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>';
                                                                                                                                        if($a == 1 || $a == 2)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Aman]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 3)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Mandiri]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 4)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 5)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Berahlak]</span>';
                                                                                                                                        }
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class=" collapse" id="program_sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="5%"><strong>No</strong></th>
                                                                                                                                                        <th width="45%"><strong>Sasaran Indikator</strong></th>
                                                                                                                                                        <th width="25%"><strong>Target</strong></th>
                                                                                                                                                        <th width="25%"><strong>Satuan</strong></th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                    $sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                    $b = 1;
                                                                                                                                                    foreach ($sasaran_indikators as $sasaran_indikator) {
                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_rpjmd'.$sasaran_indikator['id'].'" class="accordion-toggle">'.$b++.'</td>
                                                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_rpjmd'.$sasaran_indikator['id'].'" class="accordion-toggle">
                                                                                                                                                                        '.$sasaran_indikator['indikator'].'
                                                                                                                                                                        <br>';
                                                                                                                                                                        if($a == 1 || $a == 2)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Aman]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 3)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 4)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 5)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                                                    </td>
                                                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_rpjmd'.$sasaran_indikator['id'].'" class="accordion-toggle">
                                                                                                                                                                        '.$sasaran_indikator['target'].'
                                                                                                                                                                    </td>
                                                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_rpjmd'.$sasaran_indikator['id'].'" class="accordion-toggle">
                                                                                                                                                                        '.$sasaran_indikator['satuan'].'
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>
                                                                                                                                                                <tr>
                                                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                                                        <div class=" collapse" id="program_rpjmd'.$sasaran_indikator['id'].'">
                                                                                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th width="5%"><strong>No</strong></th>
                                                                                                                                                                                        <th width="35%"><strong>Program RPJMD</strong></th>
                                                                                                                                                                                        <th width="5%"><strong>Target</strong></th>
                                                                                                                                                                                        <th width="5%"><strong>Satuan</strong></th>
                                                                                                                                                                                        <th width="10%"><strong>Rp</strong></th>
                                                                                                                                                                                        <th width="20%"><strong>OPD</strong></th>
                                                                                                                                                                                        <th width="10%"><strong>Pagu</strong></th>
                                                                                                                                                                                        <th width="10%"><strong>Aksi</strong></th>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>';
                                                                                                                                                                                    $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran_indikator){
                                                                                                                                                                                        $q->where('sasaran_indikator_id', $sasaran_indikator['id']);
                                                                                                                                                                                    })->get();
                                                                                                                                                                                    $programs = [];
                                                                                                                                                                                    foreach ($get_program_rpjmds as $get_program_rpjmd) {
                                                                                                                                                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                                                                                                                                                                                                    ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                                                                                                        if($cek_perubahan_program)
                                                                                                                                                                                        {
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $cek_perubahan_program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu,
                                                                                                                                                                                            ];
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $program = Program::find($get_program_rpjmd->program_id);
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu,
                                                                                                                                                                                            ];
                                                                                                                                                                                        }
                                                                                                                                                                                    }
                                                                                                                                                                                    $c = 1;
                                                                                                                                                                                    foreach ($programs as $program) {
                                                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                                                <td>'.$c++.'</td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    '.$program['deskripsi'];
                                                                                                                                                                                                    if($program['status_program'] == "Program Prioritas")
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= ' <i title="Program Prioritas" class="fas fa-star text-primary"></i>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= ' <br> ';
                                                                                                                                                                                                    if($a == 1 || $a == 2)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Aman]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 3)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 4)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 5)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-danger text-uppercase program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                                                                                </td>';
                                                                                                                                                                                                $cek_target_rps = TargetRpPertahunProgram::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                                    ->where('tahun', $tahun_sekarang)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_target_rps)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $get_target_rps = TargetRpPertahunProgram::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                                    ->where('tahun', $tahun_sekarang)
                                                                                                                                                                                                                    ->get();
                                                                                                                                                                                                    $program_target = [];
                                                                                                                                                                                                    $program_rp = [];
                                                                                                                                                                                                    foreach ($get_target_rps as $get_target_rp) {
                                                                                                                                                                                                        $program_target[] = $get_target_rp->target;
                                                                                                                                                                                                        $program_rp[] = $get_target_rp->rp;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.array_sum($program_target).'</td>
                                                                                                                                                                                                <td>'.$get_target_rp->satuan.'</td>
                                                                                                                                                                                                <td>Rp. '.number_format(array_sum($program_rp), 2).'</td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td></td>
                                                                                                                                                                                                <td></td>
                                                                                                                                                                                                <td></td>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $html .= '<td>';
                                                                                                                                                                                                $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $program['id'])->get();
                                                                                                                                                                                                $html .= '<ul>';
                                                                                                                                                                                                    foreach ($get_opds as $get_opd) {
                                                                                                                                                                                                        $html .= '<li>'.$get_opd->opd->nama.'</li>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                $html.='</ul>';
                                                                                                                                                                                                $html.= '</td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    Rp. '.number_format($program['pagu'], 2).'
                                                                                                                                                                                                </td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-program-rpjmd" data-program-rpjmd-id="'.$program['id'].'" type="button" title="Detail Program"><i class="fas fa-eye"></i></button>
                                                                                                                                                                                                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-program-rpjmd" data-program-rpjmd-id="'.$program['id'].'" type="button" title="Edit Program"><i class="fas fa-edit"></i></button>
                                                                                                                                                                                                </td>
                                                                                                                                                                                            </tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                $html .= '</tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </div>
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>';
                                                                                                                                                    }
                                                                                                                                                $html .= '</tbody>
                                                                                                                                            </table>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                </tr>';
                                                                                                                    }
                                                                                                                $html .= '</tbody>
                                                                                                            </table>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>';
                                                                                    }
                                                                                $html .= '</tbody>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>';
                                                        $a++;
                                                    }
                                                $html .= '</tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>';
                            }
                            $html .='</tbody>
                        </table>
                    </div>
                </div>';
        return response()->json(['success' => $html]);
    }

    public function detail($id)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('program_rpjmd_id', $id)->get();
        $pivot_sasaran_indikators = [];
        $sasaran_indikator = '';
        foreach ($get_pivot_sasaran_indikator_program_rpjmds as $get_pivot_sasaran_indikator_program_rpjmd) {
            $pivot_sasaran_indikator = PivotSasaranIndikator::find($get_pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_id);
            $sasaran_indikator .= '<tr>';
                $sasaran_indikator .= '<td>'.$pivot_sasaran_indikator->indikator.'</td>';
                $sasaran_indikator .= '<td>'.$pivot_sasaran_indikator->target.'</td>';
                $sasaran_indikator .= '<td>'.$pivot_sasaran_indikator->satuan.'</td>';
            $sasaran_indikator .= '</tr>';
        }

        $first_pivot_sasaran_indikator_program_rpjmd = PivotSasaranIndikatorProgramRpjmd::where('program_rpjmd_id', $id)->first();
        $first_pivot_sasaran_indikator = PivotSasaranIndikator::find($first_pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_id);
        // Sasaran
        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $first_pivot_sasaran_indikator->sasaran_id)->orderBy('tahun_perubahan', 'desc')
                                    ->latest()->first();
        if($cek_perubahan_sasaran)
        {
            $sasaran = $cek_perubahan_sasaran->deskripsi;
            $sasaran_kode = $cek_perubahan_sasaran->kode;
            $tujuan_id = $cek_perubahan_sasaran->tujuan_id;
        } else {
            $get_sasaran = Sasaran::find($first_pivot_sasaran_indikator->sasaran_id);
            $sasaran = $get_sasaran->deskripsi;
            $sasaran_kode = $get_sasaran->kode;
            $tujuan_id = $get_sasaran->tujuan_id;
        }
        // Tujuan
        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $tujuan_id)
                                ->orderBy('tahun_perubahan', 'desc')
                                ->latest()->first();
        if($cek_perubahan_tujuan)
        {
            $tujuan = $cek_perubahan_tujuan->deskripsi;
            $tujuan_kode = $cek_perubahan_tujuan->kode;
            $misi_id = $cek_perubahan_tujuan->misi_id;
        } else {
            $get_tujuan = Tujuan::find($tujuan_id);
            $tujuan = $get_tujuan->deskripsi;
            $tujuan_kode = $get_tujuan->kode;
            $misi_id = $get_tujuan->misi_id;
        }

        // Misi
        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)
                                ->orderBy('tahun_perubahan', 'desc')
                                ->latest()->first();
        if($cek_perubahan_misi)
        {
            $misi = $cek_perubahan_misi->deskripsi;
            $misi_kode = $cek_perubahan_misi->kode;
            $visi_id = $cek_perubahan_misi->visi_id;
        } else {
            $get_misi = Misi::find($misi_id);
            $misi = $get_misi->deskripsi;
            $misi_kode = $get_misi->kode;
            $visi_id = $get_misi->visi_id;
        }

        // Visi
        $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $visi_id)
                                ->orderBy('tahun_perubahan', 'desc')
                                ->latest()->first();
        if($cek_perubahan_visi)
        {
            $visi = $cek_perubahan_visi->deskripsi;
        } else {
            $get_visi = Visi::find($visi_id);
            $visi = $get_visi->deskripsi;
        }

        // Program
        $get_program_rpjmd = ProgramRpjmd::find($id);
        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                    ->orderBy('tahun_perubahan')->latest()->first();
        if($cek_perubahan_program)
        {
            $program = $cek_perubahan_program->deskripsi;
            $program_kode = $cek_perubahan_program->kode;
        } else {
            $get_program = Program::find($get_program_rpjmd->program_id);
            $program = $get_program->deskripsi;
            $program_kode = $get_program->kode;
        }

        //Opd
        $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $id)->get();
        $target_rp_pertahun = '';
        foreach ($get_opds as $get_opd) {
            $target_rp_pertahun .= '<div class="data-table-rows slim">
                                    <h2 class="small-title">OPD: '.$get_opd->opd->nama.' </h2>
                                    <div class="data-table-responsive-wrapper">
                                        <table class="data-table w-100">
                                            <thead>
                                                <tr>
                                                    <th class="text-muted text-small text-uppercase" width="20%">Target</th>
                                                    <th class="text-muted text-small text-uppercase" width="30%">Satuan</th>
                                                    <th class="text-muted text-small text-uppercase" width="20%">RP</th>
                                                    <th class="text-muted text-small text-uppercase" width="15%">Tahun</th>
                                                    <th class="text-muted text-small text-uppercase" width="15%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                            foreach ($tahuns as $tahun) {
                                                $get_target_rp_pertahun = TargetRpPertahunProgram::where('tahun', $tahun)
                                                                            ->where('opd_id', $get_opd->opd_id)
                                                                            ->where('program_rpjmd_id', $id)
                                                                            ->first();
                                                                            if($get_target_rp_pertahun)
                                                                            {
                                                                                $target_rp_pertahun .= '<tr class="tr-target-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$id.'">';
                                                                                $target_rp_pertahun .= '<td><span class="span-target '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$id.'">'.$get_target_rp_pertahun->target.'</span></td>';
                                                                                $target_rp_pertahun .= '<td><span class="span-satuan '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$id.'">'. $get_target_rp_pertahun->satuan.'</span></td>';
                                                                                $target_rp_pertahun .= '<td><span class="span-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$id.'">'. $get_target_rp_pertahun->rp.'</span></td>';
                                                                                $target_rp_pertahun .= '<td>'.$tahun.'</td>';
                                                                                $target_rp_pertahun .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-target-rp-pertahun" type="button" data-opd-id="'.$get_opd->opd_id.'" data-tahun="'.$tahun.'" data-program-rpjmd-id="'.$id.'" data-target-rp-pertahun-program-id="'.$get_target_rp_pertahun->id.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                            } else {
                                                                                $target_rp_pertahun .= '<tr>';
                                                                                $target_rp_pertahun .= '<td><input type="number" class="form-control add-target '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$id.'"></td>';
                                                                                $target_rp_pertahun .= '<td><input type="text" class="form-control add-satuan '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$id.'"></td>';
                                                                                $target_rp_pertahun .= '<td><input type="text" class="form-control add-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$id.'"></td>';
                                                                                $target_rp_pertahun .= '<td>'.$tahun.'</td>';
                                                                                $target_rp_pertahun .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-target-rp-pertahun" type="button" data-opd-id="'.$get_opd->opd_id.'" data-tahun="'.$tahun.'" data-program-rpjmd-id="'.$id.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                            }
                                                $target_rp_pertahun .= '</tr>';
                                            }
                                            $target_rp_pertahun .= '</tbody>
                                        </table>
                                    </div>
                                </div> <hr>';
        }

        $array = [
            'visi' => $visi,
            'misi' => $misi,
            'misi_kode' => $misi_kode,
            'tujuan' => $tujuan,
            'tujuan_kode' => $tujuan_kode,
            'sasaran' => $sasaran,
            'sasaran_kode' => $sasaran_kode,
            'sasaran_indikator' => $sasaran_indikator,
            'program' => $program,
            'program_kode' => $program_kode,
            'target_rp_pertahun' => $target_rp_pertahun
        ];

        return response()->json(['result' => $array]);
    }

    public function store_target_rp_pertahun(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'opd_id' => 'required',
            'program_rpjmd_id' => 'required',
            'target' => 'required',
            'satuan' => 'required',
            'rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        if($request->target_rp_pertahun_program_id)
        {
            $target_rp_pertahun_program = TargetRpPertahunProgram::find($request->target_rp_pertahun_program_id);
        } else {
            $target_rp_pertahun_program = new TargetRpPertahunProgram;
        }
        $target_rp_pertahun_program->program_rpjmd_id = $request->program_rpjmd_id;
        $target_rp_pertahun_program->target = $request->target;
        $target_rp_pertahun_program->satuan = $request->satuan;
        $target_rp_pertahun_program->rp = $request->rp;
        $target_rp_pertahun_program->tahun = $request->tahun;
        $target_rp_pertahun_program->opd_id = $request->opd_id;
        $target_rp_pertahun_program->save();

        // Tahun
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        //Opd
        $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $request->program_rpjmd_id)->get();
        $target_rp_pertahun = '';
        foreach ($get_opds as $get_opd) {
            $target_rp_pertahun .= '<div class="data-table-rows slim">
                                    <h2 class="small-title">OPD: '.$get_opd->opd->nama.' </h2>
                                    <div class="data-table-responsive-wrapper">
                                        <table class="data-table w-100">
                                            <thead>
                                                <tr>
                                                    <th class="text-muted text-small text-uppercase" width="20%">Target</th>
                                                    <th class="text-muted text-small text-uppercase" width="30%">Satuan</th>
                                                    <th class="text-muted text-small text-uppercase" width="20%">RP</th>
                                                    <th class="text-muted text-small text-uppercase" width="15%">Tahun</th>
                                                    <th class="text-muted text-small text-uppercase" width="15%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                            foreach ($tahuns as $tahun) {
                                                $get_target_rp_pertahun = TargetRpPertahunProgram::where('tahun', $tahun)
                                                                            ->where('opd_id', $get_opd->opd_id)
                                                                            ->where('program_rpjmd_id',$request->program_rpjmd_id)
                                                                            ->first();
                                                                            if($get_target_rp_pertahun)
                                                                            {
                                                                                $target_rp_pertahun .= '<tr class="tr-target-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$request->program_rpjmd_id.'">';
                                                                                $target_rp_pertahun .= '<td><span class="span-target '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$request->program_rpjmd_id.'">'.$get_target_rp_pertahun->target.'</span></td>';
                                                                                $target_rp_pertahun .= '<td><span class="span-satuan '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$request->program_rpjmd_id.'">'. $get_target_rp_pertahun->satuan.'</span></td>';
                                                                                $target_rp_pertahun .= '<td><span class="span-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$request->program_rpjmd_id.'">'. $get_target_rp_pertahun->rp.'</span></td>';
                                                                                $target_rp_pertahun .= '<td>'.$tahun.'</td>';
                                                                                $target_rp_pertahun .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-target-rp-pertahun" type="button" data-opd-id="'.$get_opd->opd_id.'" data-tahun="'.$tahun.'" data-program-rpjmd-id="'.$request->program_rpjmd_id.'" data-target-rp-pertahun-program-id="'.$get_target_rp_pertahun->id.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                            } else {
                                                                                $target_rp_pertahun .= '<tr>';
                                                                                $target_rp_pertahun .= '<td><input type="number" class="form-control add-target '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$request->program_rpjmd_id.'"></td>';
                                                                                $target_rp_pertahun .= '<td><input type="text" class="form-control add-satuan '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$request->program_rpjmd_id.'"></td>';
                                                                                $target_rp_pertahun .= '<td><input type="text" class="form-control add-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-program-rpjmd-'.$request->program_rpjmd_id.'"></td>';
                                                                                $target_rp_pertahun .= '<td>'.$tahun.'</td>';
                                                                                $target_rp_pertahun .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-target-rp-pertahun" type="button" data-opd-id="'.$get_opd->opd_id.'" data-tahun="'.$tahun.'" data-program-rpjmd-id="'.$request->program_rpjmd_id.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                            }
                                                $target_rp_pertahun .= '</tr>';
                                            }
                                            $target_rp_pertahun .= '</tbody>
                                        </table>
                                    </div>
                                </div> <hr>';
        }

        return response()->json(['success' => $target_rp_pertahun]);
    }

    public function edit($id)
    {
        $data = ProgramRpjmd::find($id);

        return response()->json(['result' => $data]);
    }

    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'edit_program_pagu' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_rpjmd = ProgramRpjmd::find($request->edit_program_hidden_id);
        $program_rpjmd->pagu = $request->edit_program_pagu;
        $program_rpjmd->save();

        $get_visis = Visi::all();
        $visis = [];
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $tahun_sekarang)
                                    ->latest()->first();
            if($cek_perubahan_visi)
            {
                $visis[] = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'deskripsi' => $cek_perubahan_visi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_visi->tahun_perubahan
                ];
            } else {
                $visis[] = [
                    'id' => $get_visi->id,
                    'deskripsi' => $get_visi->deskripsi,
                    'tahun_perubahan' => $get_visi->tahun_perubahan
                ];
            }
        }

        $html = '<div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="onOffTaggingProgram" checked>
                            <label class="form-check-label" for="onOffTaggingProgram">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="95%">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase program-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class=" collapse" id="program_visi'.$visi['id'].'">
                                            <table class="table table-condensed table-striped">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                    $misis = [];
                                                    foreach ($get_misis as $get_misi) {
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->where('tahun_perubahan', $tahun_sekarang)
                                                                                ->latest()->first();
                                                        if($cek_perubahan_misi)
                                                        {
                                                            $misis[] = [
                                                                'id' => $cek_perubahan_misi->misi_id,
                                                                'kode' => $cek_perubahan_misi->kode,
                                                                'deskripsi' => $cek_perubahan_misi->deskripsi,
                                                                'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan,
                                                            ];
                                                        } else {
                                                            $misis[] = [
                                                                'id' => $get_misi->id,
                                                                'kode' => $get_misi->kode,
                                                                'deskripsi' => $get_misi->deskripsi,
                                                                'tahun_perubahan' => $get_misi->tahun_perubahan,
                                                            ];
                                                        }
                                                    }
                                                    $a = 1;
                                                    foreach ($misis as $misi) {
                                                        $html .= '<tr>
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle">
                                                                        '.$misi['deskripsi'].'
                                                                        <br>';
                                                                        if($a == 1 || $a == 2)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Aman]</span>';
                                                                        }
                                                                        if($a == 3)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Mandiri]</span>';
                                                                        }
                                                                        if($a == 4)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Sejahtera]</span>';
                                                                        }
                                                                        if($a == 5)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Berahlak]</span>';
                                                                        }
                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class=" collapse" id="program_misi'.$misi['id'].'">
                                                                            <table class="table table-condensed table-striped">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan', $tahun_sekarang)
                                                                                                                ->latest()
                                                                                                                ->first();
                                                                                        if($cek_perubahan_tujuan)
                                                                                        {
                                                                                            $tujuans[] = [
                                                                                                'id' => $cek_perubahan_tujuan->tujuan_id,
                                                                                                'kode' => $cek_perubahan_tujuan->kode,
                                                                                                'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                                                                                                'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan,
                                                                                            ];
                                                                                        } else {
                                                                                            $tujuans[] = [
                                                                                                'id' => $get_tujuan->id,
                                                                                                'kode' => $get_tujuan->kode,
                                                                                                'deskripsi' => $get_tujuan->deskripsi,
                                                                                                'tahun_perubahan' => $get_tujuan->tahun_perubahan,
                                                                                            ];
                                                                                        }
                                                                                    }
                                                                                    foreach ($tujuans as $tujuan) {
                                                                                        $html .= '<tr>
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                                                                                                        '.$tujuan['deskripsi'].'
                                                                                                        <br>';
                                                                                                        if($a == 1 || $a == 2)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Aman]</span>';
                                                                                                        }
                                                                                                        if($a == 3)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Mandiri]</span>';
                                                                                                        }
                                                                                                        if($a == 4)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Sejahtera]</span>';
                                                                                                        }
                                                                                                        if($a == 5)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Berahlak]</span>';
                                                                                                        }
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class=" collapse" id="program_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $tahun_sekarang)
                                                                                                                                                    ->latest()->first();
                                                                                                                        if($cek_perubahan_sasaran)
                                                                                                                        {
                                                                                                                            $sasarans[] = [
                                                                                                                                'id' => $cek_perubahan_sasaran->sasaran_id,
                                                                                                                                'kode' => $cek_perubahan_sasaran->kode,
                                                                                                                                'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                                                                                                                                'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan,
                                                                                                                            ];
                                                                                                                        } else {
                                                                                                                            $sasarans[] = [
                                                                                                                                'id' => $get_sasaran->id,
                                                                                                                                'kode' => $get_sasaran->kode,
                                                                                                                                'deskripsi' => $get_sasaran->deskripsi,
                                                                                                                                'tahun_perubahan' => $get_sasaran->tahun_perubahan,
                                                                                                                            ];
                                                                                                                        }
                                                                                                                    }
                                                                                                                    foreach ($sasarans as $sasaran) {
                                                                                                                        $html .= '<tr>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="95%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>';
                                                                                                                                        if($a == 1 || $a == 2)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Aman]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 3)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Mandiri]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 4)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 5)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Berahlak]</span>';
                                                                                                                                        }
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class=" collapse" id="program_sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="5%"><strong>No</strong></th>
                                                                                                                                                        <th width="45%"><strong>Sasaran Indikator</strong></th>
                                                                                                                                                        <th width="25%"><strong>Target</strong></th>
                                                                                                                                                        <th width="25%"><strong>Satuan</strong></th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                    $sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                    $b = 1;
                                                                                                                                                    foreach ($sasaran_indikators as $sasaran_indikator) {
                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_rpjmd'.$sasaran_indikator['id'].'" class="accordion-toggle">'.$b++.'</td>
                                                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_rpjmd'.$sasaran_indikator['id'].'" class="accordion-toggle">
                                                                                                                                                                        '.$sasaran_indikator['indikator'].'
                                                                                                                                                                        <br>';
                                                                                                                                                                        if($a == 1 || $a == 2)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Aman]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 3)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 4)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 5)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                                                    </td>
                                                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_rpjmd'.$sasaran_indikator['id'].'" class="accordion-toggle">
                                                                                                                                                                        '.$sasaran_indikator['target'].'
                                                                                                                                                                    </td>
                                                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_rpjmd'.$sasaran_indikator['id'].'" class="accordion-toggle">
                                                                                                                                                                        '.$sasaran_indikator['satuan'].'
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>
                                                                                                                                                                <tr>
                                                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                                                        <div class=" collapse" id="program_rpjmd'.$sasaran_indikator['id'].'">
                                                                                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th width="5%"><strong>No</strong></th>
                                                                                                                                                                                        <th width="35%"><strong>Program RPJMD</strong></th>
                                                                                                                                                                                        <th width="5%"><strong>Target</strong></th>
                                                                                                                                                                                        <th width="5%"><strong>Satuan</strong></th>
                                                                                                                                                                                        <th width="10%"><strong>Rp</strong></th>
                                                                                                                                                                                        <th width="20%"><strong>OPD</strong></th>
                                                                                                                                                                                        <th width="10%"><strong>Pagu</strong></th>
                                                                                                                                                                                        <th width="10%"><strong>Aksi</strong></th>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>';
                                                                                                                                                                                    $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran_indikator){
                                                                                                                                                                                        $q->where('sasaran_indikator_id', $sasaran_indikator['id']);
                                                                                                                                                                                    })->get();
                                                                                                                                                                                    $programs = [];
                                                                                                                                                                                    foreach ($get_program_rpjmds as $get_program_rpjmd) {
                                                                                                                                                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                                                                                                                                                                                                    ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                                                                                                        if($cek_perubahan_program)
                                                                                                                                                                                        {
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $cek_perubahan_program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu,
                                                                                                                                                                                            ];
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $program = Program::find($get_program_rpjmd->program_id);
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu,
                                                                                                                                                                                            ];
                                                                                                                                                                                        }
                                                                                                                                                                                    }
                                                                                                                                                                                    $c = 1;
                                                                                                                                                                                    foreach ($programs as $program) {
                                                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                                                <td>'.$c++.'</td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    '.$program['deskripsi'];
                                                                                                                                                                                                    if($program['status_program'] == "Program Prioritas")
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= ' <i title="Program Prioritas" class="fas fa-star text-primary"></i>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= ' <br> ';
                                                                                                                                                                                                    if($a == 1 || $a == 2)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Aman]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 3)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 4)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 5)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-danger text-uppercase program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                                                                                </td>';
                                                                                                                                                                                                $cek_target_rps = TargetRpPertahunProgram::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                                    ->where('tahun', $tahun_sekarang)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_target_rps)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $get_target_rps = TargetRpPertahunProgram::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                                    ->where('tahun', $tahun_sekarang)
                                                                                                                                                                                                                    ->get();
                                                                                                                                                                                                    $program_target = [];
                                                                                                                                                                                                    $program_rp = [];
                                                                                                                                                                                                    foreach ($get_target_rps as $get_target_rp) {
                                                                                                                                                                                                        $program_target[] = $get_target_rp->target;
                                                                                                                                                                                                        $program_rp[] = $get_target_rp->rp;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.array_sum($program_target).'</td>
                                                                                                                                                                                                <td>'.$get_target_rp->satuan.'</td>
                                                                                                                                                                                                <td>Rp. '.number_format(array_sum($program_rp), 2).'</td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td></td>
                                                                                                                                                                                                <td></td>
                                                                                                                                                                                                <td></td>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $html .= '<td>';
                                                                                                                                                                                                $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $program['id'])->get();
                                                                                                                                                                                                $html .= '<ul>';
                                                                                                                                                                                                    foreach ($get_opds as $get_opd) {
                                                                                                                                                                                                        $html .= '<li>'.$get_opd->opd->nama.'</li>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                $html.='</ul>';
                                                                                                                                                                                                $html.= '</td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    Rp. '.number_format($program['pagu'], 2).'
                                                                                                                                                                                                </td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-program-rpjmd" data-program-rpjmd-id="'.$program['id'].'" type="button" title="Detail Program"><i class="fas fa-eye"></i></button>
                                                                                                                                                                                                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-program-rpjmd" data-program-rpjmd-id="'.$program['id'].'" type="button" title="Edit Program"><i class="fas fa-edit"></i></button>
                                                                                                                                                                                                </td>
                                                                                                                                                                                            </tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                $html .= '</tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </div>
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>';
                                                                                                                                                    }
                                                                                                                                                $html .= '</tbody>
                                                                                                                                            </table>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                </tr>';
                                                                                                                    }
                                                                                                                $html .= '</tbody>
                                                                                                            </table>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>';
                                                                                    }
                                                                                $html .= '</tbody>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>';
                                                        $a++;
                                                    }
                                                $html .= '</tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>';
                            }
                            $html .='</tbody>
                        </table>
                    </div>
                </div>';
        return response()->json(['success' => $html]);
    }
}
