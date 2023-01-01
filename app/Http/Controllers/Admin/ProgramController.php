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

class ProgramController extends Controller
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
            $data = Program::latest()->get();
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
                ->editColumn('urusan_id', function($data){
                    if($data->urusan_id)
                    {
                        return $data->urusan->kode;
                    } else {
                        return 'Segera Edit Data!';
                    }
                })
                ->editColumn('kode', function($data){
                    $cek_perubahan = PivotPerubahanProgram::where('program_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->kode;
                    } else {
                        return $data->kode;
                    }
                })
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan = PivotPerubahanProgram::where('program_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->deskripsi;
                    } else {
                        return $data->deskripsi;
                    }
                })
                ->addColumn('indikator', function($data){
                    $jumlah = PivotProgramIndikator::whereHas('program', function($q) use ($data){
                        $q->where('program_id', $data->id);
                    })->count();

                    return '<a href="'.url('/admin/program/'.$data->id.'/indikator').'" >'.$jumlah.'</a>';;
                })
                ->editColumn('pagu', function($data){
                    $cek_perubahan = PivotPerubahanProgram::where('program_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return 'Rp. '.number_format($cek_perubahan->pagu, 2);
                    } else {
                        return 'Rp. '.number_format($data->pagu, 2);
                    }
                })
                ->rawColumns(['aksi', 'indikator'])
                ->make(true);
        }
        $urusan = Urusan::pluck('deskripsi', 'id');
        return view('admin.program.index', [
            'urusan' => $urusan
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
            'program_urusan_id' => 'required',
            'program_kode' => 'required',
            'program_deskripsi' => 'required',
            'program_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $cek_perubahan_urusan = PivotPerubahanUrusan::where('id', $request->program_urusan_id)->latest()->first();
        if($cek_perubahan_urusan)
        {
            $urusan_id = $cek_perubahan_urusan->urusan_id;
        } else {
            $urusan_id = $request->program_urusan_id;
        }

        $cek_program = Program::where('kode', $request->kode)->first();
        if($cek_program)
        {
            $pivot = new PivotPerubahanProgram;
            $pivot->program_id = $cek_program->id;
            $pivot->urusan_id = $urusan_id;
            $pivot->kode = $request->program_kode;
            $pivot->deskripsi = $request->program_deskripsi;
            $pivot->tahun_perubahan = $request->program_tahun_perubahan;
            if($request->status_perubahan > 2020)
            {
                $pivot->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot->status_aturan = 'Sebelum Perubahan';
            }
            $pivot->kabupaten_id = 62;
            $pivot->save();
        } else {
            $program = new Program;
            $program->urusan_id = $urusan_id;
            $program->kode = $request->program_kode;
            $program->deskripsi = $request->program_deskripsi;
            $program->tahun_perubahan = $request->program_tahun_perubahan;
            if($request->status_perubahan > 2020)
            {
                $program->status_aturan = 'Sesudah Perubahan';
            } else {
                $program->status_aturan = 'Sebelum Perubahan';
            }
            $program->kabupaten_id = 62;
            $program->save();
        }

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

            return response()->json(['success' => 'Berhasil menambahkan program','html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->program_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->program_filter_urusan);
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

            return response()->json(['success' => 'Berhasil menambahkan program','html' => $html]);
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
            $data = Program::find($id);

            $cek_perubahan = PivotPerubahanProgram::where('program_id', $id)->orderBy('tahun_perubahan', 'desc')->first();
            $html = '<div>';

            if($cek_perubahan)
            {
                $get_perubahans = PivotPerubahanProgram::where('program_id', $id)->orderBy('tahun_perubahan', 'asc')->get();
                $html .= '<ul>';
                $html .= '<li><p>
                                Kode Urusan: '.$data->urusan->kode.' <br>
                                Kode: '.$data->kode.'<br>
                                Deskripsi: '.$data->deskripsi.'<br>
                                Tahun: '.$data->tahun_perubahan.'<br>
                            </p></li>';
                $a = 1;
                foreach ($get_perubahans as $get_perubahan) {
                    $html .= '<li><p>
                                Kode Urusan: '.$get_perubahan->program->urusan->kode.' <br>
                                Kode: '.$get_perubahan->kode.'<br>
                                Deskripsi: '.$get_perubahan->deskripsi.'<br>
                                Tahun: '.$get_perubahan->tahun_perubahan.'<br>
                            </p></li>';
                }
                $html .= '</ul>';
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $cek_perubahan->urusan_id)
                                            ->orderBy('tahun_perubahan', 'desc')
                                            ->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $program_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $urusan = Urusan::find($cek_perubahan->urusan_id);
                    $program_urusan = $urusan->deskripsi;
                }
                $program_deskripsi = $cek_perubahan->deskripsi;
                $program_kode = $cek_perubahan->kode;
                $program_tahun_perubahan = $cek_perubahan->tahun_perubahan;
            } else {
                $html .= '<p>Tidak ada</p>';
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $data->urusan_id)
                                            ->orderBy('tahun_perubahan', 'desc')
                                            ->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $program_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $urusan = Urusan::find($data->urusan_id);
                    $program_urusan = $urusan->deskripsi;
                }
                $program_deskripsi = $data->deskripsi;
                $program_kode = $data->kode;
                $program_tahun_perubahan = $data->tahun_perubahan;
            }
        } else {
            $data = Program::find($id);

            $cek_perubahan = PivotPerubahanProgram::where('program_id', $id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
            $html = '<div>';

            if($cek_perubahan)
            {
                $get_perubahans = PivotPerubahanProgram::where('program_id', $id)->orderBy('tahun_perubahan', 'desc')->get();
                $html .= '<ul>';
                $html .= '<li><p>
                                Kode Urusan: '.$data->urusan->kode.' <br>
                                Kode: '.$data->kode.'<br>
                                Deskripsi: '.$data->deskripsi.'<br>
                                Tahun Perubahan: '.$data->tahun_perubahan.'<br>
                            </p></li>';
                $a = 1;
                foreach ($get_perubahans as $get_perubahan) {
                    $html .= '<li><p>
                                Kode Urusan: '.$get_perubahan->program->urusan->kode.' <br>
                                Kode: '.$get_perubahan->kode.'<br>
                                Deskripsi: '.$get_perubahan->deskripsi.'<br>
                                Tahun Perubahan: '.$get_perubahan->tahun_perubahan.'<br>
                            </p></li>';
                }
                $html .= '</ul>';
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $cek_perubahan->urusan_id)
                                            ->orderBy('tahun_perubahan', 'desc')
                                            ->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $program_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $urusan = Urusan::find($cek_perubahan->urusan_id);
                    $program_urusan = $urusan->deskripsi;
                }
                $program_deskripsi = $data->deskripsi;
                $program_kode = $data->kode;
                $program_tahun_perubahan = $data->tahun_perubahan;
            } else {
                $html .= '<p>Tidak ada</p>';
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $data->urusan_id)
                                            ->orderBy('tahun_perubahan', 'desc')
                                            ->latest()->first();
                if($cek_perubahan_urusan)
                {
                    $program_urusan = $cek_perubahan_urusan->deskripsi;
                } else {
                    $urusan = Urusan::find($data->urusan_id);
                    $program_urusan = $urusan->deskripsi;
                }
                $program_deskripsi = $data->deskripsi;
                $program_kode = $data->kode;
                $program_tahun_perubahan = $data->tahun_perubahan;
            }
        }

        $array = [
            'urusan' => $program_urusan,
            'kode' => $program_kode,
            'deskripsi' => $program_deskripsi,
            'tahun_perubahan' => $program_tahun_perubahan,
            'pivot_perubahan_program' => $html
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
            $data = Program::find($id);
            $cek_perubahan = PivotPerubahanProgram::where('program_id', $id)->latest()->first();
            if($cek_perubahan)
            {
                $array = [
                    'id' => $cek_perubahan->id,
                    'urusan_id' => $cek_perubahan->urusan_id,
                    'kode' => $cek_perubahan->kode,
                    'deskripsi' => $cek_perubahan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan->tahun_perubahan
                ];
            } else {
                $array = [
                    'id' => $data->id,
                    'urusan_id' => $data->urusan_id,
                    'kode' => $data->kode,
                    'deskripsi' => $data->deskripsi,
                    'tahun_perubahan' => $data->tahun_perubahan
                ];
            }
        } else {
            $data = Program::find($id);
            $array = [
                'id' => $data->id,
                'urusan_id' => $data->urusan_id,
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
            'program_kode' => 'required',
            'program_deskripsi' => 'required',
            'program_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $cek_pivot = PivotPerubahanProgram::where('program_id', $request->program_hidden_id)
                        ->where('urusan_id', $request->program_urusan_id)
                        ->where('kode', $request->program_kode)
                        ->where('tahun_perubahan', $request->program_tahun_perubahan)
                        ->first();
        if($cek_pivot)
        {
            PivotPerubahanProgram::find($cek_pivot->id)->delete();

            $pivot = new PivotPerubahanProgram;
            $pivot->program_id = $request->program_hidden_id;
            $pivot->urusan_id = $request->program_urusan_id;
            $pivot->kode = $request->program_kode;
            $pivot->deskripsi = $request->program_deskripsi;
            $pivot->tahun_perubahan = $request->program_tahun_perubahan;
            $pivot->kabupaten_id = 62;
            if($request->status_perubahan > 2020)
            {
                $pivot->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot->status_aturan = 'Sebelum Perubahan';
            }
            $pivot->save();
        } else {
            $pivot = new PivotPerubahanProgram;
            $pivot->program_id = $request->program_hidden_id;
            $pivot->urusan_id = $request->program_urusan_id;
            $pivot->kode = $request->program_kode;
            $pivot->deskripsi = $request->program_deskripsi;
            $pivot->tahun_perubahan = $request->program_tahun_perubahan;
            $pivot->kabupaten_id = 62;
            if($request->status_perubahan > 2020)
            {
                $pivot->status_aturan = 'Sesudah Perubahan';
            } else {
                $pivot->status_aturan = 'Sebelum Perubahan';
            }
            $pivot->save();
        }

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

            return response()->json(['success' => 'Berhasil merubah program','html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->program_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->program_filter_urusan);
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

            return response()->json(['success' => 'Berhasil merubah program','html' => $html]);
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
        $urusan_id = $request->program_impor_urusan_id;
        $file = $request->file('impor_program');
        Excel::import(new ProgramImport($urusan_id), $file->store('temp'));
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
        $indikator_kinerja = new ProgramIndikatorKinerja;
        $indikator_kinerja->program_id = $request->indikator_kinerja_program_program_id;
        $indikator_kinerja->deskripsi = $request->indikator_kinerja_program_deskripsi;
        $indikator_kinerja->satuan = $request->indikator_kinerja_program_satuan;
        $indikator_kinerja->kondisi_target_kinerja_awal = $request->indikator_kinerja_program_kondisi_target_kinerja_awal;
        $indikator_kinerja->kondisi_target_anggaran_awal = $request->indikator_kinerja_program_kondisi_target_anggaran_awal;
        $indikator_kinerja->save();

        $opd_id = $request->indikator_kinerja_program_opd_id;

        for ($i=0; $i < count($opd_id); $i++) {
            $opd_program_indikator_kinerja = new OpdProgramIndikatorKinerja;
            $opd_program_indikator_kinerja->program_indikator_kinerja_id = $indikator_kinerja->id;
            $opd_program_indikator_kinerja->opd_id = $opd_id[$i];
            $opd_program_indikator_kinerja->save();
        }

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

            return response()->json(['success' => 'Berhasil menambah indikator program','html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->program_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->program_filter_urusan);
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

            return response()->json(['success' => 'Berhasil menambah indikator program','html' => $html]);
        }
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $program_indikator = ProgramIndikatorKinerja::find($request->program_indikator_kinerja_id);

        $get_opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator->id)->get();

        foreach ($get_opd_program_indikator_kinerjas as $get_opd_program_indikator_kinerja) {
            $program_target_satuan_rp_realisasis = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $get_opd_program_indikator_kinerja->id)->get();
            foreach ($program_target_satuan_rp_realisasis as $program_target_satuan_rp_realisasi) {
                ProgramTargetSatuanRpRealisasi::find($program_target_satuan_rp_realisasi->id)->delete();
            }
            OpdProgramIndikatorKinerja::find($get_opd_program_indikator_kinerja->id)->delete();
        }

        $program_indikator = $program_indikator->delete();

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

            return response()->json(['success' => 'Berhasil menghapus indikator program','html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->program_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->program_filter_urusan);
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

            return response()->json(['success' => 'Berhasil menghapus indikator program','html' => $html]);
        }
    }

    public function indikator_kinerja_edit($id)
    {
        $data = ProgramIndikatorKinerja::find($id);

        return response()->json(['result' => $data]);
    }

    public function indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'edit_program_indikator_kinerja_id' => 'required',
            'edit_program_indikator_kinerja_deskripsi' => 'required',
            'edit_program_indikator_kinerja_satuan' => 'required',
            'edit_program_indikator_kinerja_kondisi_target_kinerja_awal' => 'required',
            'edit_program_indikator_kinerja_kondisi_target_anggaran_awal' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $program_indikator_kinerja = ProgramIndikatorKinerja::find($request->edit_program_indikator_kinerja_id);
        $program_indikator_kinerja->deskripsi = $request->edit_program_indikator_kinerja_deskripsi;
        $program_indikator_kinerja->satuan = $request->edit_program_indikator_kinerja_satuan;
        $program_indikator_kinerja->kondisi_target_kinerja_awal = $request->edit_program_indikator_kinerja_kondisi_target_kinerja_awal;
        $program_indikator_kinerja->kondisi_target_anggaran_awal = $request->edit_program_indikator_kinerja_kondisi_target_anggaran_awal;
        $program_indikator_kinerja->save();

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

            return response()->json(['success' => 'Berhasil merubah indikator program','html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->program_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->program_filter_urusan);
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

            return response()->json(['success' => 'Berhasil merubah indikator program','html' => $html]);
        }
    }

    public function opd_indikator_kinerja_edit($id)
    {
        $datas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $id)->get();
        $html = '';
        foreach ($datas as $data) {
            $html .= '<div class="alert alert-secondary alert-dismissable fade show" role="alert">
                '.$data->opd->nama.'
                <button type="button" class="btn btn-close text-body hapus-opd-program-indikator-kinerja" data-bs-dismiss="alert" data-id="'.$data->id.'" aria-hidden="true"></button>
            </div>';
        }

        return response()->json(['html' => $html]);
    }

    public function opd_indikator_kinerja_hapus(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'id' => 'required|array',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $id = $request->id;
        for ($i=0; $i < count($id); $i++) {
            $get_target_realisasies = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $id[$i])->get();
            foreach ($get_target_realisasies as $get_target_realisasi) {
                ProgramTargetSatuanRpRealisasi::find($get_target_realisasi->id)->delete();
            }
            OpdProgramIndikatorKinerja::find($id[$i])->delete();
        }

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

            return response()->json(['success' => 'Berhasil menghapus opd dari indikator program','html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->program_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->program_filter_urusan);
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

            return response()->json(['success' => 'Berhasil menghapus opd dari indikator program','html' => $html]);
        }
    }

    public function opd_indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tambah_opd_indikator_program_program_indikator_kinerja_id' => 'required',
            'tambah_opd_indikator_program_opd_id' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        // Cek Data
        $cek = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $request->tambah_opd_indikator_program_program_indikator_kinerja_id)
                ->where('opd_id', $request->tambah_opd_indikator_program_opd_id)->first();
        if($cek)
        {
            return response()->json(['errors' => 'Data sudah ada !, Tidak dapat menyimpan']);
        }

        $opd_id = $request->tambah_opd_indikator_program_opd_id;
        for ($i=0; $i < count($opd_id); $i++) {
            $opd = new OpdProgramIndikatorKinerja;
            $opd->program_indikator_kinerja_id = $request->tambah_opd_indikator_program_program_indikator_kinerja_id;
            $opd->opd_id = $opd_id[$i];
            $opd->save();
        }

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

            return response()->json(['success' => 'Berhasil mengubah opd dari indikator program','html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->program_filter_urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->program_filter_urusan);
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

            return response()->json(['success' => 'Berhasil mengedit opd dari indikator program','html' => $html]);
        }
    }

    public function hapus(Request $request)
    {
        if($request->hapus_program_tahun == 'semua')
        {
            $get_perubahan_programs = PivotPerubahanProgram::where('program_id', $request->hapus_program_id)->get();
            foreach ($get_perubahan_programs as $get_perubahan_program) {
                PivotPerubahanProgram::find($get_perubahan_program->id)->delete();
            }

            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $request->hapus_program_id)->get();
            foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                    $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->get();
                    foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                        $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->get();
                        foreach ($program_tw_realisasies as $program_tw_realisasi) {
                            ProgramTwRealisasi::find($program_tw_realisasi->id)->delete();
                        }

                        ProgramTargetSatuanRpRealisasi::find($program_target_satuan_rp_realisasi->id)->delete();
                    }

                    OpdProgramIndikatorKinerja::find($opd_program_indikator_kinerja->id)->delete();
                }

                ProgramIndikatorKinerja::find($program_indikator_kinerja->id)->delete();
            }

            $program_rpjmds = ProgramRpjmd::where('program_id', $request->hapus_program_id)->get();
            foreach ($program_rpjmds as $program_rpjmd) {
                $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)->get();
                foreach ($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd) {
                    PivotSasaranIndikatorProgramRpjmd::find($pivot_sasaran_indikator_program_rpjmd->id)->delete();
                }

                $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)->get();
                foreach ($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd) {
                    SasaranPdProgramRpjmd::find($sasaran_pd_program_rpjmd->id)->delete();
                }

                ProgramRpjmd::find($program_rpjmd->id)->delete();
            }

            $kegiatans = Kegiatan::where('program_id', $request->hapus_program_id)->get();
            foreach ($kegiatans as $kegiatan) {
                $get_perubahan_kegiatans = PivotPerubahanKegiatan::where('kegiatan_id', $kegiatan->id)->get();
                foreach ($get_perubahan_kegiatans as $get_perubahan_kegiatan) {
                    PivotPerubahanKegiatan::find($get_perubahan_kegiatan->id)->delete();
                }

                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan->id)->get();
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

                $sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan->id)->get();
                foreach ($sub_kegiatans as $sub_kegiatan) {
                    $get_perubahan_sub_kegiatans = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $sub_kegiatan->id)->get();
                    foreach ($get_perubahan_sub_kegiatans as $get_perubahan_sub_kegiatan) {
                        PivotPerubahanSubKegiatan::find($get_perubahan_sub_kegiatan->id)->delete();
                    }

                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan->id)->get();
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

                Kegiatan::find($kegiatan->id)->delete();
            }

            Program::find($request->hapus_program_id)->delete();
        } else {
            $cek_perubahan_program_1 = PivotPerubahanProgram::where('program_id', $request->hapus_program_id)->where('tahun_perubahan', $request->hapus_program_tahun)->first();
            if($cek_perubahan_program_1)
            {
                PivotPerubahanProgram::find($cek_perubahan_program_1->id)->delete();
            } else {
                // Logika jika malah tahun ada di program bukan di pivot perubahan program
                $cek_program = Program::where('tahun_perubahan', $request->hapus_program_tahun)->where('id', $request->hapus_program_id)->first();
                if($cek_program)
                {
                    $pivot_perubahan_program = PivotPerubahanProgram::where('program_id', $request->hapus_program_id)->first();
                    if($pivot_perubahan_program)
                    {
                        $edit_program = Program::find($cek_program->id);
                        $edit_program->tahun_perubahan = $pivot_perubahan_program->tahun_perubahan;
                        $edit_program->save();

                        PivotPerubahanProgram::find($pivot_perubahan_program->id)->delete();
                    } else {
                        return response()->json(['errors' => 'Pilih Pilihan Hapus Semua!']);
                    }
                }
                // Pengecekan jika menjadi satu - satunya
                $cek_perubahan_program_2 = PivotPerubahanProgram::where('program_id', $request->hapus_program_id)->first();
                if(!$cek_perubahan_program_2)
                {
                    $get_perubahan_programs = PivotPerubahanProgram::where('program_id', $request->hapus_program_id)->get();
                    foreach ($get_perubahan_programs as $get_perubahan_program) {
                        PivotPerubahanProgram::find($get_perubahan_program->id)->delete();
                    }

                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $request->hapus_program_id)->get();
                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                        $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                        foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                            $program_target_satuan_rp_realisasies = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->get();
                            foreach ($program_target_satuan_rp_realisasies as $program_target_satuan_rp_realisasi) {
                                $program_tw_realisasies = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $program_target_satuan_rp_realisasi->id)->get();
                                foreach ($program_tw_realisasies as $program_tw_realisasi) {
                                    ProgramTwRealisasi::find($program_tw_realisasi->id)->delete();
                                }

                                ProgramTargetSatuanRpRealisasi::find($program_target_satuan_rp_realisasi->id)->delete();
                            }

                            OpdProgramIndikatorKinerja::find($opd_program_indikator_kinerja->id)->delete();
                        }

                        ProgramIndikatorKinerja::find($program_indikator_kinerja->id)->delete();
                    }

                    $program_rpjmds = ProgramRpjmd::where('program_id', $request->hapus_program_id)->get();
                    foreach ($program_rpjmds as $program_rpjmd) {
                        $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)->get();
                        foreach ($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd) {
                            PivotSasaranIndikatorProgramRpjmd::find($pivot_sasaran_indikator_program_rpjmd->id)->delete();
                        }

                        $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)->get();
                        foreach ($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd) {
                            SasaranPdProgramRpjmd::find($sasaran_pd_program_rpjmd->id)->delete();
                        }

                        ProgramRpjmd::find($program_rpjmd->id)->delete();
                    }

                    $kegiatans = Kegiatan::where('program_id', $request->hapus_program_id)->get();
                    foreach ($kegiatans as $kegiatan) {
                        $get_perubahan_kegiatans = PivotPerubahanKegiatan::where('kegiatan_id', $kegiatan->id)->get();
                        foreach ($get_perubahan_kegiatans as $get_perubahan_kegiatan) {
                            PivotPerubahanKegiatan::find($get_perubahan_kegiatan->id)->delete();
                        }

                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan->id)->get();
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

                        $sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan->id)->get();
                        foreach ($sub_kegiatans as $sub_kegiatan) {
                            $get_perubahan_sub_kegiatans = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $sub_kegiatan->id)->get();
                            foreach ($get_perubahan_sub_kegiatans as $get_perubahan_sub_kegiatan) {
                                PivotPerubahanSubKegiatan::find($get_perubahan_sub_kegiatan->id)->delete();
                            }

                            $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan->id)->get();
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

                        Kegiatan::find($kegiatan->id)->delete();
                    }

                    Program::find($request->hapus_program_id)->delete();
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

            return response()->json(['success' => 'Berhasil menghapus program','html' => $html]);
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

            return response()->json(['success' => 'Berhasil menghapus program','html' => $html]);
        }
    }
}
