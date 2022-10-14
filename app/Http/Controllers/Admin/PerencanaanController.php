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
        return view('admin.perencanaan.index', [
            'tahuns' => $tahuns
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
}
