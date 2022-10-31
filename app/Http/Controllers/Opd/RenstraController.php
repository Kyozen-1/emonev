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
use Auth;

class RenstraController extends Controller
{
    public function index()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $get_urusans = Urusan::select('id', 'kode', 'deskripsi')->get();
        $urusans = [];
        foreach ($get_urusans as $get_urusan) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->orderBy('tahun_perubahan', 'desc')
                                        ->latest()->first();
            if($cek_perubahan_urusan)
            {
                $urusans[] = [
                    'id' => $cek_perubahan_urusan->urusan_id,
                    'kode' => $cek_perubahan_urusan->kode,
                    'deskripsi' => $cek_perubahan_urusan->deskripsi
                ];
            } else {
                $urusans[] = [
                    'id' => $get_urusan->id,
                    'kode' => $get_urusan->kode,
                    'deskripsi' => $get_urusan->deskripsi
                ];
            }
        }
        $get_misis = Misi::select('id', 'kode', 'deskripsi')->get();
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
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
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan
                ];
            } else {
                $misis[] = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan
                ];
            }
        }
        return view('opd.renstra.index', [
            'tahuns' => $tahuns,
            'urusans' => $urusans,
            'misis' => $misis
        ]);
    }

    public function filter_get_misi(Request $request)
    {
        $get_misis = new Misi;
        if($request->id == 'aman')
        {
            $get_misis = $get_misis->where(function($q){
                $q->where('kode', 1)->orWhere('kode', 2);
            });
        }
        if($request->id == 'mandiri')
        {
            $get_misis = $get_misis->where('kode', 3);
        }
        if($request->id == 'sejahtera')
        {
            $get_misis = $get_misis->where('kode', 4);
        }
        if($request->id == 'berahlak')
        {
            $get_misis = $get_misis->where('kode', 5);
        }
        $get_misis = $get_misis->get();
        $misis = [];
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::select('misi_id', 'deskripsi', 'kode')
                                    ->where('misi_id', $get_misi->id)
                                    ->latest()->first();
            if($cek_perubahan_misi)
            {
                $misis[] = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'kode' => $cek_perubahan_misi->kode
                ];
            } else {
                $misis[] = [
                    'id' => $get_misi->id,
                    'deskripsi' => $get_misi->deskripsi,
                    'kode' => $get_misi->kode
                ];
            }
        }
        return response()->json($misis);
    }

    public function filter_get_tujuan(Request $request)
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

    public function filter_get_sasaran(Request $request)
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

    public function filter_get_program(Request $request)
    {
        $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($request){
            $q->whereHas('pivot_sasaran_indikator', function($q) use ($request) {
                $q->where('sasaran_id', $request->id);
            });
        })->whereHas('pivot_opd_program_rpjmd', function($q){
            $q->where('opd_id', Auth::user()->opd->opd_id);
        })->get();

        $programs = [];
        foreach ($get_program_rpjmds as $get_program_rpjmd) {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                        ->orderBy('tahun_perubahan','desc')->latest()->first();
            if($cek_perubahan_program)
            {
                $programs[] = [
                    'id' => $cek_perubahan_program->program_id,
                    'kode' => $cek_perubahan_program->kode,
                    'deskripsi' => $cek_perubahan_program->deskripsi,
                    'program_rpjmd_id' => $get_program_rpjmd->id
                ];
            } else {
                $get_program = Program::find($get_program_rpjmd->program_id);
                $programs[] = [
                    'id' =>$get_program->id,
                    'kode' => $get_program->kode,
                    'deskripsi' => $get_program->deskripsi,
                    'program_rpjmd_id' => $get_program_rpjmd->id
                ];
            }
        }

        return response()->json($programs);
    }

    public function filter_get_kegiatan(Request $request)
    {
        $get_renstra_kegiatans = RenstraKegiatan::where('program_rpjmd_id', $request->id)->get();
        $kegiatans = [];
        foreach ($get_renstra_kegiatans as $get_renstra_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                        ->orderBy('tahun_perubahan', 'desc')->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatans[] = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' =>$cek_perubahan_kegiatan->deskripsi
                ];
            } else {
                $kegiatans[] = [
                    'id' => $get_renstra_kegiatan->kegiatan_id,
                    'kode' => $get_renstra_kegiatan->kegiatan->kode,
                    'deskripsi' =>$get_renstra_kegiatan->kegiatan->deskripsi
                ];
            }
        }

        return response()->json($kegiatans);
    }

    public function option_kegiatan(Request $request)
    {
        $get_kegiatans = Kegiatan::where('program_id', $request->id)->get();
        $kegiatans = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                        ->orderBy('tahun_perubahan', 'desc')->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatans[] = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                ];
            } else {
                $kegiatans[] = [
                    'id' => $get_kegiatan->id,
                    'kode' => $get_kegiatan->kode,
                    'deskripsi' => $get_kegiatan->deskripsi
                ];
            }
        }

        return response()->json($kegiatans);
    }

    public function get_tujuan()
    {
        $get_visis = Visi::all();
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
        $visis = [];
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
                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraTujuan" checked>
                            <label class="form-check-label" for="onOffTaggingTujuan">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim" id="tujuan_div_table">
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
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase renstra-tujuan-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse" id="tujuan_visi'.$visi['id'].'">
                                            <table class="table table-striped table-condesed">
                                                <tbody>';
                                                $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                $misis = [];
                                                foreach ($get_misis as $get_misi) {
                                                    $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                                                            ->where('tahun_perubahan', $tahun_sekarang)
                                                                            ->latest()
                                                                            ->first();
                                                    if($cek_perubahan_misi)
                                                    {
                                                        $misis[] = [
                                                            'id' => $cek_perubahan_misi->misi_id,
                                                            'kode' => $cek_perubahan_misi->kode,
                                                            'deskripsi' => $cek_perubahan_misi->deskripsi,
                                                            'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan
                                                        ];
                                                    } else {
                                                        $misis[] = [
                                                            'id' => $get_misi->id,
                                                            'kode' => $get_misi->kode,
                                                            'deskripsi' => $get_misi->deskripsi,
                                                            'tahun_perubahan' => $get_misi->tahun_perubahan
                                                        ];
                                                    }
                                                }
                                                $a = 1;
                                                foreach ($misis as $misi) {
                                                    $html .= '<tr>
                                                        <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                        <td width="95%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">
                                                            '.$misi['deskripsi'].'
                                                            <br>';
                                                            if($a == 1 || $a == 2)
                                                            {
                                                                $html .= '<span class="badge bg-primary text-uppercase renstra-tujuan-tagging">Visi [Aman]</span>';
                                                            }
                                                            if($a == 3)
                                                            {
                                                                $html .= '<span class="badge bg-primary text-uppercase renstra-tujuan-tagging">Visi [Mandiri]</span>';
                                                            }
                                                            if($a == 4)
                                                            {
                                                                $html .= '<span class="badge bg-primary text-uppercase renstra-tujuan-tagging">Visi [Sejahtera]</span>';
                                                            }
                                                            if($a == 5)
                                                            {
                                                                $html .= '<span class="badge bg-primary text-uppercase renstra-tujuan-tagging">Visi [Berahlak]</span>';
                                                            }
                                                            $html .= ' <span class="badge bg-warning text-uppercase renstra-tujuan-tagging">Misi '.$misi['kode'].' </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="hiddenRow">
                                                            <div class="collapse" id="tujuan_misi'.$misi['id'].'">
                                                                <table class="table table-striped table-condesed">
                                                                    <tbody>';
                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->orderBy('kode', 'asc')->get();
                                                                    $tujuans = [];
                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                                                                                    ->where('tahun_perubahan', $tahun_sekarang)
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
                                                                            <td width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                            <td width="95%">
                                                                                '.$tujuan['deskripsi'].'
                                                                                <br>';
                                                                                if($a == 1 || $a == 2)
                                                                                {
                                                                                    $html .= '<span class="badge bg-primary text-uppercase renstra-tujuan-tagging">Visi [Aman]</span>';
                                                                                }
                                                                                if($a == 3)
                                                                                {
                                                                                    $html .= '<span class="badge bg-primary text-uppercase renstra-tujuan-tagging">Visi [Mandiri]</span>';
                                                                                }
                                                                                if($a == 4)
                                                                                {
                                                                                    $html .= '<span class="badge bg-primary text-uppercase renstra-tujuan-tagging">Visi [Sejahtera]</span>';
                                                                                }
                                                                                if($a == 5)
                                                                                {
                                                                                    $html .= '<span class="badge bg-primary text-uppercase renstra-tujuan-tagging">Visi [Berahlak]</span>';
                                                                                }
                                                                                $html .= ' <span class="badge bg-warning text-uppercase renstra-tujuan-tagging">Misi '.$misi['kode'].'</span>
                                                                                <span class="badge bg-secondary text-uppercase renstra-tujuan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
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
                            $html .= '</tbody>
                        </table>
                    </div>
                </div>';
        return response()->json(['html' => $html]);
    }

    public function get_filter_tujuan(Request $request)
    {
        $get_visis = Visi::all();
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
        $visis = [];
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
                            <input class="form-check-input" type="checkbox" id="onOffTaggingTujuan" checked>
                            <label class="form-check-label" for="onOffTaggingTujuan">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim" id="tujuan_div_table">
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
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase tujuan-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse" id="tujuan_visi'.$visi['id'].'">
                                            <table class="table table-striped table-condesed">
                                                <tbody>';
                                                $get_misis = Misi::where('visi_id', $visi['id']);
                                                if($request->visi == 'aman')
                                                {
                                                    $get_misis = $get_misis->where(function($q){
                                                        $q->where('kode', 1)->orWhere('kode', 2);
                                                    });
                                                }
                                                if($request->visi == 'mandiri')
                                                {
                                                    $get_misis = $get_misis->where('kode', 3);
                                                }
                                                if($request->visi == 'sejahtera')
                                                {
                                                    $get_misis = $get_misis->where('kode', 4);
                                                }
                                                if($request->visi == 'berahlak')
                                                {
                                                    $get_misis = $get_misis->where('kode', 5);
                                                }
                                                if($request->misi)
                                                {
                                                    $get_misis = $get_misis->where('id', $request->misi);
                                                }
                                                $get_misis = $get_misis->get();
                                                $misis = [];
                                                foreach ($get_misis as $get_misi) {
                                                    $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                                                            ->where('tahun_perubahan', $tahun_sekarang)
                                                                            ->latest()
                                                                            ->first();
                                                    if($cek_perubahan_misi)
                                                    {
                                                        $misis[] = [
                                                            'id' => $cek_perubahan_misi->misi_id,
                                                            'kode' => $cek_perubahan_misi->kode,
                                                            'deskripsi' => $cek_perubahan_misi->deskripsi,
                                                            'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan
                                                        ];
                                                    } else {
                                                        $misis[] = [
                                                            'id' => $get_misi->id,
                                                            'kode' => $get_misi->kode,
                                                            'deskripsi' => $get_misi->deskripsi,
                                                            'tahun_perubahan' => $get_misi->tahun_perubahan
                                                        ];
                                                    }
                                                }
                                                $a = 1;
                                                foreach ($misis as $misi) {
                                                    $html .= '<tr>
                                                        <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                        <td width="95%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">
                                                            '.$misi['deskripsi'].'
                                                            <br>';
                                                            $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi '.$request->visi.'</span>';
                                                            $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].' </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="hiddenRow">
                                                            <div class="collapse" id="tujuan_misi'.$misi['id'].'">
                                                                <table class="table table-striped table-condesed">
                                                                    <tbody>';
                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id']);
                                                                    if($request->tujuan)
                                                                    {
                                                                        $get_tujuans = $get_tujuans->where('id', $request->tujuan);
                                                                    }
                                                                    $get_tujuans = $get_tujuans->orderBy('kode', 'asc')->get();
                                                                    $tujuans = [];
                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                                                                                    ->where('tahun_perubahan', $tahun_sekarang)
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
                                                                            <td width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                            <td width="95%">
                                                                                '.$tujuan['deskripsi'].'
                                                                                <br>';
                                                                                $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi '.$request->visi.'</span>';
                                                                                $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].'</span>
                                                                                <span class="badge bg-secondary text-uppercase tujuan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
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
                            $html .= '</tbody>
                        </table>
                    </div>
                </div>';
        return response()->json(['html' => $html]);
    }

    public function get_sasaran()
    {
        $get_visis = Visi::all();
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
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
                    'id' => $get_visi->id,
                    'deskripsi' => $get_visi->deskripsi,
                    'tahun_perubahan' => $get_visi->tahun_perubahan
                ];
            }
        }

        $html = '<div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraSasaran" checked>
                            <label class="form-check-label" for="onOffTaggingRenstraSasaran">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim" id="sasaran_div_table">
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
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse" id="sasaran_visi'.$visi['id'].'">
                                            <table class="table table-striped table-condensed">
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
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">
                                                                        '.$misi['deskripsi'].'
                                                                        <br>';
                                                                        if($a == 1 || $a == 2)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Aman]</span>';
                                                                        }
                                                                        if($a == 3)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Mandiri]</span>';
                                                                        }
                                                                        if($a == 4)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Sejahtera]</span>';
                                                                        }
                                                                        if($a == 5)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Berahlak]</span>';
                                                                        }
                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-sasaran-tagging">'.$misi['kode'].' Misi</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse" id="sasaran_misi'.$misi['id'].'">
                                                                            <table class="table table-striped table-condensed">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan',$tahun_sekarang)
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                                                                                                        '.$tujuan['deskripsi'].'
                                                                                                        <br>';
                                                                                                        if($a == 1 || $a == 2)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Aman]</span>';
                                                                                                        }
                                                                                                        if($a == 3)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Mandiri]</span>';
                                                                                                        }
                                                                                                        if($a == 4)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Sejahtera]</span>';
                                                                                                        }
                                                                                                        if($a == 5)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Berahlak]</span>';
                                                                                                        }
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase renstra-sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse" id="sasaran_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-striped table-condensed">
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
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="95%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>';
                                                                                                                                        if($a == 1 || $a == 2)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Aman]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 3)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Mandiri]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 4)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Sejahtera]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 5)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Berahlak]</span>';
                                                                                                                                        }
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse" id="sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="5%"><strong>No</strong></th>
                                                                                                                                                        <th width="65%"><strong>Sasaran Indikator</strong></th>
                                                                                                                                                        <th width="15%"><strong>Target</strong></th>
                                                                                                                                                        <th width="15%"><strong>Satuan</strong></th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                    $sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                    $b = 1;
                                                                                                                                                    foreach ($sasaran_indikators as $sasaran_indikator) {
                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                    <td>'.$b++.'</td>
                                                                                                                                                                    <td>
                                                                                                                                                                        '.$sasaran_indikator['indikator'].'
                                                                                                                                                                        <br>';
                                                                                                                                                                        if($a == 1 || $a == 2)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Aman]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 3)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 4)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 5)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                                                    </td>
                                                                                                                                                                    <td>
                                                                                                                                                                        '.$sasaran_indikator['target'].'
                                                                                                                                                                    </td>
                                                                                                                                                                    <td>
                                                                                                                                                                        '.$sasaran_indikator['satuan'].'
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
        return response()->json(['html' => $html]);
    }

    public function get_filter_sasaran(Request $request)
    {
        $get_visis = Visi::all();
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
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
                    'id' => $get_visi->id,
                    'deskripsi' => $get_visi->deskripsi,
                    'tahun_perubahan' => $get_visi->tahun_perubahan
                ];
            }
        }

        $html = '<div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraSasaran" checked>
                            <label class="form-check-label" for="onOffTaggingRenstraSasaran">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim" id="sasaran_div_table">
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
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse" id="sasaran_visi'.$visi['id'].'">
                                            <table class="table table-striped table-condensed">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id']);
                                                    if($request->visi == 'aman')
                                                    {
                                                        $get_misis = $get_misis->where(function($q){
                                                            $q->where('kode', 1)->orWhere('kode', 2);
                                                        });
                                                    }
                                                    if($request->visi == 'mandiri')
                                                    {
                                                        $get_misis = $get_misis->where('kode', 3);
                                                    }
                                                    if($request->visi == 'sejahtera')
                                                    {
                                                        $get_misis = $get_misis->where('kode', 4);
                                                    }
                                                    if($request->visi == 'berahlak')
                                                    {
                                                        $get_misis = $get_misis->where('kode', 5);
                                                    }
                                                    if($request->misi)
                                                    {
                                                        $get_misis = $get_misis->where('id', $request->misi);
                                                    }
                                                    $get_misis = $get_misis->get();
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
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">
                                                                        '.$misi['deskripsi'].'
                                                                        <br>';
                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-sasaran-tagging">'.$misi['kode'].' Misi</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse" id="sasaran_misi'.$misi['id'].'">
                                                                            <table class="table table-striped table-condensed">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id']);
                                                                                    if($request->tujuan)
                                                                                    {
                                                                                        $get_tujuans = $get_tujuans->where('id', $request->tujuan);
                                                                                    }
                                                                                    $get_tujuans = $get_tujuans->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan',$tahun_sekarang)
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                                                                                                        '.$tujuan['deskripsi'].'
                                                                                                        <br>';
                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase renstra-sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse" id="sasaran_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id']);
                                                                                                                    if($request->sasaran)
                                                                                                                    {
                                                                                                                        $get_sasarans = $get_sasarans->where('id', $request->sasaran);
                                                                                                                    }
                                                                                                                    $get_sasarans = $get_sasarans->get();
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
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="95%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>';
                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse" id="sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="5%"><strong>No</strong></th>
                                                                                                                                                        <th width="65%"><strong>Sasaran Indikator</strong></th>
                                                                                                                                                        <th width="15%"><strong>Target</strong></th>
                                                                                                                                                        <th width="15%"><strong>Satuan</strong></th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                    $sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                    $b = 1;
                                                                                                                                                    foreach ($sasaran_indikators as $sasaran_indikator) {
                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                    <td>'.$b++.'</td>
                                                                                                                                                                    <td>
                                                                                                                                                                        '.$sasaran_indikator['indikator'].'
                                                                                                                                                                        <br>';
                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                                                    </td>
                                                                                                                                                                    <td>
                                                                                                                                                                        '.$sasaran_indikator['target'].'
                                                                                                                                                                    </td>
                                                                                                                                                                    <td>
                                                                                                                                                                        '.$sasaran_indikator['satuan'].'
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
        return response()->json(['html' => $html]);
    }

    public function get_program()
    {
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
                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraProgram" checked>
                            <label class="form-check-label" for="onOffTaggingRenstraProgram">On / Off Tagging</label>
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
                                        <span class="badge bg-primary text-uppercase renstra-program-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse" id="program_visi'.$visi['id'].'">
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
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Aman]</span>';
                                                                        }
                                                                        if($a == 3)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Mandiri]</span>';
                                                                        }
                                                                        if($a == 4)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Sejahtera]</span>';
                                                                        }
                                                                        if($a == 5)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Berahlak]</span>';
                                                                        }
                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-program-tagging">Misi '.$misi['kode'].'</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse" id="program_misi'.$misi['id'].'">
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
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Aman]</span>';
                                                                                                        }
                                                                                                        if($a == 3)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Mandiri]</span>';
                                                                                                        }
                                                                                                        if($a == 4)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Sejahtera]</span>';
                                                                                                        }
                                                                                                        if($a == 5)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Berahlak]</span>';
                                                                                                        }
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase renstra-program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse" id="program_tujuan'.$tujuan['id'].'">
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
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Aman]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 3)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Mandiri]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 4)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 5)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Berahlak]</span>';
                                                                                                                                        }
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse" id="program_sasaran_indikator'.$sasaran['id'].'">
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
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Aman]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 3)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 4)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 5)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
                                                                                                                                                                        <div class="collapse" id="program_rpjmd'.$sasaran_indikator['id'].'">
                                                                                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th width="5%"><strong>No</strong></th>
                                                                                                                                                                                        <th width="45%"><strong>Program RPJMD</strong></th>
                                                                                                                                                                                        <th width="5%"><strong>Target</strong></th>
                                                                                                                                                                                        <th width="5%"><strong>Satuan</strong></th>
                                                                                                                                                                                        <th width="10%"><strong>Rp</strong></th>
                                                                                                                                                                                        <th width="20%"><strong>OPD</strong></th>
                                                                                                                                                                                        <th width="10%"><strong>Pagu</strong></th>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>';
                                                                                                                                                                                    $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran_indikator){
                                                                                                                                                                                        $q->where('sasaran_indikator_id', $sasaran_indikator['id']);
                                                                                                                                                                                    })->whereHas('pivot_opd_program_rpjmd', function($q){
                                                                                                                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
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
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Aman]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 3)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 4)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 5)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<span class="badge bg-warning text-uppercase renstra-program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-secondary text-uppercase renstra-program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-danger text-uppercase renstra-program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
        return response()->json(['html' => $html]);
    }

    public function get_filter_program(Request $request)
    {
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
                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraProgram" checked>
                            <label class="form-check-label" for="onOffTaggingRenstraProgram">On / Off Tagging</label>
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
                                        <span class="badge bg-primary text-uppercase renstra-program-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse" id="program_visi'.$visi['id'].'">
                                            <table class="table table-condensed table-striped">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id']);
                                                    if($request->visi == 'aman')
                                                    {
                                                        $get_misis = $get_misis->where(function($q){
                                                            $q->where('kode', 1)->orWhere('kode', 2);
                                                        });
                                                    }
                                                    if($request->visi == 'mandiri')
                                                    {
                                                        $get_misis = $get_misis->where('kode', 3);
                                                    }
                                                    if($request->visi == 'sejahtera')
                                                    {
                                                        $get_misis = $get_misis->where('kode', 4);
                                                    }
                                                    if($request->visi == 'berahlak')
                                                    {
                                                        $get_misis = $get_misis->where('kode', 5);
                                                    }
                                                    if($request->misi)
                                                    {
                                                        $get_misis = $get_misis->where('id', $request->misi);
                                                    }
                                                    $get_misis = $get_misis->get();
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
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Aman]</span>';
                                                                        }
                                                                        if($a == 3)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Mandiri]</span>';
                                                                        }
                                                                        if($a == 4)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Sejahtera]</span>';
                                                                        }
                                                                        if($a == 5)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Berahlak]</span>';
                                                                        }
                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-program-tagging">Misi '.$misi['kode'].'</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse" id="program_misi'.$misi['id'].'">
                                                                            <table class="table table-condensed table-striped">
                                                                                <tbody>';
                                                                                $get_tujuans = Tujuan::where('misi_id', $misi['id']);
                                                                                if($request->tujuan)
                                                                                {
                                                                                    $get_tujuans = $get_tujuans->where('id', $request->tujuan);
                                                                                }
                                                                                $get_tujuans = $get_tujuans->get();
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
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Aman]</span>';
                                                                                                        }
                                                                                                        if($a == 3)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Mandiri]</span>';
                                                                                                        }
                                                                                                        if($a == 4)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Sejahtera]</span>';
                                                                                                        }
                                                                                                        if($a == 5)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Berahlak]</span>';
                                                                                                        }
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase renstra-program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse" id="program_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id']);
                                                                                                                    if($request->sasaran)
                                                                                                                    {
                                                                                                                        $get_sasarans = $get_sasarans->where('id', $request->sasaran);
                                                                                                                    }
                                                                                                                    $get_sasarans = $get_sasarans->get();
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
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Aman]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 3)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Mandiri]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 4)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 5)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Berahlak]</span>';
                                                                                                                                        }
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse" id="program_sasaran_indikator'.$sasaran['id'].'">
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
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Aman]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 3)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 4)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 5)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
                                                                                                                                                                        <div class="collapse" id="program_rpjmd'.$sasaran_indikator['id'].'">
                                                                                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th width="5%"><strong>No</strong></th>
                                                                                                                                                                                        <th width="45%"><strong>Program RPJMD</strong></th>
                                                                                                                                                                                        <th width="5%"><strong>Target</strong></th>
                                                                                                                                                                                        <th width="5%"><strong>Satuan</strong></th>
                                                                                                                                                                                        <th width="10%"><strong>Rp</strong></th>
                                                                                                                                                                                        <th width="20%"><strong>OPD</strong></th>
                                                                                                                                                                                        <th width="10%"><strong>Pagu</strong></th>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>';
                                                                                                                                                                                    $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran_indikator){
                                                                                                                                                                                        $q->where('sasaran_indikator_id', $sasaran_indikator['id']);
                                                                                                                                                                                    })->whereHas('pivot_opd_program_rpjmd', function($q){
                                                                                                                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                                                    });
                                                                                                                                                                                    if($request->program)
                                                                                                                                                                                    {
                                                                                                                                                                                        $get_program_rpjmds = $get_program_rpjmds->where('program_id', $request->program);
                                                                                                                                                                                    }
                                                                                                                                                                                    $get_program_rpjmds = $get_program_rpjmds->get();
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
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Aman]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 3)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 4)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 5)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-program-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<span class="badge bg-warning text-uppercase renstra-program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-secondary text-uppercase renstra-program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-danger text-uppercase renstra-program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
        return response()->json(['html' => $html]);
    }

    public function get_kegiatan(Request $request)
    {
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
                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraKegiatan" checked>
                            <label class="form-check-label" for="onOffTaggingRenstraKegiatan">On / Off Tagging</label>
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
                                        <span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse" id="program_visi'.$visi['id'].'">
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
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                        }
                                                                        if($a == 3)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                        }
                                                                        if($a == 4)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                        }
                                                                        if($a == 5)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                        }
                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse" id="program_misi'.$misi['id'].'">
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
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                        }
                                                                                                        if($a == 3)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                        }
                                                                                                        if($a == 4)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                        }
                                                                                                        if($a == 5)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                        }
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse" id="program_tujuan'.$tujuan['id'].'">
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
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 3)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 4)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 5)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                                                        }
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse" id="program_sasaran_indikator'.$sasaran['id'].'">
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
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 3)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 4)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 5)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
                                                                                                                                                                        <div class="collapse" id="program_rpjmd'.$sasaran_indikator['id'].'">
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
                                                                                                                                                                                                'program_id' => $get_program_rpjmd->program_id,
                                                                                                                                                                                            ];
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $program = Program::find($get_program_rpjmd->program_id);
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu,
                                                                                                                                                                                                'program_id' => $get_program_rpjmd->program_id,
                                                                                                                                                                                            ];
                                                                                                                                                                                        }
                                                                                                                                                                                    }
                                                                                                                                                                                    $c = 1;
                                                                                                                                                                                    foreach ($programs as $program) {
                                                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">'.$c++.'</td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">
                                                                                                                                                                                                    '.$program['deskripsi'];
                                                                                                                                                                                                    if($program['status_program'] == "Program Prioritas")
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= ' <i title="Program Prioritas" class="fas fa-star text-primary"></i>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= ' <br> ';
                                                                                                                                                                                                    if($a == 1 || $a == 2)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 3)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 4)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 5)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">'.$get_target_rp->satuan.'</td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">Rp. '.number_format(array_sum($program_rp), 2).'</td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle"></td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle"></td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle"></td>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $html .= '<td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">';
                                                                                                                                                                                                $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $program['id'])->get();
                                                                                                                                                                                                $html .= '<ul>';
                                                                                                                                                                                                    foreach ($get_opds as $get_opd) {
                                                                                                                                                                                                        $html .= '<li>'.$get_opd->opd->nama.'</li>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                $html.='</ul>';
                                                                                                                                                                                                $html.= '</td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">
                                                                                                                                                                                                    Rp. '.number_format($program['pagu'], 2).'
                                                                                                                                                                                                </td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    <button class="btn btn-primary waves-effect waves-light renstra_kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditRenstraKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['program_id'].'" data-program-rpjmd-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                                                                                                                </td>
                                                                                                                                                                                            </tr>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <td colspan="8" class="hiddenRow">
                                                                                                                                                                                                    <div class="collapse" id="kegiatan_renstra'.$program['id'].'">
                                                                                                                                                                                                        <table class="table table-condensed table-striped">
                                                                                                                                                                                                            <thead>
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <th width="5%"><strong>No</strong></th>
                                                                                                                                                                                                                    <th width="35%"><strong>Kegiatan</strong></th>
                                                                                                                                                                                                                    <th width="5%"><strong>Target</strong></th>
                                                                                                                                                                                                                    <th width="5%"><strong>Satuan</strong></th>
                                                                                                                                                                                                                    <th width="10%"><strong>Rp</strong></th>
                                                                                                                                                                                                                    <th width="20%"><strong>OPD</strong></th>
                                                                                                                                                                                                                    <th width="10%"><strong>Pagu</strong></th>
                                                                                                                                                                                                                    <th width="10%"><strong>Aksi</strong></th>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                            </thead>
                                                                                                                                                                                                            <tbody>';
                                                                                                                                                                                                            $get_renstra_kegiatans = RenstraKegiatan::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                                                    ->get();
                                                                                                                                                                                                            $kegiatans = [];
                                                                                                                                                                                                            foreach ($get_renstra_kegiatans as $get_renstra_kegiatan) {
                                                                                                                                                                                                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                                                                                                                                                                                                            ->orderBy('tahun_perubahan', 'desc')
                                                                                                                                                                                                                                            ->latest()->first();
                                                                                                                                                                                                                if($cek_perubahan_kegiatan)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $kegiatans[] = [
                                                                                                                                                                                                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                                                                                                                        'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                                                                                                                        'deskripsi'  => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                                                                                                                        'pagu' => $get_renstra_kegiatan->pagu,
                                                                                                                                                                                                                        'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                                                                                                                                                                                                                    ];
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    $kegiatan = Kegiatan::find($get_renstra_kegiatan->kegiatan_id);
                                                                                                                                                                                                                    $kegiatans[] = [
                                                                                                                                                                                                                        'id' => $kegiatan->id,
                                                                                                                                                                                                                        'kode' => $kegiatan->kode,
                                                                                                                                                                                                                        'deskripsi'  => $kegiatan->deskripsi,
                                                                                                                                                                                                                        'pagu' => $get_renstra_kegiatan->pagu,
                                                                                                                                                                                                                        'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                                                                                                                                                                                                                    ];
                                                                                                                                                                                                                }
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $d = 1;
                                                                                                                                                                                                            foreach ($kegiatans as $kegiatan) {
                                                                                                                                                                                                                $html .= '<tr>
                                                                                                                                                                                                                    <td>'.$d++.'</td>
                                                                                                                                                                                                                    <td>'.$kegiatan['deskripsi'].'</td>';
                                                                                                                                                                                                                    $cek_target_rp_pertahun_renstra_kegiatan = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                                                                                                                                                                                    ->where('tahun', $tahun_sekarang)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                                    if($cek_target_rp_pertahun_renstra_kegiatan)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        $get_target_rp_pertahun_renstra_kegiatans = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                                                                                                                                                                                                        ->where('tahun', $tahun_sekarang)
                                                                                                                                                                                                                                        ->get();
                                                                                                                                                                                                                        $kegiatan_target = [];
                                                                                                                                                                                                                        $kegiatan_rp = [];
                                                                                                                                                                                                                        foreach ($get_target_rp_pertahun_renstra_kegiatans as $get_target_rp_pertahun_renstra_kegiatan) {
                                                                                                                                                                                                                            $kegiatan_target[] = $get_target_rp_pertahun_renstra_kegiatan->target;
                                                                                                                                                                                                                            $kegiatan_rp[] = $get_target_rp_pertahun_renstra_kegiatan->rp;
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        $html .= '<td>'.array_sum($kegiatan_target).'</td>
                                                                                                                                                                                                                    <td>'.$cek_target_rp_pertahun_renstra_kegiatan->satuan.'</td>
                                                                                                                                                                                                                    <td>Rp. '.number_format(array_sum($kegiatan_rp), 2).'</td>';
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        $html .= '<td></td>
                                                                                                                                                                                                                    <td></td>
                                                                                                                                                                                                                    <td></td>';
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                $get_opd_renstra_kegiatans = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])->get();
                                                                                                                                                                                                                $html .= '<td><ul>';
                                                                                                                                                                                                                foreach ($get_opd_renstra_kegiatans as $get_opd_renstra_kegiatan) {
                                                                                                                                                                                                                    $html .= '<li>'.$get_opd_renstra_kegiatan->opd->nama.'</li>';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                $html .='</ul></td>';
                                                                                                                                                                                                                $html .= '<td>Rp. '.number_format($kegiatan['pagu']).'</td>';
                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-renstra-kegiatan" data-renstra-kegiatan-id="'.$kegiatan['renstra_kegiatan_id'].'" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                                                                                                                </td>';
                                                                                                                                                                                                                $html .= '</tr>';
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
        return response()->json(['html' => $html]);
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'renstra_kegiatan_program_rpjmd_id' => 'required',
            'renstra_kegiatan_kegiatan_id' => 'required',
            'renstra_kegiatan_opd_id' => 'required',
            'renstra_kegiatan_pagu' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        $cek_renstra_kegiatan = RenstraKegiatan::where('program_rpjmd_id', $request->renstra_kegiatan_program_rpjmd_id)->first();
        if($cek_renstra_kegiatan)
        {
            $renstra_kegiatan = RenstraKegiatan::find($cek_renstra_kegiatan->id);
        } else {
            $renstra_kegiatan = new RenstraKegiatan;
        }
        $renstra_kegiatan->program_rpjmd_id = $request->renstra_kegiatan_program_rpjmd_id;
        $renstra_kegiatan->kegiatan_id = $request->renstra_kegiatan_kegiatan_id;
        $renstra_kegiatan->pagu = $request->renstra_kegiatan_pagu;
        $renstra_kegiatan->save();

        $cek_opd = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $renstra_kegiatan->id)
                    ->where('opd_id', $request->renstra_kegiatan_opd_id)->first();
        if(!$cek_opd)
        {
            $pivot_opd = new PivotOpdRentraKegiatan;
            $pivot_opd->rentra_kegiatan_id = $renstra_kegiatan->id;
            $pivot_opd->opd_id = $request->renstra_kegiatan_opd_id;
            $pivot_opd->save();
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
                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraKegiatan" checked>
                            <label class="form-check-label" for="onOffTaggingRenstraKegiatan">On / Off Tagging</label>
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
                                        <span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse" id="program_visi'.$visi['id'].'">
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
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                        }
                                                                        if($a == 3)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                        }
                                                                        if($a == 4)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                        }
                                                                        if($a == 5)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                        }
                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse" id="program_misi'.$misi['id'].'">
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
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                        }
                                                                                                        if($a == 3)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                        }
                                                                                                        if($a == 4)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                        }
                                                                                                        if($a == 5)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                        }
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse" id="program_tujuan'.$tujuan['id'].'">
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
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 3)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 4)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 5)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                                                        }
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse" id="program_sasaran_indikator'.$sasaran['id'].'">
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
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 3)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 4)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 5)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
                                                                                                                                                                        <div class="collapse" id="program_rpjmd'.$sasaran_indikator['id'].'">
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
                                                                                                                                                                                                'program_id' => $get_program_rpjmd->program_id,
                                                                                                                                                                                            ];
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $program = Program::find($get_program_rpjmd->program_id);
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu,
                                                                                                                                                                                                'program_id' => $get_program_rpjmd->program_id,
                                                                                                                                                                                            ];
                                                                                                                                                                                        }
                                                                                                                                                                                    }
                                                                                                                                                                                    $c = 1;
                                                                                                                                                                                    foreach ($programs as $program) {
                                                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">'.$c++.'</td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">
                                                                                                                                                                                                    '.$program['deskripsi'];
                                                                                                                                                                                                    if($program['status_program'] == "Program Prioritas")
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= ' <i title="Program Prioritas" class="fas fa-star text-primary"></i>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= ' <br> ';
                                                                                                                                                                                                    if($a == 1 || $a == 2)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 3)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 4)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 5)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">'.$get_target_rp->satuan.'</td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">Rp. '.number_format(array_sum($program_rp), 2).'</td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle"></td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle"></td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle"></td>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $html .= '<td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">';
                                                                                                                                                                                                $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $program['id'])->get();
                                                                                                                                                                                                $html .= '<ul>';
                                                                                                                                                                                                    foreach ($get_opds as $get_opd) {
                                                                                                                                                                                                        $html .= '<li>'.$get_opd->opd->nama.'</li>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                $html.='</ul>';
                                                                                                                                                                                                $html.= '</td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">
                                                                                                                                                                                                    Rp. '.number_format($program['pagu'], 2).'
                                                                                                                                                                                                </td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    <button class="btn btn-primary waves-effect waves-light renstra_kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditRenstraKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['program_id'].'" data-program-rpjmd-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                                                                                                                </td>
                                                                                                                                                                                            </tr>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <td colspan="8" class="hiddenRow">
                                                                                                                                                                                                    <div class="collapse" id="kegiatan_renstra'.$program['id'].'">
                                                                                                                                                                                                        <table class="table table-condensed table-striped">
                                                                                                                                                                                                            <thead>
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <th width="5%"><strong>No</strong></th>
                                                                                                                                                                                                                    <th width="35%"><strong>Kegiatan</strong></th>
                                                                                                                                                                                                                    <th width="5%"><strong>Target</strong></th>
                                                                                                                                                                                                                    <th width="5%"><strong>Satuan</strong></th>
                                                                                                                                                                                                                    <th width="10%"><strong>Rp</strong></th>
                                                                                                                                                                                                                    <th width="20%"><strong>OPD</strong></th>
                                                                                                                                                                                                                    <th width="10%"><strong>Pagu</strong></th>
                                                                                                                                                                                                                    <th width="10%"><strong>Aksi</strong></th>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                            </thead>
                                                                                                                                                                                                            <tbody>';
                                                                                                                                                                                                            $get_renstra_kegiatans = RenstraKegiatan::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                                                    ->get();
                                                                                                                                                                                                            $kegiatans = [];
                                                                                                                                                                                                            foreach ($get_renstra_kegiatans as $get_renstra_kegiatan) {
                                                                                                                                                                                                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                                                                                                                                                                                                            ->orderBy('tahun_perubahan', 'desc')
                                                                                                                                                                                                                                            ->latest()->first();
                                                                                                                                                                                                                if($cek_perubahan_kegiatan)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $kegiatans[] = [
                                                                                                                                                                                                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                                                                                                                        'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                                                                                                                        'deskripsi'  => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                                                                                                                        'pagu' => $get_renstra_kegiatan->pagu,
                                                                                                                                                                                                                        'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                                                                                                                                                                                                                    ];
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    $kegiatan = Kegiatan::find($get_renstra_kegiatan->kegiatan_id);
                                                                                                                                                                                                                    $kegiatans[] = [
                                                                                                                                                                                                                        'id' => $kegiatan->id,
                                                                                                                                                                                                                        'kode' => $kegiatan->kode,
                                                                                                                                                                                                                        'deskripsi'  => $kegiatan->deskripsi,
                                                                                                                                                                                                                        'pagu' => $get_renstra_kegiatan->pagu,
                                                                                                                                                                                                                        'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                                                                                                                                                                                                                    ];
                                                                                                                                                                                                                }
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $d = 1;
                                                                                                                                                                                                            foreach ($kegiatans as $kegiatan) {
                                                                                                                                                                                                                $html .= '<tr>
                                                                                                                                                                                                                    <td>'.$d++.'</td>
                                                                                                                                                                                                                    <td>'.$kegiatan['deskripsi'].'</td>';
                                                                                                                                                                                                                    $cek_target_rp_pertahun_renstra_kegiatan = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                                                                                                                                                                                    ->where('tahun', $tahun_sekarang)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                                    if($cek_target_rp_pertahun_renstra_kegiatan)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        $get_target_rp_pertahun_renstra_kegiatans = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                                                                                                                                                                                                        ->where('tahun', $tahun_sekarang)
                                                                                                                                                                                                                                        ->get();
                                                                                                                                                                                                                        $kegiatan_target = [];
                                                                                                                                                                                                                        $kegiatan_rp = [];
                                                                                                                                                                                                                        foreach ($get_target_rp_pertahun_renstra_kegiatans as $get_target_rp_pertahun_renstra_kegiatan) {
                                                                                                                                                                                                                            $kegiatan_target[] = $get_target_rp_pertahun_renstra_kegiatan->target;
                                                                                                                                                                                                                            $kegiatan_rp[] = $get_target_rp_pertahun_renstra_kegiatan->rp;
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        $html .= '<td>'.array_sum($kegiatan_target).'</td>
                                                                                                                                                                                                                    <td>'.$cek_target_rp_pertahun_renstra_kegiatan->satuan.'</td>
                                                                                                                                                                                                                    <td>Rp. '.number_format(array_sum($kegiatan_rp), 2).'</td>';
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        $html .= '<td></td>
                                                                                                                                                                                                                    <td></td>
                                                                                                                                                                                                                    <td></td>';
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                $get_opd_renstra_kegiatans = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])->get();
                                                                                                                                                                                                                $html .= '<td><ul>';
                                                                                                                                                                                                                foreach ($get_opd_renstra_kegiatans as $get_opd_renstra_kegiatan) {
                                                                                                                                                                                                                    $html .= '<li>'.$get_opd_renstra_kegiatan->opd->nama.'</li>';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                $html .='</ul></td>';
                                                                                                                                                                                                                $html .= '<td>Rp. '.number_format($kegiatan['pagu']).'</td>';
                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-renstra-kegiatan" data-renstra-kegiatan-id="'.$kegiatan['renstra_kegiatan_id'].'" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                                                                                                                </td>';
                                                                                                                                                                                                                $html .= '</tr>';
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

        $get_pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::whereHas('program_rpjmd', function($q) use ($id){
            $q->whereHas('renstra_kegiatan', function($q) use ($id){
                $q->where('id', $id);
            });
        })->get();

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

        $first_pivot_sasaran_indikator_program_rpjmd = PivotSasaranIndikatorProgramRpjmd::whereHas('program_rpjmd', function($q) use ($id){
            $q->whereHas('renstra_kegiatan', function($q) use ($id){
                $q->where('id', $id);
            });
        })->first();
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
        $get_program_rpjmd = ProgramRpjmd::whereHas('renstra_kegiatan', function($q) use ($id){
            $q->where('id', $id);
        })->first();
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

        //Kegiatan
        $get_renstra_kegiatan = RenstraKegiatan::find($id);
        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                    ->orderBy('tahun_perubahan', 'desc')->latest()->first();
        if($cek_perubahan_kegiatan)
        {
            $kegiatan = $cek_perubahan_kegiatan->deskripsi;
            $kegiatan_kode = $cek_perubahan_kegiatan->kode;
        } else {
            $kegiatan = $get_renstra_kegiatan->kegiatan->deskripsi;
            $kegiatan_kode = $get_renstra_kegiatan->kegiatan->kode;
        }

        // Opd
        $get_opds = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $id)->whereHas('opd', function($q){
            $q->where('id', Auth::user()->opd->opd_id);
        })->get();
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
                                                $get_target_rp_pertahun = TargetRpPertahunRenstraKegiatan::where('tahun', $tahun)
                                                                            ->where('opd_id', $get_opd->opd_id)
                                                                            ->where('renstra_kegiatan_id', $id)
                                                                            ->first();
                                                                            if($get_target_rp_pertahun)
                                                                            {
                                                                                $target_rp_pertahun .= '<tr class="tr-target-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$id.'">';
                                                                                $target_rp_pertahun .= '<td><span class="span-target '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$id.'">'.$get_target_rp_pertahun->target.'</span></td>';
                                                                                $target_rp_pertahun .= '<td><span class="span-satuan '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$id.'">'. $get_target_rp_pertahun->satuan.'</span></td>';
                                                                                $target_rp_pertahun .= '<td><span class="span-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$id.'">'. $get_target_rp_pertahun->rp.'</span></td>';
                                                                                $target_rp_pertahun .= '<td>'.$tahun.'</td>';
                                                                                $target_rp_pertahun .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-target-rp-pertahun-renstra-kegiatan" type="button" data-opd-id="'.$get_opd->opd_id.'" data-tahun="'.$tahun.'" data-renstra-kegiatan-id="'.$id.'" data-target-rp-pertahun-renstra-kegiatan-id="'.$get_target_rp_pertahun->id.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                            } else {
                                                                                $target_rp_pertahun .= '<tr>';
                                                                                $target_rp_pertahun .= '<td><input type="number" class="form-control add-target '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$id.'"></td>';
                                                                                $target_rp_pertahun .= '<td><input type="text" class="form-control add-satuan '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$id.'"></td>';
                                                                                $target_rp_pertahun .= '<td><input type="text" class="form-control add-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$id.'"></td>';
                                                                                $target_rp_pertahun .= '<td>'.$tahun.'</td>';
                                                                                $target_rp_pertahun .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-target-rp-pertahun-renstra-kegiatan" type="button" data-opd-id="'.$get_opd->opd_id.'" data-tahun="'.$tahun.'" data-renstra-kegiatan-id="'.$id.'">
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
            'kegiatan' => $kegiatan,
            'kegiatan_kode' => $kegiatan_kode,
            'target_rp_pertahun' => $target_rp_pertahun
        ];

        return response()->json(['result' => $array]);
    }

    public function store_target_rp_pertahun(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'opd_id' => 'required',
            'renstra_kegiatan_id' => 'required',
            'target' => 'required',
            'satuan' => 'required',
            'rp' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        if($request->target_rp_pertahun_renstra_kegiatan_id)
        {
            $target_rp_pertahun_renstra_kegiatan = TargetRpPertahunRenstraKegiatan::find($request->target_rp_pertahun_renstra_kegiatan_id);
        } else {
            $target_rp_pertahun_renstra_kegiatan = new TargetRpPertahunRenstraKegiatan;
        }
        $target_rp_pertahun_renstra_kegiatan->renstra_kegiatan_id = $request->renstra_kegiatan_id;
        $target_rp_pertahun_renstra_kegiatan->target = $request->target;
        $target_rp_pertahun_renstra_kegiatan->satuan = $request->satuan;
        $target_rp_pertahun_renstra_kegiatan->rp = $request->rp;
        $target_rp_pertahun_renstra_kegiatan->tahun = $request->tahun;
        $target_rp_pertahun_renstra_kegiatan->opd_id = $request->opd_id;
        $target_rp_pertahun_renstra_kegiatan->save();

        // Tahun
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        // Opd
        $get_opds = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $request->renstra_kegiatan_id)->whereHas('opd', function($q){
            $q->where('id', Auth::user()->opd->opd_id);
        })->get();
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
                                                $get_target_rp_pertahun = TargetRpPertahunRenstraKegiatan::where('tahun', $tahun)
                                                                            ->where('opd_id', $get_opd->opd_id)
                                                                            ->where('renstra_kegiatan_id', $request->renstra_kegiatan_id)
                                                                            ->first();
                                                                            if($get_target_rp_pertahun)
                                                                            {
                                                                                $target_rp_pertahun .= '<tr class="tr-target-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$request->renstra_kegiatan_id.'">';
                                                                                $target_rp_pertahun .= '<td><span class="span-target '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$request->renstra_kegiatan_id.'">'.$get_target_rp_pertahun->target.'</span></td>';
                                                                                $target_rp_pertahun .= '<td><span class="span-satuan '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$request->renstra_kegiatan_id.'">'. $get_target_rp_pertahun->satuan.'</span></td>';
                                                                                $target_rp_pertahun .= '<td><span class="span-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$request->renstra_kegiatan_id.'">'. $get_target_rp_pertahun->rp.'</span></td>';
                                                                                $target_rp_pertahun .= '<td>'.$tahun.'</td>';
                                                                                $target_rp_pertahun .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-target-rp-pertahun-renstra-kegiatan" type="button" data-opd-id="'.$get_opd->opd_id.'" data-tahun="'.$tahun.'" data-renstra-kegiatan-id="'.$request->renstra_kegiatan_id.'" data-target-rp-pertahun-renstra-kegiatan-id="'.$get_target_rp_pertahun->id.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                            } else {
                                                                                $target_rp_pertahun .= '<tr>';
                                                                                $target_rp_pertahun .= '<td><input type="number" class="form-control add-target '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$request->renstra_kegiatan_id.'"></td>';
                                                                                $target_rp_pertahun .= '<td><input type="text" class="form-control add-satuan '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$request->renstra_kegiatan_id.'"></td>';
                                                                                $target_rp_pertahun .= '<td><input type="text" class="form-control add-rp '.$tahun.' data-opd-'.$get_opd->opd_id.' data-renstra-kegiatan-'.$request->renstra_kegiatan_id.'"></td>';
                                                                                $target_rp_pertahun .= '<td>'.$tahun.'</td>';
                                                                                $target_rp_pertahun .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-target-rp-pertahun-renstra-kegiatan" type="button" data-opd-id="'.$get_opd->opd_id.'" data-tahun="'.$tahun.'" data-renstra-kegiatan-id="'.$request->renstra_kegiatan_id.'">
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

    public function get_filter_kegiatan(Request $request)
    {
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
                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraKegiatan" checked>
                            <label class="form-check-label" for="onOffTaggingRenstraKegiatan">On / Off Tagging</label>
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
                                        <span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse" id="program_visi'.$visi['id'].'">
                                            <table class="table table-condensed table-striped">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id']);
                                                    if($request->visi == 'aman')
                                                    {
                                                        $get_misis = $get_misis->where(function($q){
                                                            $q->where('kode', 1)->orWhere('kode', 2);
                                                        });
                                                    }
                                                    if($request->visi == 'mandiri')
                                                    {
                                                        $get_misis = $get_misis->where('kode', 3);
                                                    }
                                                    if($request->visi == 'sejahtera')
                                                    {
                                                        $get_misis = $get_misis->where('kode', 4);
                                                    }
                                                    if($request->visi == 'berahlak')
                                                    {
                                                        $get_misis = $get_misis->where('kode', 5);
                                                    }
                                                    if($request->misi)
                                                    {
                                                        $get_misis = $get_misis->where('id', $request->misi);
                                                    }
                                                    $get_misis = $get_misis->get();
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
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                        }
                                                                        if($a == 3)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                        }
                                                                        if($a == 4)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                        }
                                                                        if($a == 5)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                        }
                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse" id="program_misi'.$misi['id'].'">
                                                                            <table class="table table-condensed table-striped">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id']);
                                                                                    if($request->tujuan)
                                                                                    {
                                                                                        $get_tujuans = $get_tujuans->where('id', $request->tujuan);
                                                                                    }
                                                                                    $get_tujuans = $get_tujuans->get();
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
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                        }
                                                                                                        if($a == 3)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                        }
                                                                                                        if($a == 4)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                        }
                                                                                                        if($a == 5)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                        }
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse" id="program_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id']);
                                                                                                                    if($request->sasaran)
                                                                                                                    {
                                                                                                                        $get_sasarans = $get_sasarans->where('id', $request->sasaran);
                                                                                                                    }
                                                                                                                    $get_sasarans = $get_sasarans->get();
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
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 3)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 4)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 5)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                                                        }
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse" id="program_sasaran_indikator'.$sasaran['id'].'">
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
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 3)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 4)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 5)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
                                                                                                                                                                        <div class="collapse" id="program_rpjmd'.$sasaran_indikator['id'].'">
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
                                                                                                                                                                                    });
                                                                                                                                                                                    if($request->program)
                                                                                                                                                                                    {
                                                                                                                                                                                        $get_program_rpjmds = $get_program_rpjmds->where('id', $request->program);
                                                                                                                                                                                    }
                                                                                                                                                                                    $get_program_rpjmds = $get_program_rpjmds->get();
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
                                                                                                                                                                                                'program_id' => $get_program_rpjmd->program_id,
                                                                                                                                                                                            ];
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $program = Program::find($get_program_rpjmd->program_id);
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu,
                                                                                                                                                                                                'program_id' => $get_program_rpjmd->program_id,
                                                                                                                                                                                            ];
                                                                                                                                                                                        }
                                                                                                                                                                                    }
                                                                                                                                                                                    $c = 1;
                                                                                                                                                                                    foreach ($programs as $program) {
                                                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">'.$c++.'</td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">
                                                                                                                                                                                                    '.$program['deskripsi'];
                                                                                                                                                                                                    if($program['status_program'] == "Program Prioritas")
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= ' <i title="Program Prioritas" class="fas fa-star text-primary"></i>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= ' <br> ';
                                                                                                                                                                                                    if($a == 1 || $a == 2)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Aman]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 3)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 4)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if($a == 5)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                                                    <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">'.$get_target_rp->satuan.'</td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">Rp. '.number_format(array_sum($program_rp), 2).'</td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle"></td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle"></td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle"></td>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $html .= '<td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">';
                                                                                                                                                                                                $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $program['id'])->get();
                                                                                                                                                                                                $html .= '<ul>';
                                                                                                                                                                                                    foreach ($get_opds as $get_opd) {
                                                                                                                                                                                                        $html .= '<li>'.$get_opd->opd->nama.'</li>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                $html.='</ul>';
                                                                                                                                                                                                $html.= '</td>
                                                                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_renstra'.$program['id'].'" class="accordion-toggle">
                                                                                                                                                                                                    Rp. '.number_format($program['pagu'], 2).'
                                                                                                                                                                                                </td>
                                                                                                                                                                                                <td>
                                                                                                                                                                                                    <button class="btn btn-primary waves-effect waves-light renstra_kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditRenstraKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['program_id'].'" data-program-rpjmd-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                                                                                                                </td>
                                                                                                                                                                                            </tr>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <td colspan="8" class="hiddenRow">
                                                                                                                                                                                                    <div class="collapse" id="kegiatan_renstra'.$program['id'].'">
                                                                                                                                                                                                        <table class="table table-condensed table-striped">
                                                                                                                                                                                                            <thead>
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <th width="5%"><strong>No</strong></th>
                                                                                                                                                                                                                    <th width="35%"><strong>Kegiatan</strong></th>
                                                                                                                                                                                                                    <th width="5%"><strong>Target</strong></th>
                                                                                                                                                                                                                    <th width="5%"><strong>Satuan</strong></th>
                                                                                                                                                                                                                    <th width="10%"><strong>Rp</strong></th>
                                                                                                                                                                                                                    <th width="20%"><strong>OPD</strong></th>
                                                                                                                                                                                                                    <th width="10%"><strong>Pagu</strong></th>
                                                                                                                                                                                                                    <th width="10%"><strong>Aksi</strong></th>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                            </thead>
                                                                                                                                                                                                            <tbody>';
                                                                                                                                                                                                            $get_renstra_kegiatans = RenstraKegiatan::where('program_rpjmd_id', $program['id']);
                                                                                                                                                                                                            if($request->kegiatan)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $get_renstra_kegiatans = $get_renstra_kegiatans->where('kegiatan_id', $request->kegiatan);
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $get_renstra_kegiatans = $get_renstra_kegiatans->get();
                                                                                                                                                                                                            $kegiatans = [];
                                                                                                                                                                                                            foreach ($get_renstra_kegiatans as $get_renstra_kegiatan) {
                                                                                                                                                                                                                $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_renstra_kegiatan->kegiatan_id)
                                                                                                                                                                                                                                            ->orderBy('tahun_perubahan', 'desc')
                                                                                                                                                                                                                                            ->latest()->first();
                                                                                                                                                                                                                if($cek_perubahan_kegiatan)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $kegiatans[] = [
                                                                                                                                                                                                                        'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                                                                                                                        'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                                                                                                                        'deskripsi'  => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                                                                                                                        'pagu' => $get_renstra_kegiatan->pagu,
                                                                                                                                                                                                                        'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                                                                                                                                                                                                                    ];
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    $kegiatan = Kegiatan::find($get_renstra_kegiatan->kegiatan_id);
                                                                                                                                                                                                                    $kegiatans[] = [
                                                                                                                                                                                                                        'id' => $kegiatan->id,
                                                                                                                                                                                                                        'kode' => $kegiatan->kode,
                                                                                                                                                                                                                        'deskripsi'  => $kegiatan->deskripsi,
                                                                                                                                                                                                                        'pagu' => $get_renstra_kegiatan->pagu,
                                                                                                                                                                                                                        'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                                                                                                                                                                                                                    ];
                                                                                                                                                                                                                }
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $d = 1;
                                                                                                                                                                                                            foreach ($kegiatans as $kegiatan) {
                                                                                                                                                                                                                $html .= '<tr>
                                                                                                                                                                                                                    <td>'.$d++.'</td>
                                                                                                                                                                                                                    <td>'.$kegiatan['deskripsi'].'</td>';
                                                                                                                                                                                                                    $cek_target_rp_pertahun_renstra_kegiatan = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                                                                                                                                                                                    ->where('tahun', $tahun_sekarang)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                                    if($cek_target_rp_pertahun_renstra_kegiatan)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        $get_target_rp_pertahun_renstra_kegiatans = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                                                                                                                                                                                                        ->where('tahun', $tahun_sekarang)
                                                                                                                                                                                                                                        ->get();
                                                                                                                                                                                                                        $kegiatan_target = [];
                                                                                                                                                                                                                        $kegiatan_rp = [];
                                                                                                                                                                                                                        foreach ($get_target_rp_pertahun_renstra_kegiatans as $get_target_rp_pertahun_renstra_kegiatan) {
                                                                                                                                                                                                                            $kegiatan_target[] = $get_target_rp_pertahun_renstra_kegiatan->target;
                                                                                                                                                                                                                            $kegiatan_rp[] = $get_target_rp_pertahun_renstra_kegiatan->rp;
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        $html .= '<td>'.array_sum($kegiatan_target).'</td>
                                                                                                                                                                                                                    <td>'.$cek_target_rp_pertahun_renstra_kegiatan->satuan.'</td>
                                                                                                                                                                                                                    <td>Rp. '.number_format(array_sum($kegiatan_rp), 2).'</td>';
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        $html .= '<td></td>
                                                                                                                                                                                                                    <td></td>
                                                                                                                                                                                                                    <td></td>';
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                $get_opd_renstra_kegiatans = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])->get();
                                                                                                                                                                                                                $html .= '<td><ul>';
                                                                                                                                                                                                                foreach ($get_opd_renstra_kegiatans as $get_opd_renstra_kegiatan) {
                                                                                                                                                                                                                    $html .= '<li>'.$get_opd_renstra_kegiatan->opd->nama.'</li>';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                $html .='</ul></td>';
                                                                                                                                                                                                                $html .= '<td>Rp. '.number_format($kegiatan['pagu']).'</td>';
                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-renstra-kegiatan" data-renstra-kegiatan-id="'.$kegiatan['renstra_kegiatan_id'].'" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                                                                                                                </td>';
                                                                                                                                                                                                                $html .= '</tr>';
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
        return response()->json(['html' => $html]);
    }
}
