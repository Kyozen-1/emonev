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
use App\Models\PivotPerubahanKegiatan;
use App\Models\Kegiatan;
use App\Models\PivotOpdRentraKegiatan;

class PerencanaanController extends Controller
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
        $opds = MasterOpd::pluck('nama', 'id');
        return view('admin.perencanaan.index', [
            'tahuns' => $tahuns,
            'urusans' => $urusans,
            'misis' => $misis,
            'opds' => $opds
        ]);
    }

    public function get_misi()
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
                            <input class="form-check-input" type="checkbox" id="onOffTaggingMisi" checked>
                            <label class="form-check-label" for="onOffTaggingMisi">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim" id="misi_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="75%">Deskripsi</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                $misis = [];
                                foreach($get_misis as $get_misi)
                                {
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
                                $html .= '<tr>
                                            <td data-bs-toggle="collapse" data-bs-target="#misi_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                            <td data-bs-toggle="collapse" data-bs-target="#misi_visi'.$visi['id'].'" class="accordion-toggle">
                                                '.$visi['deskripsi'].'
                                                <br>
                                                <span class="badge bg-primary text-uppercase misi-tagging">Visi</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary waves-effect waves-light mr-2 misi_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditMisiModal" title="Tambah Data Misi" data-visi-id="'.$visi['id'].'"><i class="fas fa-plus"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="hiddenRow">
                                                <div class="collapse" id="misi_visi'.$visi['id'].'">
                                                    <table class="table table-striped">
                                                        <tbody>';
                                                        $a = 1;
                                                        foreach ($misis as $misi) {
                                                            $html .= '<tr>
                                                                        <td width="5%">'.$misi['kode'].'</td>
                                                                        <td width="75%">
                                                                            '.$misi['deskripsi'].'
                                                                            <br>';
                                                                            if($a == 1 || $a == 2)
                                                                            {
                                                                                $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Aman]</span>';
                                                                            }
                                                                            if($a == 3)
                                                                            {
                                                                                $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Mandiri]</span>';
                                                                            }
                                                                            if($a == 4)
                                                                            {
                                                                                $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Sejahtera]</span>';
                                                                            }
                                                                            if($a == 5)
                                                                            {
                                                                                $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Berahlak]</span>';
                                                                            }
                                                                            $html .= ' <span class="badge bg-warning text-uppercase misi-tagging">Misi '.$misi['kode'].'</span>
                                                                        </td>
                                                                        <td width="20%">
                                                                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-misi" data-misi-id="'.$misi['id'].'" type="button" title="Detail Misi"><i class="fas fa-eye"></i></button>
                                                                            <button class="btn btn-icon btn-warning waves-effect waves-light edit-misi" data-misi-id="'.$misi['id'].'" data-visi-id="'.$visi['id'].'" type="button" title="Edit Misi"><i class="fas fa-edit"></i></button>
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
                                                        <td width="75%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">
                                                            '.$misi['deskripsi'].'
                                                            <br>';
                                                            if($a == 1 || $a == 2)
                                                            {
                                                                $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi [Aman]</span>';
                                                            }
                                                            if($a == 3)
                                                            {
                                                                $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi [Mandiri]</span>';
                                                            }
                                                            if($a == 4)
                                                            {
                                                                $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi [Sejahtera]</span>';
                                                            }
                                                            if($a == 5)
                                                            {
                                                                $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi [Berahlak]</span>';
                                                            }
                                                            $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].' </span>
                                                        </td>
                                                        <td width="20%">
                                                            <button class="btn btn-primary waves-effect waves-light mr-2 tujuan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditTujuanModal" title="Tambah Data Misi" data-misi-id="'.$misi['id'].'"><i class="fas fa-plus"></i></button>
                                                            <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_tujuan.xlsx').'" title="Download Template Import Data Tujuan"><i class="fas fa-file-excel"></i></a>
                                                            <button class="btn btn-info waves-effect waves-light tujuan_btn_impor_template" title="Import Data Tujuan" type="button" data-misi-id="'.$misi['id'].'"><i class="fas fa-file-import"></i></button>
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
                                                                            <td width="75%">
                                                                                '.$tujuan['deskripsi'].'
                                                                                <br>';
                                                                                if($a == 1 || $a == 2)
                                                                                {
                                                                                    $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi [Aman]</span>';
                                                                                }
                                                                                if($a == 3)
                                                                                {
                                                                                    $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi [Mandiri]</span>';
                                                                                }
                                                                                if($a == 4)
                                                                                {
                                                                                    $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi [Sejahtera]</span>';
                                                                                }
                                                                                if($a == 5)
                                                                                {
                                                                                    $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi [Berahlak]</span>';
                                                                                }
                                                                                $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].'</span>
                                                                                <span class="badge bg-secondary text-uppercase tujuan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                            </td>
                                                                            <td width="20%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
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
                            <input class="form-check-input" type="checkbox" id="onOffTaggingSasaran" checked>
                            <label class="form-check-label" for="onOffTaggingSasaran">On / Off Tagging</label>
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
                                        <span class="badge bg-primary text-uppercase sasaran-tagging">Visi</span>
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
                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Aman]</span>';
                                                                        }
                                                                        if($a == 3)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Mandiri]</span>';
                                                                        }
                                                                        if($a == 4)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Sejahtera]</span>';
                                                                        }
                                                                        if($a == 5)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Berahlak]</span>';
                                                                        }
                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">'.$misi['kode'].' Misi</span>
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
                                                                                                    <td width="75%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                                                                                                        '.$tujuan['deskripsi'].'
                                                                                                        <br>';
                                                                                                        if($a == 1 || $a == 2)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Aman]</span>';
                                                                                                        }
                                                                                                        if($a == 3)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Mandiri]</span>';
                                                                                                        }
                                                                                                        if($a == 4)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Sejahtera]</span>';
                                                                                                        }
                                                                                                        if($a == 5)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Berahlak]</span>';
                                                                                                        }
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                    <td width ="20%">
                                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSasaranModal" title="Tambah Data Sasaran" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                        <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_sasaran.xlsx').'" title="Download Template Import Data Sasaran"><i class="fas fa-file-excel"></i></a>
                                                                                                        <button class="btn btn-info waves-effect waves-light sasaran_btn_impor_template" title="Import Data Sasaran" type="button" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-file-import"></i></button>
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
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="70%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>';
                                                                                                                                        if($a == 1 || $a == 2)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Aman]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 3)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Mandiri]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 4)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Sejahtera]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 5)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Berahlak]</span>';
                                                                                                                                        }
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                    <td width="25%">
                                                                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_indikator_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSasaranIndikatorModal" title="Tambah Data Sasaran Indikator" data-sasaran-id="'.$sasaran['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse" id="sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="5%"><strong>No</strong></th>
                                                                                                                                                        <th width="50%"><strong>Sasaran Indikator</strong></th>
                                                                                                                                                        <th width="15%"><strong>Target</strong></th>
                                                                                                                                                        <th width="15%"><strong>Satuan</strong></th>
                                                                                                                                                        <th width="15%"><strong>Aksi</strong></th>
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
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Aman]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 3)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 4)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 5)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                                                    </td>
                                                                                                                                                                    <td width="15%">
                                                                                                                                                                        '.$sasaran_indikator['target'].'
                                                                                                                                                                    </td>
                                                                                                                                                                    <td width="15%">
                                                                                                                                                                        '.$sasaran_indikator['satuan'].'
                                                                                                                                                                    </td>
                                                                                                                                                                    <td width="20%">
                                                                                                                                                                        <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran-indikator" data-sasaran-indikator-id="'.$sasaran_indikator['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Edit Sasaran Indikator"><i class="fas fa-edit"></i></button>
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
        $get_kegiatans = RenstraKegiatan::where('program_rpjmd_id', $request->id)
                        ->get();
        $kegiatan = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->kegiatan_id)
                                        ->orderBy('tahun_perubahan', 'desc')
                                        ->latest()->first();
            if($cek_perubahan_kegiatan){
                $kegiatan[] = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                ];
            } else {
                $kegiatan = Kegiatan::find($get_kegiatan->kegiatan_id);
                $kegiatan[] = [
                    'id' => $kegiatan->kegiatan_id,
                    'kode' => $kegiatan->kode,
                    'deskripsi' => $kegiatan->deskripsi
                ];
            }
        }

        return response()->json($kegiatan);
    }

    public function filter_program(Request $request)
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
                                                    foreach ($misis as $misi) {
                                                        $html .= '<tr>
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle">
                                                                        '.$misi['deskripsi'].'
                                                                        <br>';
                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi '.$request->visi.'</span>';
                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
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
                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi '.$request->visi.'</span>';
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
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
                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi '.$request->visi.'</span>';
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi '.$request->visi.'</span>';
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
                                                                                                                                                                                                    $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi '.$request->visi.'</span>';
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

    public function filter_sasaran(Request $request)
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
                            <input class="form-check-input" type="checkbox" id="onOffTaggingSasaran" checked>
                            <label class="form-check-label" for="onOffTaggingSasaran">On / Off Tagging</label>
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
                                        <span class="badge bg-primary text-uppercase sasaran-tagging">Visi</span>
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
                                                                        $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">'.$misi['kode'].' Misi</span>
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
                                                                                                    <td width="75%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                                                                                                        '.$tujuan['deskripsi'].'
                                                                                                        <br>';
                                                                                                        $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                    <td width ="20%">
                                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSasaranModal" title="Tambah Data Sasaran" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                        <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_sasaran.xlsx').'" title="Download Template Import Data Sasaran"><i class="fas fa-file-excel"></i></a>
                                                                                                        <button class="btn btn-info waves-effect waves-light sasaran_btn_impor_template" title="Import Data Sasaran" type="button" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-file-import"></i></button>
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
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="70%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>';
                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                    <td width="25%">
                                                                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_indikator_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSasaranIndikatorModal" title="Tambah Data Sasaran Indikator" data-sasaran-id="'.$sasaran['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse" id="sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="5%"><strong>No</strong></th>
                                                                                                                                                        <th width="50%"><strong>Sasaran Indikator</strong></th>
                                                                                                                                                        <th width="15%"><strong>Target</strong></th>
                                                                                                                                                        <th width="15%"><strong>Satuan</strong></th>
                                                                                                                                                        <th width="15%"><strong>Aksi</strong></th>
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
                                                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                                                    </td>
                                                                                                                                                                    <td width="15%">
                                                                                                                                                                        '.$sasaran_indikator['target'].'
                                                                                                                                                                    </td>
                                                                                                                                                                    <td width="15%">
                                                                                                                                                                        '.$sasaran_indikator['satuan'].'
                                                                                                                                                                    </td>
                                                                                                                                                                    <td width="20%">
                                                                                                                                                                        <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran-indikator" data-sasaran-indikator-id="'.$sasaran_indikator['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Edit Sasaran Indikator"><i class="fas fa-edit"></i></button>
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

    public function filter_tujuan(Request $request)
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
                                                        <td width="75%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">
                                                            '.$misi['deskripsi'].'
                                                            <br>';
                                                            $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi '.$request->visi.'</span>';
                                                            $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].' </span>
                                                        </td>
                                                        <td width="20%">
                                                            <button class="btn btn-primary waves-effect waves-light mr-2 tujuan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditTujuanModal" title="Tambah Data Misi" data-misi-id="'.$misi['id'].'"><i class="fas fa-plus"></i></button>
                                                            <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_tujuan.xlsx').'" title="Download Template Import Data Tujuan"><i class="fas fa-file-excel"></i></a>
                                                            <button class="btn btn-info waves-effect waves-light tujuan_btn_impor_template" title="Import Data Tujuan" type="button" data-misi-id="'.$misi['id'].'"><i class="fas fa-file-import"></i></button>
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
                                                                            <td width="75%">
                                                                                '.$tujuan['deskripsi'].'
                                                                                <br>';
                                                                                $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi '.$request->visi.'</span>';
                                                                                $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].'</span>
                                                                                <span class="badge bg-secondary text-uppercase tujuan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                            </td>
                                                                            <td width="20%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
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

    public function filter_misi(Request $request)
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
                            <input class="form-check-input" type="checkbox" id="onOffTaggingMisi" checked>
                            <label class="form-check-label" for="onOffTaggingMisi">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim" id="misi_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="75%">Deskripsi</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
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
                                foreach($get_misis as $get_misi)
                                {
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
                                $html .= '<tr>
                                            <td data-bs-toggle="collapse" data-bs-target="#misi_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                            <td data-bs-toggle="collapse" data-bs-target="#misi_visi'.$visi['id'].'" class="accordion-toggle">
                                                '.$visi['deskripsi'].'
                                                <br>
                                                <span class="badge bg-primary text-uppercase misi-tagging">Visi</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary waves-effect waves-light mr-2 misi_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditMisiModal" title="Tambah Data Misi" data-visi-id="'.$visi['id'].'"><i class="fas fa-plus"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="hiddenRow">
                                                <div class="collapse" id="misi_visi'.$visi['id'].'">
                                                    <table class="table table-striped">
                                                        <tbody>';
                                                        $a = 1;
                                                        foreach ($misis as $misi) {
                                                            $html .= '<tr>
                                                                        <td width="5%">'.$misi['kode'].'</td>
                                                                        <td width="75%">
                                                                            '.$misi['deskripsi'].'
                                                                            <br>';
                                                                            $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi '.$request->visi.'</span>';
                                                                            $html .= ' <span class="badge bg-warning text-uppercase misi-tagging">Misi '.$misi['kode'].'</span>
                                                                        </td>
                                                                        <td width="20%">
                                                                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-misi" data-misi-id="'.$misi['id'].'" type="button" title="Detail Misi"><i class="fas fa-eye"></i></button>
                                                                            <button class="btn btn-icon btn-warning waves-effect waves-light edit-misi" data-misi-id="'.$misi['id'].'" data-visi-id="'.$visi['id'].'" type="button" title="Edit Misi"><i class="fas fa-edit"></i></button>
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

    public function renstra_get_tujuan(Request $request)
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
                                        <span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi</span>
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
                                                                $html .= '<span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi [Aman]</span>';
                                                            }
                                                            if($a == 3)
                                                            {
                                                                $html .= '<span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi [Mandiri]</span>';
                                                            }
                                                            if($a == 4)
                                                            {
                                                                $html .= '<span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi [Sejahtera]</span>';
                                                            }
                                                            if($a == 5)
                                                            {
                                                                $html .= '<span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi [Berahlak]</span>';
                                                            }
                                                            $html .= ' <span class="badge bg-warning text-uppercase tujuan-renstra-tagging">Misi '.$misi['kode'].' </span>
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
                                                                                    $html .= '<span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi [Aman]</span>';
                                                                                }
                                                                                if($a == 3)
                                                                                {
                                                                                    $html .= '<span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi [Mandiri]</span>';
                                                                                }
                                                                                if($a == 4)
                                                                                {
                                                                                    $html .= '<span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi [Sejahtera]</span>';
                                                                                }
                                                                                if($a == 5)
                                                                                {
                                                                                    $html .= '<span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi [Berahlak]</span>';
                                                                                }
                                                                                $html .= ' <span class="badge bg-warning text-uppercase tujuan-renstra-tagging">Misi '.$misi['kode'].'</span>
                                                                                <span class="badge bg-secondary text-uppercase tujuan-renstra-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
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

    public function renstra_filter_tujuan(Request $request)
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
                            <label class="form-check-label" for="onOffTaggingRenstraTujuan">On / Off Tagging</label>
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
                                        <span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi</span>
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
                                                            $html .= '<span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi '.$request->visi.'</span>';
                                                            $html .= ' <span class="badge bg-warning text-uppercase tujuan-renstra-tagging">Misi '.$misi['kode'].' </span>
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
                                                                                $html .= '<span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi '.$request->visi.'</span>';
                                                                                $html .= ' <span class="badge bg-warning text-uppercase tujuan-renstra-tagging">Misi '.$misi['kode'].'</span>
                                                                                <span class="badge bg-secondary text-uppercase tujuan-renstra-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
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

    public function renstra_get_sasaran()
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
                                        <span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi</span>
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
                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Aman]</span>';
                                                                        }
                                                                        if($a == 3)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Mandiri]</span>';
                                                                        }
                                                                        if($a == 4)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Sejahtera]</span>';
                                                                        }
                                                                        if($a == 5)
                                                                        {
                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Berahlak]</span>';
                                                                        }
                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-renstra-tagging">'.$misi['kode'].' Misi</span>
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
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Aman]</span>';
                                                                                                        }
                                                                                                        if($a == 3)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Mandiri]</span>';
                                                                                                        }
                                                                                                        if($a == 4)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Sejahtera]</span>';
                                                                                                        }
                                                                                                        if($a == 5)
                                                                                                        {
                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Berahlak]</span>';
                                                                                                        }
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-renstra-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase sasaran-renstra-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
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
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Aman]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 3)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Mandiri]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 4)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Sejahtera]</span>';
                                                                                                                                        }
                                                                                                                                        if($a == 5)
                                                                                                                                        {
                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Berahlak]</span>';
                                                                                                                                        }
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-renstra-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase sasaran-renstra-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase sasaran-renstra-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Aman]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 3)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Mandiri]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 4)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Sejahtera]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        if($a == 5)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi [Berahlak]</span>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-renstra-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase sasaran-renstra-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase sasaran-renstra-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
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

    public function renstra_filter_sasaran(Request $request)
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

    public function renstra_get_program()
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

    public function renstra_filter_program(Request $request)
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

    public function renstra_get_kegiatan()
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
                                                                                                                                                                                                                    <th width="75%"><strong>Kegiatan</strong></th>
                                                                                                                                                                                                                    <th width="20%"><strong>OPD</strong></th>
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
                                                                                                                                                                                                                        'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                                                                                                                                                                                                                    ];
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    $kegiatan = Kegiatan::find($get_renstra_kegiatan->kegiatan_id);
                                                                                                                                                                                                                    $kegiatans[] = [
                                                                                                                                                                                                                        'id' => $kegiatan->id,
                                                                                                                                                                                                                        'kode' => $kegiatan->kode,
                                                                                                                                                                                                                        'deskripsi'  => $kegiatan->deskripsi,
                                                                                                                                                                                                                        'renstra_kegiatan_id' => $get_renstra_kegiatan->id
                                                                                                                                                                                                                    ];
                                                                                                                                                                                                                }
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $d = 1;
                                                                                                                                                                                                            foreach ($kegiatans as $kegiatan) {
                                                                                                                                                                                                                $html .= '<tr>
                                                                                                                                                                                                                    <td>'.$d++.'</td>
                                                                                                                                                                                                                    <td>'.$kegiatan['deskripsi'].'</td>';

                                                                                                                                                                                                                $get_opd_renstra_kegiatans = PivotOpdRentraKegiatan::where('rentra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])->get();
                                                                                                                                                                                                                $html .= '<td><ul>';
                                                                                                                                                                                                                foreach ($get_opd_renstra_kegiatans as $get_opd_renstra_kegiatan) {
                                                                                                                                                                                                                    $html .= '<li>'.$get_opd_renstra_kegiatan->opd->nama.'</li>';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                $html .='</ul></td>';
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
