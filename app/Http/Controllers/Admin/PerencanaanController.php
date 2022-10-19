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
        $html = '<div class="data-table-rows slim" id="misi_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="65%">Visi</th>
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
                                                            ->orderBy('tahun_perubahan', 'desc')
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
                                                <span class="badge bg-primary text-uppercase">Visi</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary waves-effect waves-light mr-2 misi_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditMisiModal" title="Tambah Data Misi" data-visi-id="'.$visi['id'].'"><i class="fas fa-plus"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="hiddenRow">
                                                <div class="accordian-body collapse" id="misi_visi'.$visi['id'].'">
                                                    <table class="table table-striped">
                                                        <tbody>';
                                                        foreach ($misis as $misi) {
                                                            $html .= '<tr>
                                                                        <td width="15%">'.$misi['kode'].'</td>
                                                                        <td width="50%">
                                                                            '.$misi['deskripsi'].'
                                                                            <br>
                                                                            <span class="badge bg-primary text-uppercase">Visi</span>
                                                                            <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                        </td>
                                                                        <td width="15%">'.$misi['tahun_perubahan'].'</td>
                                                                        <td width="20%">
                                                                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-misi" data-misi-id="'.$misi['id'].'" type="button" title="Detail Misi"><i class="fas fa-eye"></i></button>
                                                                            <button class="btn btn-icon btn-warning waves-effect waves-light edit-misi" data-misi-id="'.$misi['id'].'" data-visi-id="'.$visi['id'].'" type="button" title="Edit Misi"><i class="fas fa-edit"></i></button>
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

        return response()->json(['html' => $html]);
    }

    public function get_tujuan()
    {
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

        $html = '<div class="data-table-rows slim" id="tujuan_div_table">
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
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="accordian-body collapse" id="tujuan_visi'.$visi['id'].'">
                                            <table class="table table-striped">
                                                <tbody>';
                                                $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                $misis = [];
                                                foreach ($get_misis as $get_misi) {
                                                    $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                                                            ->orderBy('tahun_perubahan', 'desc')
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
                                                foreach ($misis as $misi) {
                                                    $html .= '<tr>
                                                        <td width="15%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                        <td width="50%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">
                                                            '.$misi['deskripsi'].'
                                                            <br>
                                                            <span class="badge bg-primary text-uppercase">Visi</span>
                                                            <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                        </td>
                                                        <td width="15%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">2020</td>
                                                        <td>
                                                            <button class="btn btn-primary waves-effect waves-light mr-2 tujuan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditTujuanModal" title="Tambah Data Misi" data-misi-id="'.$misi['id'].'"><i class="fas fa-plus"></i></button>
                                                            <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_tujuan.xlsx').'" title="Download Template Import Data Tujuan"><i class="fas fa-file-excel"></i></a>
                                                            <button class="btn btn-info waves-effect waves-light tujuan_btn_impor_template" title="Import Data Tujuan" type="button" data-misi-id="'.$misi['id'].'"><i class="fas fa-file-import"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="hiddenRow">
                                                            <div class="accordian-body collapse" id="tujuan_misi'.$misi['id'].'">
                                                                <table class="table table-striped">
                                                                    <tbody>';
                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                    $tujuans = [];
                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                                                                                    ->orderBy('tahun_perubahan', 'desc')
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
                                                                            <td width="15%">'.$tujuan['kode'].'</td>
                                                                            <td width="50%">
                                                                                '.$tujuan['deskripsi'].'
                                                                                <br>
                                                                                <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                            </td>
                                                                            <td width="15%">'.$tujuan['tahun_perubahan'].'</td>
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

        $html = '<div class="data-table-rows slim" id="sasaran_div_table">
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
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="accordian-body collapse" id="sasaran_visi'.$visi['id'].'">
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
                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                                    <td width="70%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">
                                                                        '.$misi['deskripsi'].'
                                                                        <br>
                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                    </td>
                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['tahun_perubahan'].'</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="accordian-body collapse" id="sasaran_misi'.$misi['id'].'">
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="15%">'.$tujuan['kode'].'</td>
                                                                                                    <td width="50%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                                                                                                        '.$tujuan['deskripsi'].'
                                                                                                        <br>
                                                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                        <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                                                    </td>
                                                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$tujuan['tahun_perubahan'].'</td>
                                                                                                    <td>
                                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSasaranModal" title="Tambah Data Sasaran" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                        <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_sasaran.xlsx').'" title="Download Template Import Data Sasaran"><i class="fas fa-file-excel"></i></a>
                                                                                                        <button class="btn btn-info waves-effect waves-light sasaran_btn_impor_template" title="Import Data Sasaran" type="button" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-file-import"></i></button>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="accordian-body collapse" id="sasaran_tujuan'.$tujuan['id'].'">
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
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="15%">'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="50%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>
                                                                                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase">'.$sasaran['kode'].' Sasaran</span>
                                                                                                                                    </td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="15%">'.$sasaran['tahun_perubahan'].'</td>
                                                                                                                                    <td width="20%">
                                                                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_indikator_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSasaranIndikatorModal" title="Tambah Data Sasaran Indikator" data-sasaran-id="'.$sasaran['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="accordian-body collapse" id="sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-striped">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="50%"><strong>Sasaran Indikator</strong></th>
                                                                                                                                                        <th width="15%"><strong>Target</strong></th>
                                                                                                                                                        <th width="15%"><strong>Satuan</strong></th>
                                                                                                                                                        <th width="20%"><strong>Aksi</strong></th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                    $sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                    foreach ($sasaran_indikators as $sasaran_indikator) {
                                                                                                                                                        $html .= '<tr>
                                                                                                                                                                    <td>
                                                                                                                                                                        '.$sasaran_indikator['indikator'].'
                                                                                                                                                                        <br>
                                                                                                                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                                                                                        <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                                                                                                                        <span class="badge bg-danger text-uppercase">'.$sasaran['kode'].' Sasaran</span>
                                                                                                                                                                        <span class="badge bg-info text-uppercase">Sasaran Indikator</span>
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
                                                                                                                                                                                        <th width="40%"><strong>Program RPJMD</strong></th>
                                                                                                                                                                                        <th width="15%"><strong>Status Program</strong></th>
                                                                                                                                                                                        <th width="15%"><strong>Pagu</strong></th>
                                                                                                                                                                                        <th width="20%"><strong>OPD</strong></th>
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
}
