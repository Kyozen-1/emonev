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
use App\Models\ProgramRpjmd;
use App\Models\PivotPerubahanProgramRpjmd;
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\MasterOpd;
use App\Models\PivotSasaranIndikatorProgramRpjmd;
use App\Models\PivotOpdProgramRpjmd;

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
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->orderBy('tahun_perubahan', 'desc')
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
                    'id' => $get_visi->visi_id,
                    'deskripsi' => $get_visi->deskripsi,
                    'tahun_perubahan' => $get_visi->tahun_perubahan
                ];
            }
        }

        $html = '<div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="85%">Visi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="accordian-body collapse" id="program_visi'.$visi['id'].'">
                                            <table class="table table-striped">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                    $misis = [];
                                                    foreach ($get_misis as $get_misi) {
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->orderBy('tahun_perubahan', 'desc')
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
                                                    foreach ($misis as $misi) {
                                                        $html .= '<tr>
                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                                    <td width="70%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle">
                                                                        '.$misi['deskripsi'].'
                                                                        <br>
                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                    </td>
                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['tahun_perubahan'].'</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="accordian-body collapse" id="program_misi'.$misi['id'].'">
                                                                            <table class="table table-striped">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->orderBy('tahun_perubahan','desc')
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="15%">'.$tujuan['kode'].'</td>
                                                                                                    <td width="50%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                                                                                                        '.$tujuan['deskripsi'].'
                                                                                                        <br>
                                                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                        <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                                                    </td>
                                                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$tujuan['tahun_perubahan'].'</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="accordian-body collapse" id="program_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-striped">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->orderBy('tahun_perubahan', 'desc')
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
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="15%">'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="50%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>
                                                                                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase">'.$sasaran['kode'].' Sasaran</span>
                                                                                                                                    </td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="15%">'.$sasaran['tahun_perubahan'].'</td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="accordian-body collapse" id="program_sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-striped">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="50%"><strong>Sasaran Indikator</strong></th>
                                                                                                                                                        <th width="25%"><strong>Target</strong></th>
                                                                                                                                                        <th width="25%"><strong>Satuan</strong></th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                    $sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                    foreach ($sasaran_indikators as $sasaran_indikator) {
                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_rpjmd'.$sasaran_indikator['id'].'" class="accordion-toggle">
                                                                                                                                                                        '.$sasaran_indikator['indikator'].'
                                                                                                                                                                        <br>
                                                                                                                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase">'.$sasaran['kode'].' Sasaran</span>
                                                                                                                                                                        <span class="badge bg-info text-uppercase">Sasaran Indikator</span>
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
                                                                                                                                                                        <div class="accordian-body collapse" id="program_rpjmd'.$sasaran_indikator['id'].'">
                                                                                                                                                                            <table class="table table-striped">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th width="50%"><strong>Program RPJMD</strong></th>
                                                                                                                                                                                        <th width="15%"><strong>Status Program</strong></th>
                                                                                                                                                                                        <th width="15%"><strong>Pagu</strong></th>
                                                                                                                                                                                        <th width="20%"><strong>OPD</strong></th>
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
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu
                                                                                                                                                                                            ];
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $program = Program::find($get_program_rpjmd->program_id);
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu
                                                                                                                                                                                            ];
                                                                                                                                                                                        }
                                                                                                                                                                                    }
                                                                                                                                                                                    foreach ($programs as $program) {
                                                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    '.$program['deskripsi'].'
                                                                                                                                                                                                    <br>
                                                                                                                                                                                                    <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                                                                                                                    <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                                                                                                                    <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                                                                                                                                                    <span class="badge bg-danger text-uppercase">'.$sasaran['kode'].' Sasaran</span>
                                                                                                                                                                                                    <span class="badge bg-info text-uppercase">Sasaran Indikator</span>
                                                                                                                                                                                                    <span class="badge bg-success text-uppercase">Program RPJMD</span>
                                                                                                                                                                                                </td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    '.$program['status_program'].'
                                                                                                                                                                                                </td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    '.$program['pagu'].'
                                                                                                                                                                                                </td>
                                                                                                                                                                                                <td>';
                                                                                                                                                                                                $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $program['id'])->get();
                                                                                                                                                                                                $html .= '<ul>';
                                                                                                                                                                                                    foreach ($get_opds as $get_opd) {
                                                                                                                                                                                                        $html .= '<li>'.$get_opd->opd->nama.'</li>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                $html.='</ul>';
                                                                                                                                                                                                $html.= '</td>
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
