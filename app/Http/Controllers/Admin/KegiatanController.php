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
use App\Models\OpdKegiatanIndikatorKinerja;
use App\Imports\KegiatanImport;

class KegiatanController extends Controller
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
            $data = Kegiatan::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="detail" id="'.$data->id.'" class="detail btn btn-icon waves-effect btn-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="edit" id="'.$data->id.'"
                    class="edit btn btn-icon waves-effect btn-warning" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    // $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    // $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    $button = $button_show . ' ' . $button_edit;
                    return $button;
                })
                ->addColumn('urusan', function($data){
                    if($data->program_id)
                    {
                        $cek_perubahan = PivotPerubahanUrusan::where('urusan_id',$data->program->urusan->id)->latest()->first();
                        if($cek_perubahan)
                        {
                            return $cek_perubahan->kode;
                        } else {
                            return $data->program->urusan->kode;
                        }
                    } else {
                        return 'Segera Edit Data!';
                    }
                })
                ->addColumn('program_id', function($data){
                    if($data->program_id)
                    {
                        $cek_perubahan = PivotPerubahanProgram::where('program_id',$data->program_id)->latest()->first();
                        if($cek_perubahan)
                        {
                            return $cek_perubahan->kode;
                        } else {
                            return $data->program->kode;
                        }
                    } else {
                        return 'Segera Edit Data!';
                    }
                })
                ->editColumn('kode', function($data){
                    $cek_perubahan = PivotPerubahanKegiatan::where('kegiatan_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->kode;
                    } else {
                        return $data->kode;
                    }
                })
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan = PivotPerubahanKegiatan::where('kegiatan_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->deskripsi;
                    } else {
                        return $data->deskripsi;
                    }
                })
                ->addColumn('indikator', function($data){
                    $jumlah = PivotKegiatanIndikator::whereHas('kegiatan', function($q) use ($data){
                        $q->where('kegiatan_id', $data->id);
                    })->count();

                    return '<a href="'.url('/admin/kegiatan/'.$data->id.'/indikator').'" >'.$jumlah.'</a>';;
                })
                ->rawColumns(['aksi', 'indikator'])
                ->make(true);
        }
        $urusan = Urusan::pluck('deskripsi', 'id');
        return view('admin.kegiatan.index', [
            'urusan' => $urusan
        ]);
    }

    public function get_program(Request $request)
    {
        $program = Program::where('urusan_id', $request->id)->pluck('deskripsi', 'id');
        return response()->json($program);
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
            'kegiatan_program_id' => 'required',
            'kegiatan_kode' => 'required',
            'kegiatan_deskripsi' => 'required',
            'kegiatan_tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        $cek_kegiatan = Kegiatan::where('kode', $request->kegiatan_kode)->where('program_id', $request->kegiatan_program_id)->first();
        if($cek_kegiatan)
        {
            $pivot = new PivotPerubahanKegiatan;
            $pivot->kegiatan_id = $cek_kegiatan->id;
            $pivot->program_id = $request->kegiatan_program_id;
            $pivot->kode = $request->kegiatan_kode;
            $pivot->deskripsi = $request->kegiatan_deskripsi;
            $pivot->tahun_perubahan = $request->kegiatan_tahun_perubahan;
            if($request->kegiatan_tahun_perubahan > 2020)
            {
                $pivot->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot->status_aturan = 'Sebelum Perubahan';
            }
            $pivot->kabupaten_id = 62;
            $pivot->save();
        } else {
            $kegiatan = new Kegiatan;
            $kegiatan->program_id = $request->kegiatan_program_id;
            $kegiatan->kode = $request->kegiatan_kode;
            $kegiatan->deskripsi = $request->kegiatan_deskripsi;
            $kegiatan->tahun_perubahan = $request->kegiatan_tahun_perubahan;
            if($request->kegiatan_tahun_perubahan > 2020)
            {
                $kegiatan->status_aturan = 'Sesudah Perubahan';
            } else {
                $kegiatan->status_aturan = 'Sebelum Perubahan';
            }
            $kegiatan->kabupaten_id = 62;
            $kegiatan->save();
        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        if($request->nav_nomenklatur_kegiatan_tahun == 'semua')
        {
            $get_urusans = new Urusan;
            if($request->kegiatan_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->kegiatan_filter_urusan);
            }
            $get_urusans = $get_urusans->orderBy('kode','asc')->get();
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id']);
                                        if($request->kegiatan_filter_program)
                                        {
                                            $get_programs = $get_programs->where('id', $request->kegiatan_filter_program);
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">'.strtoupper($urusan['kode']).'.'.strtoupper($program['kode']).'</td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .='<br>
                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%">
                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>';
                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id']);
                                                                                                    if($request->kegiatan_filter_kegiatan)
                                                                                                    {
                                                                                                        $get_kegiatans = $get_kegiatans->where('id', $request->kegiatan_filter_kegiatan);
                                                                                                    }
                                                                                                    $get_kegiatans = $get_kegiatans->get();

                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                                                                                                                    ->orderBy('tahun_perubahan', 'desc')->first();
                                                                                                        if($cek_perubahan_kegiatan)
                                                                                                        {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                'program_id' => $cek_perubahan_kegiatan->program_id,
                                                                                                                'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $cek_perubahan_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        } else {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $get_kegiatan->id,
                                                                                                                'program_id' => $get_kegiatan->program_id,
                                                                                                                'kode' => $get_kegiatan->kode,
                                                                                                                'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $get_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        }
                                                                                                    }
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                        <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>';
                                                                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                    $html .= '<td width="25%"><ul data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >';
                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                        $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                $opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->first();
                                                                                                                                if($opd_kegiatan_indikator_kinerja)
                                                                                                                                {
                                                                                                                                    $html .= '<span class="badge bg-muted text-uppercase kegiatan-tagging">'.$opd_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                }
                                                                                                                        $html .= '</li>';
                                                                                                                    }
                                                                                                                    $html .='</ul></td>
                                                                                                                    <td width="20%">
                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Hapus Kegiatan"><i class="fas fa-trash"></i></button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                        <div class="collapse accordion-body" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th>No</th>
                                                                                                                                        <th>Indikator</th>
                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                        <th>Target Anggaran Awal</th>
                                                                                                                                        <th>OPD</th>
                                                                                                                                        <th>Target</th>
                                                                                                                                        <th>Satuan</th>
                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                        <th>Realisasi</th>
                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                        <th>Tahun</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>';
                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                    $html .= '<tr>';
                                                                                                                                        $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                        $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                        $a = 1;
                                                                                                                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                                                                            ->get();
                                                                                                                                        foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                                                                                                            if($a == 1)
                                                                                                                                            {
                                                                                                                                                $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                $b = 1;
                                                                                                                                                foreach ($tahuns as $tahun) {
                                                                                                                                                    if($b == 1)
                                                                                                                                                    {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</>';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    $b++;
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                    $b = 1;
                                                                                                                                                    foreach ($tahuns as $tahun) {
                                                                                                                                                        if($b == 1)
                                                                                                                                                        {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
                                                                $html .='</tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>';
                                    }
                                $html .= '</tbody>
                            </table>
                        </div>
                    </div>';

            return response()->json(['success' => 'Berhasil menambahkan kegiatan','html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->kegiatan_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->kegiatan_filter_urusan);
            }
            $get_urusans = $get_urusans->orderBy('kode','asc')->get();
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Tahun Perubahan</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id']);
                                        if($request->kegiatan_filter_program)
                                        {
                                            $get_programs = $get_programs->where('id', $request->kegiatan_filter_program);
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">'.strtoupper($urusan['kode']).'.'.strtoupper($program['kode']).'</td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .='<br>
                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%">
                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>';
                                                                                                $get_kegiatans = Kegiatan::where('program_id', $program['id']);
                                                                                                if($request->kegiatan_filter_kegiatan)
                                                                                                {
                                                                                                    $get_kegiatans = $get_kegiatans->where('id', $request->kegiatan_filter_kegiatan);
                                                                                                }
                                                                                                $get_kegiatans = $get_kegiatans->get();
                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $kegiatans[] = [
                                                                                                            'id' => $get_kegiatan->id,
                                                                                                            'program_id' => $get_kegiatan->program_id,
                                                                                                            'kode' => $get_kegiatan->kode,
                                                                                                            'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                            'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                            'status_aturan' => $get_kegiatan->status_aturan
                                                                                                        ];
                                                                                                    }
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                        <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>';
                                                                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                    $html .= '<td width="25%"><ul data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >';
                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                        $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                $opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->first();
                                                                                                                                if($opd_kegiatan_indikator_kinerja)
                                                                                                                                {
                                                                                                                                    $html .= '<span class="badge bg-muted text-uppercase kegiatan-tagging">'.$opd_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                }
                                                                                                                        $html .= '</li>';
                                                                                                                    }
                                                                                                                    $html .='</ul></td>
                                                                                                                    <td width="20%">
                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Hapus Kegiatan"><i class="fas fa-trash"></i></button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                        <div class="collapse accordion-body" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th>No</th>
                                                                                                                                        <th>Indikator</th>
                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                        <th>Target Anggaran Awal</th>
                                                                                                                                        <th>OPD</th>
                                                                                                                                        <th>Target</th>
                                                                                                                                        <th>Satuan</th>
                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                        <th>Realisasi</th>
                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                        <th>Tahun</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>';
                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                    $html .= '<tr>';
                                                                                                                                        $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                        $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                        $a = 1;
                                                                                                                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                                                                            ->get();
                                                                                                                                        foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                                                                                                            if($a == 1)
                                                                                                                                            {
                                                                                                                                                $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                $b = 1;
                                                                                                                                                foreach ($tahuns as $tahun) {
                                                                                                                                                    if($b == 1)
                                                                                                                                                    {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</>';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    $b++;
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                    $b = 1;
                                                                                                                                                    foreach ($tahuns as $tahun) {
                                                                                                                                                        if($b == 1)
                                                                                                                                                        {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
                                                                $html .='</tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>';
                                    }
                                $html .= '</tbody>
                            </table>
                        </div>
                    </div>';

            return response()->json(['success' => 'Berhasil menambahkan kegiatan','html' => $html]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $tahun)
    {
        if($tahun == 'semua')
        {
            $data = Kegiatan::find($id);
            $deskripsi_program = '';
            $deskripsi_urusan = '';

            $cek_perubahan = PivotPerubahanKegiatan::where('kegiatan_id', $id)->latest()->first();
            $html = '<div>';

            if($cek_perubahan)
            {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $cek_perubahan->program_id)->latest()->first();
                if($cek_perubahan_program)
                {
                    $kode_program = $cek_perubahan_program->kode;
                    $urusan_id = $cek_perubahan_program->urusan_id;
                } else {
                    $kode_program = $data->program->kode;
                    $urusan_id = $data->program->urusan_id;
                }
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $kode_urusan = $cek_perubahan_urusan->kode;
                } else {
                    $get_urusan = Urusan::find($urusan_id);
                    $kode_urusan = $get_urusan->kode;
                }
                $html .= '<ul>';
                $html .= '<li><p>
                                Kode Urusan: '.$kode_urusan.' <br>
                                Kode Program: '.$kode_program.' <br>
                                Kode: '.$data->kode.'<br>
                                Deskripsi: '.$data->deskripsi.'<br>
                                Tahun: '.$data->tahun_perubahan.'<br>
                            </p></li>';
                $a = 1;

                $get_perubahans = PivotPerubahanKegiatan::where('kegiatan_id', $id)->orderBy('tahun_perubahan', 'asc')->get();
                foreach ($get_perubahans as $get_perubahan) {
                    $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_perubahan->program_id)->latest()->first();
                    if($cek_perubahan_program)
                    {
                        $kode_program = $cek_perubahan_program->kode;
                        $urusan_id = $cek_perubahan_program->urusan_id;
                    } else {
                        $kode_program = $data->program->kode;
                        $urusan_id = $data->program->urusan_id;
                    }
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        $kode_urusan = $cek_perubahan_urusan->kode;
                    } else {
                        $get_urusan = Urusan::find($urusan_id);
                        $kode_urusan = $get_urusan->kode;
                    }
                    $html .= '<li><p>
                                Kode Urusan: '.$kode_urusan.' <br>
                                Kode Program: '.$kode_program.' <br>
                                Kode: '.$get_perubahan->kode.'<br>
                                Deskripsi: '.$get_perubahan->deskripsi.'<br>
                                Tahun: '.$get_perubahan->tahun_perubahan.'<br>
                            </p></li>';
                }
                $html .= '</ul>';

                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $cek_perubahan->program_id)
                                            ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                if($cek_perubahan_program)
                {
                    $deskripsi_program = $cek_perubahan_program->deskripsi;
                    $urusan_id = $cek_perubahan_program->urusan_id;
                } else {
                    $program = Program::find($cek_perubahan->program_id);
                    $deskripsi_program = $program->deskripsi;
                    $urusan_id = $program->urusan_id;
                }

                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)
                                        ->orderBy('created_at', 'desc')->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $deskripsi_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $urusan = Urusan::find($urusan_id);
                    $deskripsi_urusan = $urusan->deskripsi;
                }

                $kegiatan_kode = $cek_perubahan->kode;
                $kegiatan_deskripsi = $cek_perubahan->deskripsi;
                $kegiatan_tahun_perubahan = $cek_perubahan->tahun_perubahan;
            } else {
                $html .= '<p>Tidak ada</p>';
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $data->program_id)->latest()->first();
                if($cek_perubahan_program)
                {
                    $urusan_id = $cek_perubahan_program->urusan_id;
                    $deskripsi_program = $cek_perubahan_program->deskripsi;
                } else {
                    $urusan_id = $data->program->urusan_id;
                    $deskripsi_program = $data->program->deskripsi;
                }
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $deskripsi_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $get_urusan = Urusan::find($urusan_id);
                    $deskripsi_urusan = $get_urusan->deskripsi;
                }
                $kegiatan_kode = $data->kode;
                $kegiatan_deskripsi = $data->deskripsi;
                $kegiatan_tahun_perubahan = $data->tahun_perubahan;
            }

            $html .='</div>';

        } else {
            $data = Kegiatan::find($id);
            $deskripsi_program = '';
            $deskripsi_urusan = '';

            $cek_perubahan = PivotPerubahanKegiatan::where('kegiatan_id', $id)->latest()->first();
            $html = '<div>';

            if($cek_perubahan)
            {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $cek_perubahan->program_id)->latest()->first();
                if($cek_perubahan_program)
                {
                    $kode_program = $cek_perubahan_program->kode;
                    $urusan_id = $cek_perubahan_program->urusan_id;
                } else {
                    $kode_program = $data->program->kode;
                    $urusan_id = $data->program->urusan_id;
                }
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $kode_urusan = $cek_perubahan_urusan->kode;
                } else {
                    $get_urusan = Urusan::find($urusan_id);
                    $kode_urusan = $get_urusan->kode;
                }
                $html .= '<ul>';
                $html .= '<li><p>
                                Kode Urusan: '.$kode_urusan.' <br>
                                Kode Program: '.$kode_program.' <br>
                                Kode: '.$data->kode.'<br>
                                Deskripsi: '.$data->deskripsi.'<br>
                                Tahun: '.$data->tahun_perubahan.'<br>
                            </p></li>';
                $a = 1;

                $get_perubahans = PivotPerubahanKegiatan::where('kegiatan_id', $id)->orderBy('tahun_perubahan', 'asc')->get();
                foreach ($get_perubahans as $get_perubahan) {
                    $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_perubahan->program_id)->latest()->first();
                    if($cek_perubahan_program)
                    {
                        $kode_program = $cek_perubahan_program->kode;
                        $urusan_id = $cek_perubahan_program->urusan_id;
                    } else {
                        $kode_program = $data->program->kode;
                        $urusan_id = $data->program->urusan_id;
                    }
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        $kode_urusan = $cek_perubahan_urusan->kode;
                    } else {
                        $get_urusan = Urusan::find($urusan_id);
                        $kode_urusan = $get_urusan->kode;
                    }
                    $html .= '<li><p>
                                Kode Urusan: '.$kode_urusan.' <br>
                                Kode Program: '.$kode_program.' <br>
                                Kode: '.$get_perubahan->kode.'<br>
                                Deskripsi: '.$get_perubahan->deskripsi.'<br>
                                Tahun: '.$get_perubahan->tahun_perubahan.'<br>
                            </p></li>';
                }
                $html .= '</ul>';

                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $cek_perubahan->program_id)
                                            ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                if($cek_perubahan_program)
                {
                    $deskripsi_program = $cek_perubahan_program->deskripsi;
                    $urusan_id = $cek_perubahan_program->urusan_id;
                } else {
                    $program = Program::find($cek_perubahan->program_id);
                    $deskripsi_program = $program->deskripsi;
                    $urusan_id = $program->urusan_id;
                }

                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)
                                        ->orderBy('created_at', 'desc')->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $deskripsi_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $urusan = Urusan::find($urusan_id);
                    $deskripsi_urusan = $urusan->deskripsi;
                }

                $kegiatan_kode = $data->kode;
                $kegiatan_deskripsi = $data->deskripsi;
                $kegiatan_tahun_perubahan = $data->tahun_perubahan;
            } else {
                $html .= '<p>Tidak ada</p>';
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $data->program_id)->latest()->first();
                if($cek_perubahan_program)
                {
                    $urusan_id = $cek_perubahan_program->urusan_id;
                    $deskripsi_program = $cek_perubahan_program->deskripsi;
                } else {
                    $urusan_id = $data->program->urusan_id;
                    $deskripsi_program = $data->program->deskripsi;
                }
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $deskripsi_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $get_urusan = Urusan::find($urusan_id);
                    $deskripsi_urusan = $get_urusan->deskripsi;
                }
                $kegiatan_kode = $data->kode;
                $kegiatan_deskripsi = $data->deskripsi;
                $kegiatan_tahun_perubahan = $data->tahun_perubahan;
            }

            $html .='</div>';
        }

        $array = [
            'urusan' => $deskripsi_urusan,
            'program' => $deskripsi_program,
            'kode' => $kegiatan_kode,
            'deskripsi' => $kegiatan_deskripsi,
            'tahun_perubahan' => $kegiatan_tahun_perubahan,
            'pivot_perubahan_kegiatan' => $html
        ];

        return response()->json(['result' => $array]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $tahun)
    {
        if($tahun == 'semua')
        {
            $data = Kegiatan::find($id);

            $cek_perubahan = PivotPerubahanKegiatan::where('kegiatan_id', $id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
            if($cek_perubahan)
            {
                $array = [
                    'kode' => $cek_perubahan->kode,
                    'deskripsi' => $cek_perubahan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan->tahun_perubahan
                ];
            } else {
                $array = [
                    'kode' => $data->kode,
                    'deskripsi' => $data->deskripsi,
                    'tahun_perubahan' => $data->tahun_perubahan
                ];
            }
        } else {
            $data = Kegiatan::find($id);

            $array = [
                'kode' => $data->kode,
                'deskripsi' => $data->deskripsi,
                'tahun_perubahan' => $data->tahun_perubahan
            ];
        }

        return response()->json(['result' => $array]);
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
            'kegiatan_kode' => 'required',
            'kegiatan_deskripsi' => 'required',
            'kegiatan_program_id' => 'required',
            'kegiatan_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $cek_pivot = PivotPerubahanKegiatan::where('kegiatan_id', $request->kegiatan_hidden_id)
                        ->where('program_id', $request->kegiatan_program_id)
                        ->where('kode', $request->kegiatan_kode)
                        ->where('tahun_perubahan', $request->kegiatan_tahun_perubahan)
                        ->first();
        if($cek_pivot)
        {
            PivotPerubahanKegiatan::find($cek_pivot->id)->delete();

            $pivot_perubahan_kegiatan = new PivotPerubahanKegiatan;
            $pivot_perubahan_kegiatan->kegiatan_id = $request->kegiatan_hidden_id;
            $pivot_perubahan_kegiatan->program_id = $request->kegiatan_program_id;
            $pivot_perubahan_kegiatan->kode = $request->kegiatan_kode;
            $pivot_perubahan_kegiatan->deskripsi = $request->kegiatan_deskripsi;
            $pivot_perubahan_kegiatan->tahun_perubahan = $request->kegiatan_tahun_perubahan;
            if($request->kegiatan_tahun_perubahan > 2020)
            {
                $pivot_perubahan_kegiatan->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot_perubahan_kegiatan->status_aturan = 'Sebelum Perubahan';
            }
            $pivot_perubahan_kegiatan->kabupaten_id = 62;
            $pivot_perubahan_kegiatan->save();
        } else {
            $pivot_perubahan_kegiatan = new PivotPerubahanKegiatan;
            $pivot_perubahan_kegiatan->kegiatan_id = $request->kegiatan_hidden_id;
            $pivot_perubahan_kegiatan->program_id = $request->kegiatan_program_id;
            $pivot_perubahan_kegiatan->kode = $request->kegiatan_kode;
            $pivot_perubahan_kegiatan->deskripsi = $request->kegiatan_deskripsi;
            $pivot_perubahan_kegiatan->tahun_perubahan = $request->kegiatan_tahun_perubahan;
            if($request->kegiatan_tahun_perubahan > 2020)
            {
                $pivot_perubahan_kegiatan->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot_perubahan_kegiatan->status_aturan = 'Sebelum Perubahan';
            }
            $pivot_perubahan_kegiatan->kabupaten_id = 62;
            $pivot_perubahan_kegiatan->save();

        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        if($request->nav_nomenklatur_kegiatan_tahun == 'semua')
        {
            $get_urusans = new Urusan;
            if($request->kegiatan_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->kegiatan_filter_urusan);
            }
            $get_urusans = $get_urusans->orderBy('kode','asc')->get();
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id']);
                                        if($request->kegiatan_filter_program)
                                        {
                                            $get_programs = $get_programs->where('id', $request->kegiatan_filter_program);
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">'.strtoupper($urusan['kode']).'.'.strtoupper($program['kode']).'</td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .='<br>
                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%">
                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>';
                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id']);
                                                                                                    if($request->kegiatan_filter_kegiatan)
                                                                                                    {
                                                                                                        $get_kegiatans = $get_kegiatans->where('id', $request->kegiatan_filter_kegiatan);
                                                                                                    }
                                                                                                    $get_kegiatans = $get_kegiatans->get();

                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                                                                                                                    ->orderBy('tahun_perubahan', 'desc')->first();
                                                                                                        if($cek_perubahan_kegiatan)
                                                                                                        {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                'program_id' => $cek_perubahan_kegiatan->program_id,
                                                                                                                'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $cek_perubahan_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        } else {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $get_kegiatan->id,
                                                                                                                'program_id' => $get_kegiatan->program_id,
                                                                                                                'kode' => $get_kegiatan->kode,
                                                                                                                'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $get_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        }
                                                                                                    }
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                        <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>';
                                                                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                    $html .= '<td width="25%"><ul data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >';
                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                        $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                $opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->first();
                                                                                                                                if($opd_kegiatan_indikator_kinerja)
                                                                                                                                {
                                                                                                                                    $html .= '<span class="badge bg-muted text-uppercase kegiatan-tagging">'.$opd_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                }
                                                                                                                        $html .= '</li>';
                                                                                                                    }
                                                                                                                    $html .='</ul></td>
                                                                                                                    <td width="20%">
                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Hapus Kegiatan"><i class="fas fa-trash"></i></button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                        <div class="collapse accordion-body" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th>No</th>
                                                                                                                                        <th>Indikator</th>
                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                        <th>Target Anggaran Awal</th>
                                                                                                                                        <th>OPD</th>
                                                                                                                                        <th>Target</th>
                                                                                                                                        <th>Satuan</th>
                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                        <th>Realisasi</th>
                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                        <th>Tahun</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>';
                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                    $html .= '<tr>';
                                                                                                                                        $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                        $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                        $a = 1;
                                                                                                                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                                                                            ->get();
                                                                                                                                        foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                                                                                                            if($a == 1)
                                                                                                                                            {
                                                                                                                                                $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                $b = 1;
                                                                                                                                                foreach ($tahuns as $tahun) {
                                                                                                                                                    if($b == 1)
                                                                                                                                                    {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</>';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    $b++;
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                    $b = 1;
                                                                                                                                                    foreach ($tahuns as $tahun) {
                                                                                                                                                        if($b == 1)
                                                                                                                                                        {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
                                                                $html .='</tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>';
                                    }
                                $html .= '</tbody>
                            </table>
                        </div>
                    </div>';

            return response()->json(['success' => 'Berhasil menghapus kegiatan','html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->kegiatan_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->kegiatan_filter_urusan);
            }
            $get_urusans = $get_urusans->orderBy('kode','asc')->get();
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Tahun Perubahan</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id']);
                                        if($request->kegiatan_filter_program)
                                        {
                                            $get_programs = $get_programs->where('id', $request->kegiatan_filter_program);
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">'.strtoupper($urusan['kode']).'.'.strtoupper($program['kode']).'</td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .='<br>
                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%">
                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>';
                                                                                                $get_kegiatans = Kegiatan::where('program_id', $program['id']);
                                                                                                if($request->kegiatan_filter_kegiatan)
                                                                                                {
                                                                                                    $get_kegiatans = $get_kegiatans->where('id', $request->kegiatan_filter_kegiatan);
                                                                                                }
                                                                                                $get_kegiatans = $get_kegiatans->get();
                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $kegiatans[] = [
                                                                                                            'id' => $get_kegiatan->id,
                                                                                                            'program_id' => $get_kegiatan->program_id,
                                                                                                            'kode' => $get_kegiatan->kode,
                                                                                                            'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                            'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                            'status_aturan' => $get_kegiatan->status_aturan
                                                                                                        ];
                                                                                                    }
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                        <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>';
                                                                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                    $html .= '<td width="25%"><ul data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >';
                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                        $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                $opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->first();
                                                                                                                                if($opd_kegiatan_indikator_kinerja)
                                                                                                                                {
                                                                                                                                    $html .= '<span class="badge bg-muted text-uppercase kegiatan-tagging">'.$opd_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                }
                                                                                                                        $html .= '</li>';
                                                                                                                    }
                                                                                                                    $html .='</ul></td>
                                                                                                                    <td width="20%">
                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Hapus Kegiatan"><i class="fas fa-trash"></i></button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                        <div class="collapse accordion-body" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th>No</th>
                                                                                                                                        <th>Indikator</th>
                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                        <th>Target Anggaran Awal</th>
                                                                                                                                        <th>OPD</th>
                                                                                                                                        <th>Target</th>
                                                                                                                                        <th>Satuan</th>
                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                        <th>Realisasi</th>
                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                        <th>Tahun</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>';
                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                    $html .= '<tr>';
                                                                                                                                        $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                        $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                        $a = 1;
                                                                                                                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                                                                            ->get();
                                                                                                                                        foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                                                                                                            if($a == 1)
                                                                                                                                            {
                                                                                                                                                $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                $b = 1;
                                                                                                                                                foreach ($tahuns as $tahun) {
                                                                                                                                                    if($b == 1)
                                                                                                                                                    {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</>';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    $b++;
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                    $b = 1;
                                                                                                                                                    foreach ($tahuns as $tahun) {
                                                                                                                                                        if($b == 1)
                                                                                                                                                        {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
                                                                $html .='</tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>';
                                    }
                                $html .= '</tbody>
                            </table>
                        </div>
                    </div>';

            return response()->json(['success' => 'Berhasil menghapus kegiatan','html' => $html]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function impor(Request $request)
    {
        $file = $request->file('impor_kegiatan');
        Excel::import(new KegiatanImport, $file->store('temp'));
        $msg = [session('import_status'), session('import_message')];
        if ($msg[0]) {
            Alert::success('Berhasil', $msg[1]);
            return back();
        } else {
            Alert::error('Gagal', $msg[1]);
            return back();
        }
    }

    public function indikator_kinerja_tambah(Request $request)
    {
        $deskripsis = json_decode($request->indikator_kinerja_kegiatan_deskripsi, true);
        foreach ($deskripsis as $deskripsi) {
            $indikator_kinerja = new KegiatanIndikatorKinerja;
            $indikator_kinerja->kegiatan_id = $request->indikator_kinerja_kegiatan_kegiatan_id;
            $indikator_kinerja->deskripsi = $deskripsi['value'];
            $indikator_kinerja->save();
        }

        Alert::success('Berhasil', 'Berhasil Menambahkan Indikator Kinerja untuk Kegiatan');
        return redirect()->route('admin.nomenklatur.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        KegiatanIndikatorKinerja::find($request->kegiatan_indikator_kinerja_id)->delete();

        return response()->json(['success' => 'Berhasil Menghapus Indikator Kinerja untuk Kegiatan']);
    }

    public function hapus(Request $request)
    {
        if($request->hapus_kegiatan_tahun == 'semua')
        {
            $get_perubahan_kegiatans = PivotPerubahanKegiatan::where('kegiatan_id', $request->hapus_kegiatan_id)->get();
            foreach ($get_perubahan_kegiatans as $get_perubahan_kegiatan) {
                PivotPerubahanKegiatan::find($get_perubahan_kegiatan->id)->delete();
            }

            $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $request->hapus_kegiatan_id)->get();
            foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                    $kegiatan_target_satuan_rp_realisasies = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->get();
                    foreach ($kegiatan_target_satuan_rp_realisasies as $kegiatan_target_satuan_rp_realisasi) {
                        $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->get();
                        foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                            KegiatanTwRealisasi::find($kegiatan_tw_realisasi->id)->delete();
                        }

                        KegiatanTargetSatuanRpRealisasi::find($kegiatan_target_satuan_rp_realisasi->id)->delete();
                    }

                    OpdKegiatanIndikatorKinerja::find($opd_kegiatan_indikator_kinerja->id)->delete();
                }

                KegiatanIndikatorKinerja::find($kegiatan_indikator_kinerja->id)->delete();
            }

            $sub_kegiatans = SubKegiatan::where('kegiatan_id', $request->hapus_kegiatan_id)->get();
            foreach ($sub_kegiatans as $sub_kegiatan) {
                $get_perubahan_sub_kegiatans = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $sub_kegiatan_id)->get();
                foreach ($get_perubahan_sub_kegiatans as $get_perubahan_sub_kegiatan) {
                    PivotPerubahanSubKegiatan::find($get_perubahan_sub_kegiatan->id)->delete();
                }

                $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan_id)->get();
                foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                    $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)->get();
                    foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja) {
                        $sub_kegiatan_target_satuan_rp_realisasies = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)->get();
                        foreach ($sub_kegiatan_target_satuan_rp_realisasies as $sub_kegiatan_target_satuan_rp_realisasi) {
                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                SubKegiatanTwRealisasi::find($sub_kegiatan_tw_realisasi->id)->delete();
                            }

                            SubKegiatanTargetSatuanRpRealisasi::find($kegiatan_target_satuan_rp_realisasi->id)->delete();
                        }

                        SubOpdKegiatanIndikatorKinerja::find($opd_kegiatan_indikator_kinerja->id)->delete();
                    }

                    SubKegiatanIndikatorKinerja::find($kegiatan_indikator_kinerja->id)->delete();
                }

                SubKegiatan::find($sub_kegiatan->id)->delete();
            }

            Kegiatan::find($request->hapus_kegiatan_id)->delete();
        } else {
            $cek_perubahan_kegiatan_1 = PivotPerubahanKegiatan::where('kegiatan_id', $request->hapus_kegiatan_id)->where('tahun_perubahan', $request->hapus_kegiatan_tahun)->first();
            if($cek_perubahan_kegiatan_1)
            {
                PivotPerubahanKegiatan::find($cek_perubahan_kegiatan_1->id)->delete();
            } else {
                // Logika jika malah tahun ada di kegiatan bukan di pivot perubahan kegiatan
                $cek_kegiatan = Kegiatan::where('tahun_perubahan', $request->hapus_kegiatan_tahun)->where('id', $request->hapus_kegiatan_id)->first();
                if($cek_kegiatan)
                {
                    $pivot_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $request->hapus_kegiatan_id)->first();
                    if($pivot_perubahan_kegiatan)
                    {
                        $edit_kegiatan = Kegiatan::find($cek_kegiatan->id);
                        $edit_kegiatan->tahun_perubahan = $pivot_perubahan_kegiatan->tahun_perubahan;
                        $edit_kegiatan->save();

                        PivotPerubahanKegiatan::find($pivot_perubahan_kegiatan->id)->delete();
                    } else {
                        return response()->json(['errors' => 'Pilih Pilihan Hapus Semua!']);
                    }
                }

                // Pengecekan jika menjadi satu - satunya
                $cek_perubahan_kegiatan_2 = PivotPerubahanKegiatan::where('kegiatan_id', $request->hapus_kegiatan_id)->first();
                if(!$cek_perubahan_kegiatan_2)
                {
                    $get_perubahan_kegiatans = PivotPerubahanKegiatan::where('kegiatan_id', $request->hapus_kegiatan_id)->get();
                    foreach ($get_perubahan_kegiatans as $get_perubahan_kegiatan) {
                        PivotPerubahanKegiatan::find($get_perubahan_kegiatan->id)->delete();
                    }

                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $request->hapus_kegiatan_id)->get();
                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                        foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                            $kegiatan_target_satuan_rp_realisasies = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->get();
                            foreach ($kegiatan_target_satuan_rp_realisasies as $kegiatan_target_satuan_rp_realisasi) {
                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $kegiatan_target_satuan_rp_realisasi->id)->get();
                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                    KegiatanTwRealisasi::find($kegiatan_tw_realisasi->id)->delete();
                                }

                                KegiatanTargetSatuanRpRealisasi::find($kegiatan_target_satuan_rp_realisasi->id)->delete();
                            }

                            OpdKegiatanIndikatorKinerja::find($opd_kegiatan_indikator_kinerja->id)->delete();
                        }

                        KegiatanIndikatorKinerja::find($kegiatan_indikator_kinerja->id)->delete();
                    }

                    $sub_kegiatans = SubKegiatan::where('kegiatan_id', $request->hapus_kegiatan_id)->get();
                    foreach ($sub_kegiatans as $sub_kegiatan) {
                        $get_perubahan_sub_kegiatans = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $sub_kegiatan_id)->get();
                        foreach ($get_perubahan_sub_kegiatans as $get_perubahan_sub_kegiatan) {
                            PivotPerubahanSubKegiatan::find($get_perubahan_sub_kegiatan->id)->delete();
                        }

                        $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan_id)->get();
                        foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                            $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)->get();
                            foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja) {
                                $sub_kegiatan_target_satuan_rp_realisasies = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)->get();
                                foreach ($sub_kegiatan_target_satuan_rp_realisasies as $sub_kegiatan_target_satuan_rp_realisasi) {
                                    $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                    foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                        SubKegiatanTwRealisasi::find($sub_kegiatan_tw_realisasi->id)->delete();
                                    }

                                    SubKegiatanTargetSatuanRpRealisasi::find($kegiatan_target_satuan_rp_realisasi->id)->delete();
                                }

                                SubOpdKegiatanIndikatorKinerja::find($opd_kegiatan_indikator_kinerja->id)->delete();
                            }

                            SubKegiatanIndikatorKinerja::find($kegiatan_indikator_kinerja->id)->delete();
                        }

                        SubKegiatan::find($sub_kegiatan->id)->delete();
                    }

                    Kegiatan::find($request->hapus_kegiatan_id)->delete();
                }
            }
        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        if($request->nav_nomenklatur_kegiatan_tahun == 'semua')
        {
            $get_urusans = new Urusan;
            if($request->kegiatan_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->kegiatan_filter_urusan);
            }
            $get_urusans = $get_urusans->orderBy('kode','asc')->get();
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id']);
                                        if($request->kegiatan_filter_program)
                                        {
                                            $get_programs = $get_programs->where('id', $request->kegiatan_filter_program);
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">'.strtoupper($urusan['kode']).'.'.strtoupper($program['kode']).'</td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .='<br>
                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%">
                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>';
                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id']);
                                                                                                    if($request->kegiatan_filter_kegiatan)
                                                                                                    {
                                                                                                        $get_kegiatans = $get_kegiatans->where('id', $request->kegiatan_filter_kegiatan);
                                                                                                    }
                                                                                                    $get_kegiatans = $get_kegiatans->get();

                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                                                                                                                    ->orderBy('tahun_perubahan', 'desc')->first();
                                                                                                        if($cek_perubahan_kegiatan)
                                                                                                        {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                'program_id' => $cek_perubahan_kegiatan->program_id,
                                                                                                                'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $cek_perubahan_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        } else {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $get_kegiatan->id,
                                                                                                                'program_id' => $get_kegiatan->program_id,
                                                                                                                'kode' => $get_kegiatan->kode,
                                                                                                                'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $get_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        }
                                                                                                    }
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                        <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>';
                                                                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                    $html .= '<td width="25%"><ul data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >';
                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                        $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                $opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->first();
                                                                                                                                if($opd_kegiatan_indikator_kinerja)
                                                                                                                                {
                                                                                                                                    $html .= '<span class="badge bg-muted text-uppercase kegiatan-tagging">'.$opd_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                }
                                                                                                                        $html .= '</li>';
                                                                                                                    }
                                                                                                                    $html .='</ul></td>
                                                                                                                    <td width="20%">
                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Hapus Kegiatan"><i class="fas fa-trash"></i></button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                        <div class="collapse accordion-body" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th>No</th>
                                                                                                                                        <th>Indikator</th>
                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                        <th>Target Anggaran Awal</th>
                                                                                                                                        <th>OPD</th>
                                                                                                                                        <th>Target</th>
                                                                                                                                        <th>Satuan</th>
                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                        <th>Realisasi</th>
                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                        <th>Tahun</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>';
                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                    $html .= '<tr>';
                                                                                                                                        $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                        $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                        $a = 1;
                                                                                                                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                                                                            ->get();
                                                                                                                                        foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                                                                                                            if($a == 1)
                                                                                                                                            {
                                                                                                                                                $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                $b = 1;
                                                                                                                                                foreach ($tahuns as $tahun) {
                                                                                                                                                    if($b == 1)
                                                                                                                                                    {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</>';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    $b++;
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                    $b = 1;
                                                                                                                                                    foreach ($tahuns as $tahun) {
                                                                                                                                                        if($b == 1)
                                                                                                                                                        {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
                                                                $html .='</tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>';
                                    }
                                $html .= '</tbody>
                            </table>
                        </div>
                    </div>';

            return response()->json(['success' => 'Berhasil menghapus kegiatan','html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->kegiatan_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->kegiatan_filter_urusan);
            }
            $get_urusans = $get_urusans->orderBy('kode','asc')->get();
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Tahun Perubahan</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id']);
                                        if($request->kegiatan_filter_program)
                                        {
                                            $get_programs = $get_programs->where('id', $request->kegiatan_filter_program);
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">'.strtoupper($urusan['kode']).'.'.strtoupper($program['kode']).'</td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .='<br>
                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%">
                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>';
                                                                                                $get_kegiatans = Kegiatan::where('program_id', $program['id']);
                                                                                                if($request->kegiatan_filter_kegiatan)
                                                                                                {
                                                                                                    $get_kegiatans = $get_kegiatans->where('id', $request->kegiatan_filter_kegiatan);
                                                                                                }
                                                                                                $get_kegiatans = $get_kegiatans->get();
                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $kegiatans[] = [
                                                                                                            'id' => $get_kegiatan->id,
                                                                                                            'program_id' => $get_kegiatan->program_id,
                                                                                                            'kode' => $get_kegiatan->kode,
                                                                                                            'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                            'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                            'status_aturan' => $get_kegiatan->status_aturan
                                                                                                        ];
                                                                                                    }
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                        <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>';
                                                                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                    $html .= '<td width="25%"><ul data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >';
                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                        $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                $opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->first();
                                                                                                                                if($opd_kegiatan_indikator_kinerja)
                                                                                                                                {
                                                                                                                                    $html .= '<span class="badge bg-muted text-uppercase kegiatan-tagging">'.$opd_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                }
                                                                                                                        $html .= '</li>';
                                                                                                                    }
                                                                                                                    $html .='</ul></td>
                                                                                                                    <td width="20%">
                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Hapus Kegiatan"><i class="fas fa-trash"></i></button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                        <div class="collapse accordion-body" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th>No</th>
                                                                                                                                        <th>Indikator</th>
                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                        <th>Target Anggaran Awal</th>
                                                                                                                                        <th>OPD</th>
                                                                                                                                        <th>Target</th>
                                                                                                                                        <th>Satuan</th>
                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                        <th>Realisasi</th>
                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                        <th>Tahun</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>';
                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                    $html .= '<tr>';
                                                                                                                                        $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                        $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                        $a = 1;
                                                                                                                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                                                                            ->get();
                                                                                                                                        foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                                                                                                                            if($a == 1)
                                                                                                                                            {
                                                                                                                                                $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                $b = 1;
                                                                                                                                                foreach ($tahuns as $tahun) {
                                                                                                                                                    if($b == 1)
                                                                                                                                                    {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</>';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    $b++;
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                    $b = 1;
                                                                                                                                                    foreach ($tahuns as $tahun) {
                                                                                                                                                        if($b == 1)
                                                                                                                                                        {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
                                                                $html .='</tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>';
                                    }
                                $html .= '</tbody>
                            </table>
                        </div>
                    </div>';

            return response()->json(['success' => 'Berhasil menghapus kegiatan','html' => $html]);
        }
    }
}
