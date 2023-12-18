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
use App\Models\RenstraKegiatan;
use App\Models\PivotOpdRentraKegiatan;
use App\Models\Kegiatan;
use App\Models\PivotPerubahanKegiatan;
use App\Models\TargetRpPertahunRenstraKegiatan;
use App\Models\SasaranIndikatorKinerja;
use App\Models\TujuanPd;
use App\Models\PivotPerubahanTujuanPd;
use App\Models\TujuanPdIndikatorKinerja;
use App\Models\TujuanPdTargetSatuanRpRealisasi;
use App\Models\SasaranPd;
use App\Models\PivotPerubahanSasaranPd;
use App\Models\SasaranPdIndikatorKinerja;
use App\Models\SasaranPdTargetSatuanRpRealisasi;
use App\Models\ProgramIndikatorKinerja;
use App\Models\OpdProgramIndikatorKinerja;
use App\Models\ProgramTargetSatuanRpRealisasi;
use App\Models\KegiatanIndikatorKinerja;
use App\Models\KegiatanTargetSatuanRpRealisasi;
use App\Models\MasterTw;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\TujuanPdRealisasiRenja;
use App\Models\SasaranPdRealisasiRenja;
use App\Models\SasaranPdProgramRpjmd;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\SubKegiatanIndikatorKinerja;
use App\Models\OpdSubKegiatanIndikatorKinerja;
use App\Models\SubKegiatanTargetSatuanRpRealisasi;
use App\Models\SubKegiatanTwRealisasi;
use App\Models\TujuanIndikatorKinerja;
use App\Models\TujuanTargetSatuanRpRealisasi;
use App\Models\SasaranTargetSatuanRpRealisasi;

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
            'target_rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_target_satuan_rp_realisasi = new ProgramTargetSatuanRpRealisasi;
        $program_target_satuan_rp_realisasi->opd_program_indikator_kinerja_id = $request->opd_program_indikator_kinerja_id;
        $program_target_satuan_rp_realisasi->target = $request->target;
        $program_target_satuan_rp_realisasi->target_rp = $request->target_rp;
        $program_target_satuan_rp_realisasi->tahun = $request->tahun;
        $program_target_satuan_rp_realisasi->save();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        if($request->nav_nomenklatur_program_tahun == 'semua')
        {
            $get_urusans = new Urusan;
            if($request->program_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->program_filter_urusan);
            }
            $get_urusans = $get_urusans->orderBy('kode', 'asc')->get();
            $urusans = [];
            foreach ($get_urusans as $get_urusan) {
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $urusans[] = [
                        'id' => $cek_perubahan_urusan->urusan_id,
                        'kode' => $cek_perubahan_urusan->kode,
                        'deskripsi' => $cek_perubahan_urusan->deskripsi,
                        'tahun_perubahan' => $cek_perubahan_urusan->tahun_perubahan,
                    ];
                } else {
                    $urusans[] = [
                        'id' => $get_urusan->id,
                        'kode' => $get_urusan->kode,
                        'deskripsi' => $get_urusan->deskripsi,
                        'tahun_perubahan' => $get_urusan->tahun_perubahan,
                    ];
                }
            }

            $html = '<div class="data-table-rows slim">
                        <div class="data-table-responsive-wrapper">
                            <table class="table table-condensed table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="35%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                foreach ($urusans as $urusan) {
                                    $get_programs = Program::where('urusan_id', $urusan['id']);
                                    if($request->program_filter_program)
                                    {
                                        $get_programs = $get_programs->where('id', $request->program_filter_program);
                                    }
                                    $get_programs = $get_programs->get();
                                    $programs = [];
                                    foreach ($get_programs as $get_program) {
                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                        if($cek_perubahan_program)
                                        {
                                            $programs[] = [
                                                'id' => $cek_perubahan_program->program_id,
                                                'kode' => $cek_perubahan_program->kode,
                                                'deskripsi' => $cek_perubahan_program->deskripsi,
                                                'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan,
                                                'status_aturan' => $cek_perubahan_program->status_aturan,
                                            ];
                                        } else {
                                            $programs[] = [
                                                'id' => $get_program->id,
                                                'kode' => $get_program->kode,
                                                'deskripsi' => $get_program->deskripsi,
                                                'tahun_perubahan' => $get_program->tahun_perubahan,
                                                'status_aturan' => $get_program->status_aturan,
                                            ];
                                        }
                                    }
                                    $html .= '<tr style="background: #bbbbbb;">
                                                <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                    '.strtoupper($urusan['kode']).'
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                    '.strtoupper($urusan['deskripsi']).'
                                                    <br>
                                                    <span class="badge bg-primary text-uppercase program-tagging">'.$urusan['kode'].' Urusan</span>
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary waves-effect waves-light mr-2 program_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditProgramModal" title="Tambah Data Program" data-urusan-id="'.$urusan['id'].'"><i class="fas fa-plus"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="hiddenRow">
                                                    <div class="collapse show" id="program_urusan'.$urusan['id'].'">
                                                        <table class="table table-striped table-condesed">
                                                            <tbody>';
                                                            foreach ($programs as $program) {
                                                                $html .= '<tr>
                                                                            <td data-bs-toggle="collapse" data-bs-target="#program_program'.$program['id'].'" class="accordion-toggle" width="5%">'.$urusan['kode'].'.'.$program['kode'].'</td>
                                                                            <td data-bs-toggle="collapse" data-bs-target="#program_program'.$program['id'].'" class="accordion-toggle" width="40%">
                                                                                '.$program['deskripsi'];
                                                                                $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->where('status_program', 'Prioritas')->first();
                                                                                if($cek_program_rjmd)
                                                                                {
                                                                                    $html .= '<i class="fas fa-star text-primary" title="Program Prioritas">';
                                                                                }
                                                                                $html .= '<br>
                                                                                <span class="badge bg-primary text-uppercase program-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                <span class="badge bg-warning text-uppercase program-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                            </td>';
                                                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                                                            $html .= '<td width="35%"><table>
                                                                                <tbody>';
                                                                                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td width="75%">'.$program_indikator_kinerja->deskripsi.'<br>';
                                                                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                                                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                                                                    $html .= '<span class="badge bg-dark text-uppercase">'.$opd_program_indikator_kinerja->opd->nama.'</span>';
                                                                                                }
                                                                                            $html .='</td>';
                                                                                            $html .= '<td width="25%">
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-program-indikator-kinerja mr-1" data-id="'.$program_indikator_kinerja->id.'" title="Edit Indikator Kinerja Program"><i class="fas fa-edit"></i></button>
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-opd-program-indikator-kinerja mr-1" data-id="'.$program_indikator_kinerja->id.'" title="Edit OPD"><i class="fas fa-user"></i></button>
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-program-indikator-kinerja" type="button" title="Hapus Indikator" data-program-id="'.$program['id'].'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                                                            </td>';
                                                                                        $html .='</tr>';
                                                                                    }
                                                                                $html .= '</tbody>
                                                                            </table></td>';

                                                                            $html .='<td width="20%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-program" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Detail Program"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-danger waves-effect waves-light edit-program" data-program-id="'.$program['id'].'" data-urusan-id="'.$urusan['id'].'" data-tahun="semua" type="button" title="Edit Program"><i class="fas fa-edit"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-program-indikator-kinerja" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Tambah Indikator Kinerja Program"><i class="fas fa-lock"></i></button>
                                                                                <button class="btn btn-icon btn-danger waves-effect waves-light hapus-program" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Hapus Program"><i class="fas fa-trash"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="collapse accordion-body" id="program_program'.$program['id'].'">
                                                                                    <table class="table table-striped table-condesed">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>No</th>
                                                                                                <th>Indikator</th>
                                                                                                <th>Target Kinerja Awal</th>
                                                                                                <th>OPD</th>
                                                                                                <th>Target</th>
                                                                                                <th>Satuan</th>
                                                                                                <th>Target RP</th>
                                                                                                <th>Realisasi</th>
                                                                                                <th>Realisasi RP</th>
                                                                                                <th>Tahun</th>
                                                                                                <th>Aksi</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                                                                        $no_program_indikator_kinerja = 1;
                                                                                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                                                                            $html .= '<tr>';
                                                                                                $html .= '<td>'.$no_program_indikator_kinerja++.'</td>';
                                                                                                $html .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                                                                                $html .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                $a = 1;
                                                                                                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                                                                    ->get();
                                                                                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                                                                    if($a == 1)
                                                                                                    {
                                                                                                        $html .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                                                        $b = 1;
                                                                                                        foreach ($tahuns as $tahun) {
                                                                                                            if($b == 1)
                                                                                                            {
                                                                                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                                                                            ->where('tahun', $tahun)
                                                                                                                                                            ->first();
                                                                                                                if($cek_program_target_satuan_rp_realisasi)
                                                                                                                {
                                                                                                                    $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                    $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                    $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                </button>
                                                                                                                                </td>';
                                                                                                                    $html .='</tr>';
                                                                                                                } else {
                                                                                                                    $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                    $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                    $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                    </button>
                                                                                                                                </td>';
                                                                                                                    $html .='</>';
                                                                                                                }
                                                                                                            } else {
                                                                                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                                                                            ->where('tahun', $tahun)
                                                                                                                                                            ->first();
                                                                                                                if($cek_program_target_satuan_rp_realisasi)
                                                                                                                {
                                                                                                                    $html .= '<tr>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                    $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                    $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                </button>
                                                                                                                                </td>';
                                                                                                                    $html .='</tr>';
                                                                                                                } else {
                                                                                                                    $html .= '<tr>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                    $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                    $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                    </button>
                                                                                                                                </td>';
                                                                                                                    $html .='</tr>';
                                                                                                                }
                                                                                                            }
                                                                                                            $b++;
                                                                                                        }
                                                                                                    } else {
                                                                                                        $html .= '<tr>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                                                            $b = 1;
                                                                                                            foreach ($tahuns as $tahun) {
                                                                                                                if($b == 1)
                                                                                                                {
                                                                                                                    $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                                                                            ->where('tahun', $tahun)
                                                                                                                                                            ->first();
                                                                                                                    if($cek_program_target_satuan_rp_realisasi)
                                                                                                                    {
                                                                                                                        $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                        $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                    </button>
                                                                                                                                    </td>';
                                                                                                                        $html .='</tr>';
                                                                                                                    } else {
                                                                                                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                        </button>
                                                                                                                                    </td>';
                                                                                                                        $html .='</tr>';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                                                                            ->where('tahun', $tahun)
                                                                                                                                                            ->first();
                                                                                                                    if($cek_program_target_satuan_rp_realisasi)
                                                                                                                    {
                                                                                                                        $html .= '<tr>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                        $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                    </button>
                                                                                                                                    </td>';
                                                                                                                        $html .='</tr>';
                                                                                                                    } else {
                                                                                                                        $html .= '<tr>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                        </button>
                                                                                                                                    </td>';
                                                                                                                        $html .='</tr>';
                                                                                                                    }
                                                                                                                }
                                                                                                                $b++;
                                                                                                            }
                                                                                                    }
                                                                                                    $a++;
                                                                                                }
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
                    </div>';

            return response()->json(['success' => 'Berhasil menambahkan target','html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->program_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->program_filter_urusan);
            }
            $get_urusans = $get_urusans->orderBy('kode', 'asc')->get();
            $urusans = [];
            foreach ($get_urusans as $get_urusan) {
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $urusans[] = [
                        'id' => $cek_perubahan_urusan->urusan_id,
                        'kode' => $cek_perubahan_urusan->kode,
                        'deskripsi' => $cek_perubahan_urusan->deskripsi,
                        'tahun_perubahan' => $cek_perubahan_urusan->tahun_perubahan,
                    ];
                } else {
                    $urusans[] = [
                        'id' => $get_urusan->id,
                        'kode' => $get_urusan->kode,
                        'deskripsi' => $get_urusan->deskripsi,
                        'tahun_perubahan' => $get_urusan->tahun_perubahan,
                    ];
                }
            }

            $html = '<div class="data-table-rows slim">
                        <div class="data-table-responsive-wrapper">
                            <table class="table table-condensed table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="35%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                foreach ($urusans as $urusan) {
                                    $get_programs = Program::where('urusan_id', $urusan['id']);
                                    if($request->program_filter_program)
                                    {
                                        $get_programs = $get_programs->where('id', $request->program_filter_program);
                                    }
                                    $get_programs = $get_programs->where('tahun_perubahan', $request->nav_nomenklatur_program_tahun);
                                    $get_programs = $get_programs->get();
                                    $programs = [];
                                    foreach ($get_programs as $get_program) {
                                        $programs[] = [
                                            'id' => $get_program->id,
                                            'kode' => $get_program->kode,
                                            'deskripsi' => $get_program->deskripsi,
                                            'tahun_perubahan' => $get_program->tahun_perubahan,
                                            'status_aturan' => $get_program->status_aturan,
                                        ];
                                    }
                                    $html .= '<tr style="background: #bbbbbb;">
                                                <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                    '.strtoupper($urusan['kode']).'
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                    '.strtoupper($urusan['deskripsi']).'
                                                    <br>
                                                    <span class="badge bg-primary text-uppercase program-tagging">'.$urusan['kode'].' Urusan</span>
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary waves-effect waves-light mr-2 program_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditProgramModal" title="Tambah Data Program" data-urusan-id="'.$urusan['id'].'"><i class="fas fa-plus"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="hiddenRow">
                                                    <div class="collapse show" id="program_urusan'.$urusan['id'].'">
                                                        <table class="table table-striped table-condesed">
                                                            <tbody>';
                                                            foreach ($programs as $program) {
                                                                $html .= '<tr>
                                                                            <td data-bs-toggle="collapse" data-bs-target="#program_program'.$program['id'].'" class="accordion-toggle" width="5%">'.$urusan['kode'].'.'.$program['kode'].'</td>
                                                                            <td data-bs-toggle="collapse" data-bs-target="#program_program'.$program['id'].'" class="accordion-toggle" width="40%">
                                                                                '.$program['deskripsi'];
                                                                                $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->where('status_program', 'Prioritas')->first();
                                                                                if($cek_program_rjmd)
                                                                                {
                                                                                    $html .= '<i class="fas fa-star text-primary" title="Program Prioritas">';
                                                                                }
                                                                                $html .= '<br>
                                                                                <span class="badge bg-primary text-uppercase program-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                <span class="badge bg-warning text-uppercase program-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                            </td>';
                                                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                                                            $html .= '<td width="35%"><table>
                                                                                <tbody>';
                                                                                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td width="75%">'.$program_indikator_kinerja->deskripsi.'<br>';
                                                                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                                                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                                                                    $html .= '<span class="badge bg-dark text-uppercase">'.$opd_program_indikator_kinerja->opd->nama.'</span>';
                                                                                                }
                                                                                            $html .='</td>';
                                                                                            $html .= '<td width="25%">
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-program-indikator-kinerja mr-1" data-id="'.$program_indikator_kinerja->id.'" title="Edit Indikator Kinerja Program"><i class="fas fa-edit"></i></button>
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-opd-program-indikator-kinerja mr-1" data-id="'.$program_indikator_kinerja->id.'" title="Edit OPD"><i class="fas fa-user"></i></button>
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-program-indikator-kinerja" type="button" title="Hapus Indikator" data-program-id="'.$program['id'].'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                                                            </td>';
                                                                                        $html .='</tr>';
                                                                                    }
                                                                                $html .= '</tbody>
                                                                            </table></td>';

                                                                            $html .='<td width="20%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-program" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Detail Program"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-danger waves-effect waves-light edit-program" data-program-id="'.$program['id'].'" data-urusan-id="'.$urusan['id'].'" data-tahun="semua" type="button" title="Edit Program"><i class="fas fa-edit"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-program-indikator-kinerja" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Tambah Indikator Kinerja Program"><i class="fas fa-lock"></i></button>
                                                                                <button class="btn btn-icon btn-danger waves-effect waves-light hapus-program" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Hapus Program"><i class="fas fa-trash"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="collapse accordion-body" id="program_program'.$program['id'].'">
                                                                                    <table class="table table-striped table-condesed">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>No</th>
                                                                                                <th>Indikator</th>
                                                                                                <th>Target Kinerja Awal</th>
                                                                                                <th>OPD</th>
                                                                                                <th>Target</th>
                                                                                                <th>Satuan</th>
                                                                                                <th>Target RP</th>
                                                                                                <th>Realisasi</th>
                                                                                                <th>Realisasi RP</th>
                                                                                                <th>Tahun</th>
                                                                                                <th>Aksi</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                                                                        $no_program_indikator_kinerja = 1;
                                                                                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                                                                            $html .= '<tr>';
                                                                                                $html .= '<td>'.$no_program_indikator_kinerja++.'</td>';
                                                                                                $html .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                                                                                $html .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                $a = 1;
                                                                                                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                                                                    ->get();
                                                                                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                                                                    if($a == 1)
                                                                                                    {
                                                                                                        $html .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                                                        $b = 1;
                                                                                                        foreach ($tahuns as $tahun) {
                                                                                                            if($b == 1)
                                                                                                            {
                                                                                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                                                                            ->where('tahun', $tahun)
                                                                                                                                                            ->first();
                                                                                                                if($cek_program_target_satuan_rp_realisasi)
                                                                                                                {
                                                                                                                    $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                    $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                    $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                </button>
                                                                                                                                </td>';
                                                                                                                    $html .='</tr>';
                                                                                                                } else {
                                                                                                                    $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                    $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                    $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                    </button>
                                                                                                                                </td>';
                                                                                                                    $html .='</>';
                                                                                                                }
                                                                                                            } else {
                                                                                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                                                                            ->where('tahun', $tahun)
                                                                                                                                                            ->first();
                                                                                                                if($cek_program_target_satuan_rp_realisasi)
                                                                                                                {
                                                                                                                    $html .= '<tr>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                    $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                    $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                </button>
                                                                                                                                </td>';
                                                                                                                    $html .='</tr>';
                                                                                                                } else {
                                                                                                                    $html .= '<tr>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                    $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                    $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td></td>';
                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                    </button>
                                                                                                                                </td>';
                                                                                                                    $html .='</tr>';
                                                                                                                }
                                                                                                            }
                                                                                                            $b++;
                                                                                                        }
                                                                                                    } else {
                                                                                                        $html .= '<tr>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                                                            $b = 1;
                                                                                                            foreach ($tahuns as $tahun) {
                                                                                                                if($b == 1)
                                                                                                                {
                                                                                                                    $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                                                                            ->where('tahun', $tahun)
                                                                                                                                                            ->first();
                                                                                                                    if($cek_program_target_satuan_rp_realisasi)
                                                                                                                    {
                                                                                                                        $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                        $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                    </button>
                                                                                                                                    </td>';
                                                                                                                        $html .='</tr>';
                                                                                                                    } else {
                                                                                                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                        </button>
                                                                                                                                    </td>';
                                                                                                                        $html .='</tr>';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                                                                            ->where('tahun', $tahun)
                                                                                                                                                            ->first();
                                                                                                                    if($cek_program_target_satuan_rp_realisasi)
                                                                                                                    {
                                                                                                                        $html .= '<tr>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                        $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                    </button>
                                                                                                                                    </td>';
                                                                                                                        $html .='</tr>';
                                                                                                                    } else {
                                                                                                                        $html .= '<tr>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                        </button>
                                                                                                                                    </td>';
                                                                                                                        $html .='</tr>';
                                                                                                                    }
                                                                                                                }
                                                                                                                $b++;
                                                                                                            }
                                                                                                    }
                                                                                                    $a++;
                                                                                                }
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
                    </div>';

            return response()->json(['success' => 'Berhasil menambahkan target','html' => $html]);
        }
    }

    public function update_program_target_satuan_rp_realisasi(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'program_target_satuan_rp_realisasi' => 'required',
            'program_edit_target' => 'required',
            'program_edit_target_rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::find($request->program_target_satuan_rp_realisasi);
        $program_target_satuan_rp_realisasi->target = $request->program_edit_target;
        $program_target_satuan_rp_realisasi->target_rp = $request->program_edit_target_rp;
        $program_target_satuan_rp_realisasi->save();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        if($request->nav_nomenklatur_program_tahun == 'semua')
        {
            $getProgram = Program::find($request->program_program_id);
            $program = [
                'id' => $getProgram->id,
                'kode' => $getProgram->kode,
                'deskripsi' => $getProgram->deskripsi
            ];
            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
            $no_program_indikator_kinerja = 1;
            $html = '';
            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                    $html .= '<td>'.$no_program_indikator_kinerja++.'</td>';
                    $html .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                    $html .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                    $a = 1;
                    $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                        ->get();
                    foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                        if($a == 1)
                        {
                            $html .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                            $b = 1;
                            foreach ($tahuns as $tahun) {
                                if($b == 1)
                                {
                                    $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun)
                                                                                ->first();
                                    if($cek_program_target_satuan_rp_realisasi)
                                    {
                                        $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                        $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td>'.$tahun.'</td>';
                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                    </button>
                                                    </td>';
                                        $html .='</tr>';
                                    } else {
                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td>'.$tahun.'</td>';
                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                        </button>
                                                    </td>';
                                        $html .='</tr>';
                                    }
                                } else {
                                    $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun)
                                                                                ->first();
                                    if($cek_program_target_satuan_rp_realisasi)
                                    {
                                        $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                        $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td>'.$tahun.'</td>';
                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                    </button>
                                                    </td>';
                                        $html .='</tr>';
                                    } else {
                                        $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td>'.$tahun.'</td>';
                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                        </button>
                                                    </td>';
                                        $html .='</tr>';
                                    }
                                }
                                $b++;
                            }
                        } else {
                            $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                $b = 1;
                                foreach ($tahuns as $tahun) {
                                    if($b == 1)
                                    {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun)
                                                                                ->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                            $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                            $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td>'.$tahun.'</td>';
                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                        </button>
                                                        </td>';
                                            $html .='</tr>';
                                        } else {
                                            $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                            $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                            $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td>'.$tahun.'</td>';
                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                            </button>
                                                        </td>';
                                            $html .='</tr>';
                                        }
                                    } else {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun)
                                                                                ->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                            $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                            $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td>'.$tahun.'</td>';
                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                        </button>
                                                        </td>';
                                            $html .='</tr>';
                                        } else {
                                            $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                            $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                            $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td>'.$tahun.'</td>';
                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                            </button>
                                                        </td>';
                                            $html .='</tr>';
                                        }
                                    }
                                    $b++;
                                }
                        }
                        $a++;
                    }
            }

            return response()->json(['success' => 'Berhasil merubah target','html' => $html]);
        } else {
            $getProgram = Program::find($request->program_program_id);
            $program = [
                'id' => $getProgram->id,
                'kode' => $getProgram->kode,
                'deskripsi' => $getProgram->deskripsi
            ];
            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
            $no_program_indikator_kinerja = 1;
            $html = '';
            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                    $html .= '<td>'.$no_program_indikator_kinerja++.'</td>';
                    $html .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                    $html .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                    $a = 1;
                    $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                        ->get();
                    foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                        if($a == 1)
                        {
                            $html .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                            $b = 1;
                            foreach ($tahuns as $tahun) {
                                if($b == 1)
                                {
                                    $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun)
                                                                                ->first();
                                    if($cek_program_target_satuan_rp_realisasi)
                                    {
                                        $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                        $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td>'.$tahun.'</td>';
                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                    </button>
                                                    </td>';
                                        $html .='</tr>';
                                    } else {
                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td>'.$tahun.'</td>';
                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                        </button>
                                                    </td>';
                                        $html .='</tr>';
                                    }
                                } else {
                                    $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun)
                                                                                ->first();
                                    if($cek_program_target_satuan_rp_realisasi)
                                    {
                                        $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                        $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td>'.$tahun.'</td>';
                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                    </button>
                                                    </td>';
                                        $html .='</tr>';
                                    } else {
                                        $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                        $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                        $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td>'.$tahun.'</td>';
                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                        </button>
                                                    </td>';
                                        $html .='</tr>';
                                    }
                                }
                                $b++;
                            }
                        } else {
                            $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                $b = 1;
                                foreach ($tahuns as $tahun) {
                                    if($b == 1)
                                    {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun)
                                                                                ->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                            $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                            $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td>'.$tahun.'</td>';
                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                        </button>
                                                        </td>';
                                            $html .='</tr>';
                                        } else {
                                            $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                            $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                            $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td>'.$tahun.'</td>';
                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                            </button>
                                                        </td>';
                                            $html .='</tr>';
                                        }
                                    } else {
                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun)
                                                                                ->first();
                                        if($cek_program_target_satuan_rp_realisasi)
                                        {
                                            $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td> <span class="program-span-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'">'.$cek_program_target_satuan_rp_realisasi->target.'</span></td>';
                                            $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                            $html .= '<td> <span class="program-span-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'" data-target-rp="'.$cek_program_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td>'.$tahun.'</td>';
                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-target-satuan-rp-realisasi="'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                        </button>
                                                        </td>';
                                            $html .='</tr>';
                                        } else {
                                            $html .= '<tr class="trProgramIndikatorKinerjaTargetSatuanRp'.$program_indikator_kinerja['id'].'">';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td><input type="number" step="any" class="form-control program-add-target '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                            $html .= '<td> '.$program_indikator_kinerja->satuan.'</td>';
                                            $html .= '<td><input type="number" step="any" class="form-control program-add-target-rp '.$tahun.' data-opd-program-indikator-kinerja-'.$opd_program_indikator_kinerja->id.'"></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td>'.$tahun.'</td>';
                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-target-satuan-rp-realisasi" type="button" data-opd-program-indikator-kinerja-id="'.$opd_program_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-program-indikator-kinerja-id="'.$program_indikator_kinerja['id'].'" data-program-program-id="'.$program['id'].'">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                            </button>
                                                        </td>';
                                            $html .='</tr>';
                                        }
                                    }
                                    $b++;
                                }
                        }
                        $a++;
                    }
            }

            return response()->json(['success' => 'Berhasil merubah target','html' => $html]);
        }
    }
}
