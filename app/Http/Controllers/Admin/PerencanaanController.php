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
        $get_urusans = Urusan::select('id', 'kode', 'deskripsi')->orderBy('kode','asc')->get();
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
            'tahun_awal' => $tahun_awal,
            'urusans' => $urusans,
            'misis' => $misis,
            'opds' => $opds
        ]);
    }

    public function get_misi()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)
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
        $html = '<div class="data-table-rows slim" id="misi_div_table">
                    <div class="table-responsive-sm">
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
                                $html .= '<tr style="background: #bbbbbb;">
                                            <td data-bs-toggle="collapse" data-bs-target="#misi_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                            <td data-bs-toggle="collapse" data-bs-target="#misi_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                                '.strtoupper($visi['deskripsi']).'
                                                <br>
                                                <span class="badge bg-primary text-uppercase misi-tagging">Visi</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary waves-effect waves-light mr-2 misi_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditMisiModal" title="Tambah Data Misi" data-visi-id="'.$visi['id'].'" data-tahun="'.$tahun_awal.'"><i class="fas fa-plus"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="hiddenRow">
                                                <div class="collapse show" id="misi_visi'.$visi['id'].'">
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
                                                                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-misi" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Detail Misi"><i class="fas fa-eye"></i></button>
                                                                            <button class="btn btn-icon btn-warning waves-effect waves-light edit-misi" data-misi-id="'.$misi['id'].'" data-tahun="semua" data-visi-id="'.$visi['id'].'" type="button" title="Edit Misi"><i class="fas fa-edit"></i></button>
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

    public function get_misi_tahun($tahun)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $tahun)
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
        $html = '<div class="data-table-rows slim" id="misi_div_table">
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
                                                            ->where('tahun_perubahan', $tahun)
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
                                $html .= '<tr style="background: #bbbbbb;">
                                            <td data-bs-toggle="collapse" data-bs-target="#misi_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                            <td data-bs-toggle="collapse" data-bs-target="#misi_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                                '.strtoupper($visi['deskripsi']).'
                                                <br>
                                                <span class="badge bg-primary text-uppercase misi-tagging">Visi</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary waves-effect waves-light mr-2 misi_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditMisiModal" title="Tambah Data Misi" data-visi-id="'.$visi['id'].'" data-tahun="'.$tahun.'"><i class="fas fa-plus"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="hiddenRow">
                                                <div class="collapse show" id="misi_visi'.$visi['id'].'">
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
                                                                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-misi" data-misi-id="'.$misi['id'].'" data-tahun="'.$tahun.'" type="button" title="Detail Misi"><i class="fas fa-eye"></i></button>
                                                                            <button class="btn btn-icon btn-warning waves-effect waves-light edit-misi" data-misi-id="'.$misi['id'].'" data-tahun="'.$tahun.'" data-visi-id="'.$visi['id'].'" type="button" title="Edit Misi"><i class="fas fa-edit"></i></button>
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
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->latest()->first();
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

        $html = '<div class="data-table-rows slim" id="tujuan_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                        foreach ($visis as $visi) {
                            $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase tujuan-tagging">Visi</span>
                                    </td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse show" id="tujuan_visi'.$visi['id'].'">
                                            <table class="table table-striped table-condesed">
                                                <tbody>';
                                                $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                $misis = [];
                                                foreach ($get_misis as $get_misi) {
                                                    $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
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
                                                    $html .= '<tr style="background: #c04141;">
                                                        <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle text-white">'.strtoupper($misi['kode']).'</td>
                                                        <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                            '.strtoupper($misi['deskripsi']).'
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
                                                        <td width="30%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">
                                                        </td>
                                                        <td width="20%">
                                                            <button class="btn btn-primary waves-effect waves-light mr-2 tujuan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditTujuanModal" title="Tambah Data Misi" data-misi-id="'.$misi['id'].'"><i class="fas fa-plus"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="hiddenRow">
                                                            <div class="collapse show" id="tujuan_misi'.$misi['id'].'">
                                                                <table class="table table-striped table-condesed">
                                                                    <tbody>';
                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->orderBy('kode', 'asc')->get();
                                                                    $tujuans = [];
                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
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
                                                                            <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                            <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
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
                                                                            </td>';
                                                                            $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                                                                            $html .= '<td width="28%"><table>
                                                                                <tbody>';
                                                                                    foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td width="75%">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                                                                            $html .= '<td width="25%">
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-id="'.$tujuan['id'].'" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                                                            </td>';
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                $html .= '</tbody>
                                                                            </table></td>';
                                                                            $html .= '<td width="22%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-danger waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-indikator-kinerja" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Tambah Tujuan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                                                                                <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Hapus Tujuan "><i class="fas fa-trash"></i></button>
                                                                            </td>
                                                                        </tr>';
                                                                        $html .= '<tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                                                                                    <table class="table table-striped table-condesed">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th width="5%">No</th>
                                                                                                <th width="30%">Indikator</th>
                                                                                                <th width="17%">Target Kinerja Awal</th>
                                                                                                <th width="12%">Target</th>
                                                                                                <th width="12%">Satuan</th>
                                                                                                <th width="12%">Tahun</th>
                                                                                                <th width="12%">Aksi</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                        $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                                                                                        $no_tujuan_indikator_kinerja = 1;
                                                                                        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                                                                            $html .= '<tr>';
                                                                                                $html .= '<td>'.$no_tujuan_indikator_kinerja++.'</td>';
                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                $c = 1;
                                                                                                foreach ($tahuns as $tahun) {
                                                                                                    $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                                                                                                        ->where('tahun', $tahun)->first();
                                                                                                    if($cek_tujuan_target_satuan_rp_realisasi)
                                                                                                    {
                                                                                                        if($c == 1)
                                                                                                        {
                                                                                                                $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                            </button>
                                                                                                                        </td>';
                                                                                                            $html .='</tr>';
                                                                                                        } else {
                                                                                                            $html .= '<tr>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                </button>
                                                                                                                            </td>';
                                                                                                            $html .='</tr>';
                                                                                                        }
                                                                                                        $c++;
                                                                                                    } else {
                                                                                                        if($c == 1)
                                                                                                        {
                                                                                                                $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                            </button>
                                                                                                                        </td>';
                                                                                                            $html .='</tr>';
                                                                                                        } else {
                                                                                                            $html .= '<tr>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                            </button>
                                                                                                                        </td>';
                                                                                                            $html .='</tr>';
                                                                                                        }
                                                                                                        $c++;
                                                                                                    }
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

    public function get_tujuan_tahun($tahun)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->latest()->first();
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

        $html = '<div class="data-table-rows slim" id="tujuan_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                        foreach ($visis as $visi) {
                            $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase tujuan-tagging">Visi</span>
                                    </td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse show" id="tujuan_visi'.$visi['id'].'">
                                            <table class="table table-striped table-condesed">
                                                <tbody>';
                                                $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                $misis = [];
                                                foreach ($get_misis as $get_misi) {
                                                    $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
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
                                                    $html .= '<tr style="background: #c04141;">
                                                        <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle text-white">'.strtoupper($misi['kode']).'</td>
                                                        <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                            '.strtoupper($misi['deskripsi']).'
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
                                                        <td width="30%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle"></td>
                                                        <td width="20%">
                                                            <button class="btn btn-primary waves-effect waves-light mr-2 tujuan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditTujuanModal" title="Tambah Data Misi" data-misi-id="'.$misi['id'].'"><i class="fas fa-plus"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="hiddenRow">
                                                            <div class="collapse show" id="tujuan_misi'.$misi['id'].'">
                                                                <table class="table table-striped table-condesed">
                                                                    <tbody>';
                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->orderBy('kode', 'asc')->get();
                                                                    $tujuans = [];
                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
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
                                                                            <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                            <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
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
                                                                            </td>';
                                                                            $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                                                                            $html .= '<td width="28%"><table>
                                                                                <tbody>';
                                                                                    foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td width="75%">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                                                                            $html .= '<td width="25%">
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-id="'.$tujuan['id'].'" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                                                            </td>';
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                $html .= '</tbody>
                                                                            </table></td>';
                                                                            $html .= '<td width="22%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-danger waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-indikator-kinerja" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Tambah Tujuan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                                                                                <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Hapus Tujuan "><i class="fas fa-trash"></i></button>
                                                                            </td>
                                                                        </tr>';
                                                                        $html .= '<tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                                                                                    <table class="table table-striped table-condesed">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th width="5%">No</th>
                                                                                                <th width="30%">Indikator</th>
                                                                                                <th width="17%">Target Kinerja Awal</th>
                                                                                                <th width="12%">Target</th>
                                                                                                <th width="12%">Satuan</th>
                                                                                                <th width="12%">Tahun</th>
                                                                                                <th width="12%">Aksi</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                        $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                                                                                        $no_tujuan_indikator_kinerja = 1;
                                                                                        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                                                                            $html .= '<tr>';
                                                                                                $html .= '<td>'.$no_tujuan_indikator_kinerja++.'</td>';
                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                $c = 1;
                                                                                                foreach ($tahuns as $tahun) {
                                                                                                    $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                                                                                                        ->where('tahun', $tahun)->first();
                                                                                                    if($cek_tujuan_target_satuan_rp_realisasi)
                                                                                                    {
                                                                                                        if($c == 1)
                                                                                                        {
                                                                                                                $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                            </button>
                                                                                                                        </td>';
                                                                                                            $html .='</tr>';
                                                                                                        } else {
                                                                                                            $html .= '<tr>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                </button>
                                                                                                                            </td>';
                                                                                                            $html .='</tr>';
                                                                                                        }
                                                                                                        $c++;
                                                                                                    } else {
                                                                                                        if($c == 1)
                                                                                                        {
                                                                                                                $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                            </button>
                                                                                                                        </td>';
                                                                                                            $html .='</tr>';
                                                                                                        } else {
                                                                                                            $html .= '<tr>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                            </button>
                                                                                                                        </td>';
                                                                                                            $html .='</tr>';
                                                                                                        }
                                                                                                        $c++;
                                                                                                    }
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
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->latest()->first();
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

        $html = '<div class="data-table-rows slim" id="sasaran_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase sasaran-tagging">Visi</span>
                                    </td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse show" id="sasaran_visi'.$visi['id'].'">
                                            <table class="table table-striped table-condensed">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                    $misis = [];
                                                    foreach ($get_misis as $get_misi) {
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->latest()->first();
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
                                                        $html .= '<tr style="background: #c04141;">
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                        '.strtoupper($misi['deskripsi']).'
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
                                                                    <td width="30%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle"></td>
                                                                    <td width="20%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse show" id="sasaran_misi'.$misi['id'].'">
                                                                            <table class="table table-striped table-condensed">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
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
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="30%"></td>
                                                                                                    <td width ="20%">
                                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSasaranModal" title="Tambah Data Sasaran" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse show" id="sasaran_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
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
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="45%">
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
                                                                                                                                    </td>';
                                                                                                                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                    // $html .= '<td width="30%"><ul>';
                                                                                                                                    //     foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                    //     {
                                                                                                                                    //         $html .= '<li class="mb-2">'.$sasaran_indikator_kinerja->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"></button></li>';
                                                                                                                                    //     }
                                                                                                                                    // $html .= '</ul></td>';
                                                                                                                                    $html .= '<td width="28%"><table>
                                                                                                                                                <tbody>';
                                                                                                                                                    foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                            $html .= '<td width="75%">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                            $html .= '<td width="25%">
                                                                                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                                                                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                                                                                                                            </td>';
                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                    }
                                                                                                                                                $html .= '</tbody>
                                                                                                                                            </table></td>';
                                                                                                                                    $html .='<td width="22%">
                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-misi-id="'.$misi['id'].'" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Tambah Sasaran Indikator Kinerja"><i class="fas fa-lock"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Hapus Sasaran "><i class="fas fa-trash"></i></button>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="5%">No</th>
                                                                                                                                                        <th width="30%">Indikator</th>
                                                                                                                                                        <th width="17%">Target Kinerja Awal</th>
                                                                                                                                                        <th width="12%">Target</th>
                                                                                                                                                        <th width="12%">Satuan</th>
                                                                                                                                                        <th width="12%">Tahun</th>
                                                                                                                                                        <th width="12%">Aksi</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                $no_sasaran_indikator_kinerja = 1;
                                                                                                                                                foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                        $html .= '<td>'.$no_sasaran_indikator_kinerja++.'</td>';
                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                        $b = 1;
                                                                                                                                                        foreach ($tahuns as $tahun) {
                                                                                                                                                            $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                                                                                            if($cek_sasaran_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                if($b == 1)
                                                                                                                                                                {
                                                                                                                                                                    $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                    </button>
                                                                                                                                                                                </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                        </button>
                                                                                                                                                                                    </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                }
                                                                                                                                                                $b++;
                                                                                                                                                            } else {
                                                                                                                                                                if($b == 1)
                                                                                                                                                                {
                                                                                                                                                                        $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                    </button>
                                                                                                                                                                                </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                    </button>
                                                                                                                                                                                </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                }
                                                                                                                                                                $b++;
                                                                                                                                                            }
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

    public function get_sasaran_tahun($tahun)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)
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

        $html = '<div class="data-table-rows slim" id="sasaran_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase sasaran-tagging">Visi</span>
                                    </td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse show" id="sasaran_visi'.$visi['id'].'">
                                            <table class="table table-striped table-condensed">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                    $misis = [];
                                                    foreach ($get_misis as $get_misi) {
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
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
                                                        $html .= '<tr style="background: #c04141;">
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">
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
                                                                    <td width="30%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle"></td>
                                                                    <td width="20%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse show" id="sasaran_misi'.$misi['id'].'">
                                                                            <table class="table table-striped table-condensed">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
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
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="30%"></td>
                                                                                                    <td width ="20%">
                                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSasaranModal" title="Tambah Data Sasaran" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse show" id="sasaran_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
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
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="45%">
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
                                                                                                                                    </td>';
                                                                                                                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                    // $html .= '<td width="30%"><ul>';
                                                                                                                                    //     foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                    //     {
                                                                                                                                    //         $html .= '<li class="mb-2">'.$sasaran_indikator_kinerja->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"></button></li>';
                                                                                                                                    //     }
                                                                                                                                    // $html .= '</ul></td>';
                                                                                                                                    $html .= '<td width="28%"><table>
                                                                                                                                                <tbody>';
                                                                                                                                                    foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                            $html .= '<td width="75%">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                            $html .= '<td width="25%">
                                                                                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                                                                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                                                                                                                            </td>';
                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                    }
                                                                                                                                                $html .= '</tbody>
                                                                                                                                            </table></td>';
                                                                                                                                    $html .='<td width="22%">
                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-misi-id="'.$misi['id'].'" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Tambah Sasaran Indikator Kinerja"><i class="fas fa-lock"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Hapus Sasaran "><i class="fas fa-trash"></i></button>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="5%">No</th>
                                                                                                                                                        <th width="30%">Indikator</th>
                                                                                                                                                        <th width="17%">Target Kinerja Awal</th>
                                                                                                                                                        <th width="12%">Target</th>
                                                                                                                                                        <th width="12%">Satuan</th>
                                                                                                                                                        <th width="12%">Tahun</th>
                                                                                                                                                        <th width="12%">Aksi</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                $no_sasaran_indikator_kinerja = 1;
                                                                                                                                                foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                        $html .= '<td>'.$no_sasaran_indikator_kinerja++.'</td>';
                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                        $b = 1;
                                                                                                                                                        foreach ($tahuns as $tahun) {
                                                                                                                                                            $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                                                                                            if($cek_sasaran_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                if($b == 1)
                                                                                                                                                                {
                                                                                                                                                                    $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                    </button>
                                                                                                                                                                                </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                        </button>
                                                                                                                                                                                    </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                }
                                                                                                                                                                $b++;
                                                                                                                                                            } else {
                                                                                                                                                                if($b == 1)
                                                                                                                                                                {
                                                                                                                                                                        $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                    </button>
                                                                                                                                                                                </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                    </button>
                                                                                                                                                                                </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                }
                                                                                                                                                                $b++;
                                                                                                                                                            }
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
        $tahun_sekarang = Carbon::now()->year;
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->latest()->first();
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

        $html = '<div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="50%"></th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase program-tagging">Visi</span>
                                    </td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse show" id="program_visi'.$visi['id'].'">
                                            <table class="table table-condensed table-striped">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                    $misis = [];
                                                    foreach ($get_misis as $get_misi) {
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
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
                                                        $html .= '<tr style="background: #c04141;">
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                        '.strtoupper($misi['deskripsi']).'
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
                                                                    <td width="50%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse show" id="program_misi'.$misi['id'].'">
                                                                            <table class="table table-condensed table-striped">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
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
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="50%"></td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-bordered">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)
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
                                                                                                                                    <td width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td width="45%">
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
                                                                                                                                    <td width="50%">';
                                                                                                                                        $html .= '<table class="table table-bordered">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="50%">Indikator Kinerja</th>
                                                                                                                                                        <th width="50%">Program RPJMD</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                    foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                            $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                            $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                                                                                                                                                            $a = 1;
                                                                                                                                                            foreach($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd)
                                                                                                                                                            {
                                                                                                                                                                if($a == 1)
                                                                                                                                                                {
                                                                                                                                                                        $get_perubahan_program = PivotPerubahanProgram::where('program_id', $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program_id)
                                                                                                                                                                                                    ->where('tahun_perubahan', $tahun_sekarang)->latest()->first();
                                                                                                                                                                        if($get_perubahan_program)
                                                                                                                                                                        {
                                                                                                                                                                            $program_deskripsi = $get_perubahan_program->deskripsi;
                                                                                                                                                                        } else {
                                                                                                                                                                            $program_deskripsi = $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi;
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<td>'.$program_deskripsi;
                                                                                                                                                                        if($pivot_sasaran_indikator_program_rpjmd->program_rpjmd->status_program == 'Prioritas')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program Prioritas"></i>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<button type="button" class="btn-close btn-hapus-pivot-sasaran-indikator-program-rpjmd"
                                                                                                                                                                        data-pivot-sasaran-indikator-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->id.'"
                                                                                                                                                                        data-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd_id.'"
                                                                                                                                                                        data-sasaran-indikator-kinerja-id="'.$pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_kinerja_id.'"></button></td>';
                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                } else{
                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $get_perubahan_program = PivotPerubahanProgram::where('program_id', $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program_id)
                                                                                                                                                                                                    ->where('tahun_perubahan', $tahun_sekarang)->latest()->first();
                                                                                                                                                                        if($get_perubahan_program)
                                                                                                                                                                        {
                                                                                                                                                                            $program_deskripsi = $get_perubahan_program->deskripsi;
                                                                                                                                                                        } else {
                                                                                                                                                                            $program_deskripsi = $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi;
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<td>'.$program_deskripsi;
                                                                                                                                                                        if($pivot_sasaran_indikator_program_rpjmd->program_rpjmd->status_program == 'Prioritas')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program Prioritas"></i>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<button type="button" class="btn-close btn-hapus-pivot-sasaran-indikator-program-rpjmd"
                                                                                                                                                                        data-pivot-sasaran-indikator-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->id.'"
                                                                                                                                                                        data-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd_id.'"
                                                                                                                                                                        data-sasaran-indikator-kinerja-id="'.$pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_kinerja_id.'"></button></td>';
                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                }
                                                                                                                                                                $a++;
                                                                                                                                                            }
                                                                                                                                                    }
                                                                                                                                                $html .= '</tbody>
                                                                                                                                        </table>';
                                                                                                                                    $html .= '</td>
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

    public function get_program_tahun($tahun)
    {
        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $tahun)
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

        $html = '<div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="50%"></th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase program-tagging">Visi</span>
                                    </td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse show" id="program_visi'.$visi['id'].'">
                                            <table class="table table-condensed table-striped">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                    $misis = [];
                                                    foreach ($get_misis as $get_misi) {
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->where('tahun_perubahan', $tahun)
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
                                                        $html .= '<tr style="background: #c04141;">
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                        '.strtoupper($misi['deskripsi']).'
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
                                                                    <td width="50%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse show" id="program_misi'.$misi['id'].'">
                                                                            <table class="table table-condensed table-striped">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan', $tahun)
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
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="50%"></td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-bordered">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $tahun)
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
                                                                                                                                    <td width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td width="45%">
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
                                                                                                                                    <td width="50%">';
                                                                                                                                        $html .= '<table class="table table-bordered">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="50%">Indikator Kinerja</th>
                                                                                                                                                        <th width="50%">Program RPJMD</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                    foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                            $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                            $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                                                                                                                                                            $a = 1;
                                                                                                                                                            foreach($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd)
                                                                                                                                                            {
                                                                                                                                                                if($a == 1)
                                                                                                                                                                {
                                                                                                                                                                        $get_perubahan_program = PivotPerubahanProgram::where('program_id', $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program_id)
                                                                                                                                                                                                    ->where('tahun_perubahan', $tahun)->latest()->first();
                                                                                                                                                                        if($get_perubahan_program)
                                                                                                                                                                        {
                                                                                                                                                                            $program_deskripsi = $get_perubahan_program->deskripsi;
                                                                                                                                                                        } else {
                                                                                                                                                                            $program_deskripsi = $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi;
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<td>'.$program_deskripsi;
                                                                                                                                                                        if($pivot_sasaran_indikator_program_rpjmd->program_rpjmd->status_program == 'Prioritas')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program Prioritas"></i>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<button type="button" class="btn-close btn-hapus-pivot-sasaran-indikator-program-rpjmd"
                                                                                                                                                                        data-pivot-sasaran-indikator-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->id.'"
                                                                                                                                                                        data-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd_id.'"
                                                                                                                                                                        data-sasaran-indikator-kinerja-id="'.$pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_kinerja_id.'"></button></td>';
                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                } else{
                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $get_perubahan_program = PivotPerubahanProgram::where('program_id', $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program_id)
                                                                                                                                                                                                    ->where('tahun_perubahan', $tahun)->latest()->first();
                                                                                                                                                                        if($get_perubahan_program)
                                                                                                                                                                        {
                                                                                                                                                                            $program_deskripsi = $get_perubahan_program->deskripsi;
                                                                                                                                                                        } else {
                                                                                                                                                                            $program_deskripsi = $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi;
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<td>'.$program_deskripsi;
                                                                                                                                                                        if($pivot_sasaran_indikator_program_rpjmd->program_rpjmd->status_program == 'Prioritas')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program Prioritas"></i>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<button type="button" class="btn-close btn-hapus-pivot-sasaran-indikator-program-rpjmd"
                                                                                                                                                                        data-pivot-sasaran-indikator-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->id.'"
                                                                                                                                                                        data-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd_id.'"
                                                                                                                                                                        data-sasaran-indikator-kinerja-id="'.$pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_kinerja_id.'"></button></td>';
                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                }
                                                                                                                                                                $a++;
                                                                                                                                                            }
                                                                                                                                                    }
                                                                                                                                                $html .= '</tbody>
                                                                                                                                        </table>';
                                                                                                                                    $html .= '</td>
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
            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($request) {
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
        $kegiatans = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->kegiatan_id)
                                        ->orderBy('tahun_perubahan', 'desc')
                                        ->latest()->first();
            if($cek_perubahan_kegiatan){
                $kegiatans[] = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                ];
            } else {
                $kegiatans[] = [
                    'id' => $get_kegiatan->kegiatan->id,
                    'kode' => $get_kegiatan->kegiatan->kode,
                    'deskripsi' => $get_kegiatan->kegiatan->deskripsi
                ];
            }
        }

        return response()->json($kegiatans);
    }

    public function filter_program(Request $request)
    {
        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $request->tahun)
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

        $html = '<div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="50%"></th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase program-tagging">Visi</span>
                                    </td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse show" id="program_visi'.$visi['id'].'">
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
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->where('tahun_perubahan', $request->tahun)
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
                                                        $html .= '<tr style="background: #c04141;">
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                        '.strtoupper($misi['deskripsi']).'
                                                                        <br>';
                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi '.$request->visi.'</span>';
                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                    </td>
                                                                    <td width="50%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse show" id="program_misi'.$misi['id'].'">
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
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan', $request->tahun)
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
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
                                                                                                        <br>';
                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi '.$request->visi.'</span>';
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="50%"></td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-bordered">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id']);
                                                                                                                    if($request->sasaran)
                                                                                                                    {
                                                                                                                        $get_sasarans = $get_sasarans->where('id', $request->sasaran);
                                                                                                                    }
                                                                                                                    $get_sasarans = $get_sasarans->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $request->tahun)
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
                                                                                                                                    <td width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td width="45%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>';
                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi '.$request->visi.'</span>';
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                    <td width="50%">';
                                                                                                                                        $html .= '<table class="table table-bordered">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="50%">Indikator Kinerja</th>
                                                                                                                                                        <th width="50%">Program RPJMD</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                                {
                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                        $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                                                                                                                                                        $a = 1;
                                                                                                                                                        foreach($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd)
                                                                                                                                                            {
                                                                                                                                                                if($a == 1)
                                                                                                                                                                {
                                                                                                                                                                        $get_perubahan_program = PivotPerubahanProgram::where('program_id', $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program_id)
                                                                                                                                                                                                    ->where('tahun_perubahan', $request->tahun)->latest()->first();
                                                                                                                                                                        if($get_perubahan_program)
                                                                                                                                                                        {
                                                                                                                                                                            $program_deskripsi = $get_perubahan_program->deskripsi;
                                                                                                                                                                        } else {
                                                                                                                                                                            $program_deskripsi = $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi;
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<td>'.$program_deskripsi;
                                                                                                                                                                        if($pivot_sasaran_indikator_program_rpjmd->program_rpjmd->status_program == 'Prioritas')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program Prioritas"></i>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<button type="button" class="btn-close btn-hapus-pivot-sasaran-indikator-program-rpjmd"
                                                                                                                                                                        data-pivot-sasaran-indikator-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->id.'"
                                                                                                                                                                        data-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd_id.'"
                                                                                                                                                                        data-sasaran-indikator-kinerja-id="'.$pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_kinerja_id.'"></button></td>';
                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                } else{
                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $get_perubahan_program = PivotPerubahanProgram::where('program_id', $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program_id)
                                                                                                                                                                                                    ->where('tahun_perubahan', $request->tahun)->latest()->first();
                                                                                                                                                                        if($get_perubahan_program)
                                                                                                                                                                        {
                                                                                                                                                                            $program_deskripsi = $get_perubahan_program->deskripsi;
                                                                                                                                                                        } else {
                                                                                                                                                                            $program_deskripsi = $pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi;
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<td>'.$program_deskripsi;
                                                                                                                                                                        if($pivot_sasaran_indikator_program_rpjmd->program_rpjmd->status_program == 'Prioritas')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program Prioritas"></i>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<button type="button" class="btn-close btn-hapus-pivot-sasaran-indikator-program-rpjmd"
                                                                                                                                                                        data-pivot-sasaran-indikator-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->id.'"
                                                                                                                                                                        data-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd_id.'"
                                                                                                                                                                        data-sasaran-indikator-kinerja-id="'.$pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_kinerja_id.'"></button></td>';
                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                }
                                                                                                                                                                $a++;
                                                                                                                                                            }
                                                                                                                                                }
                                                                                                                                                $html .= '</tbody>
                                                                                                                                        </table>';
                                                                                                                                    $html .= '</td>
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
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::all();
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $request->tahun)
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

        $html = '<div class="data-table-rows slim" id="sasaran_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase sasaran-tagging">Visi</span>
                                    </td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse show" id="sasaran_visi'.$visi['id'].'">
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
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->where('tahun_perubahan', $request->tahun)
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
                                                        $html .= '<tr style="background: #c04141;">
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                        '.strtoupper($misi['deskripsi']).'
                                                                        <br>';
                                                                        $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">'.$misi['kode'].' Misi</span>
                                                                    </td>
                                                                    <td width="30%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle"></td>
                                                                    <td width="20%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse show" id="sasaran_misi'.$misi['id'].'">
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
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan',$request->tahun)
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
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="75%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
                                                                                                        <br>';
                                                                                                        $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                    <td width ="20%">
                                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSasaranModal" title="Tambah Data Sasaran" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse show" id="sasaran_tujuan'.$tujuan['id'].'">
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
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id);
                                                                                                                        if($request->tahun != 'semua')
                                                                                                                        {
                                                                                                                            $cek_perubahan_sasaran = $cek_perubahan_sasaran->where('tahun_perubahan', $request->tahun);
                                                                                                                        }
                                                                                                                        $cek_perubahan_sasaran = $cek_perubahan_sasaran->latest()->first();
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
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="45%">
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
                                                                                                                                    </td>';
                                                                                                                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                    $html .= '<td width="28%"><table>
                                                                                                                                                <tbody>';
                                                                                                                                                    foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                            $html .= '<td width="75%">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                            $html .= '<td width="25%">
                                                                                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                                                                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                                                                                                                            </td>';
                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                    }
                                                                                                                                                $html .= '</tbody>
                                                                                                                                            </table></td>';
                                                                                                                                    $html .='<td width="22%">
                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-misi-id="'.$misi['id'].'" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Tambah Sasaran Indikator Kinerja"><i class="fas fa-lock"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Hapus Sasaran "><i class="fas fa-trash"></i></button>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="5%">No</th>
                                                                                                                                                        <th width="30%">Indikator</th>
                                                                                                                                                        <th width="17%">Target Kinerja Awal</th>
                                                                                                                                                        <th width="12%">Target</th>
                                                                                                                                                        <th width="12%">Satuan</th>
                                                                                                                                                        <th width="12%">Tahun</th>
                                                                                                                                                        <th width="12%">Aksi</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                $no_sasaran_indikator_kinerja = 1;
                                                                                                                                                foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                        $html .= '<td>'.$no_sasaran_indikator_kinerja++.'</td>';
                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                        $b = 1;
                                                                                                                                                        foreach ($tahuns as $tahun) {
                                                                                                                                                            $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                                                                                                                                                ->where('tahun', $tahun)->first();
                                                                                                                                                            if($cek_sasaran_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                if($b == 1)
                                                                                                                                                                {
                                                                                                                                                                    $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                    </button>
                                                                                                                                                                                </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                        </button>
                                                                                                                                                                                    </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                }
                                                                                                                                                                $b++;
                                                                                                                                                            } else {
                                                                                                                                                                if($b == 1)
                                                                                                                                                                {
                                                                                                                                                                        $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                    </button>
                                                                                                                                                                                </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                                                                                                                                        $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                    </button>
                                                                                                                                                                                </td>';
                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                }
                                                                                                                                                                $b++;
                                                                                                                                                            }
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
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::all();
        $tahun_sekarang = Carbon::now()->year;
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $request->tahun)
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

        $html = '<div class="data-table-rows slim" id="tujuan_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                        foreach ($visis as $visi) {
                            $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase tujuan-tagging">Visi</span>
                                    </td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse show" id="tujuan_visi'.$visi['id'].'">
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
                                                                            ->where('tahun_perubahan', $request->tahun)
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
                                                    $html .= '<tr style="background: #c04141;">
                                                        <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                        <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                            '.strtoupper($misi['deskripsi']).'
                                                            <br>';
                                                            $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi '.$request->visi.'</span>';
                                                            $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].' </span>
                                                        </td>
                                                        <td width="30%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle"></td>
                                                        <td width="20%">
                                                            <button class="btn btn-primary waves-effect waves-light mr-2 tujuan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditTujuanModal" title="Tambah Data Misi" data-misi-id="'.$misi['id'].'"><i class="fas fa-plus"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="hiddenRow">
                                                            <div class="collapse show" id="tujuan_misi'.$misi['id'].'">
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
                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id);
                                                                        if($request->tahun != 'semua')
                                                                        {
                                                                            $cek_perubahan_tujuan = $cek_perubahan_tujuan->where('tahun_perubahan', $request->tahun);
                                                                        }
                                                                        $cek_perubahan_tujuan = $cek_perubahan_tujuan->orderBy('created_at', 'desc');
                                                                        $cek_perubahan_tujuan = $cek_perubahan_tujuan->first();
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
                                                                            <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                            <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
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
                                                                            </td>';
                                                                            $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                                                                            $html .= '<td width="28%"><table>
                                                                                <tbody>';
                                                                                    foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td width="75%">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                                                                            $html .= '<td width="25%">
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-id="'.$tujuan['id'].'" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                                                            </td>';
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                $html .= '</tbody>
                                                                            </table></td>';
                                                                            $html .= '<td width="22%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-danger waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-indikator-kinerja" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Tambah Tujuan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                                                                                <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Hapus Tujuan "><i class="fas fa-trash"></i></button>
                                                                            </td>
                                                                        </tr>';
                                                                        $html .= '<tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                                                                                    <table class="table table-striped table-condesed">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th width="5%">No</th>
                                                                                                <th width="30%">Indikator</th>
                                                                                                <th width="17%">Target Kinerja Awal</th>
                                                                                                <th width="12%">Target</th>
                                                                                                <th width="12%">Satuan</th>
                                                                                                <th width="12%">Tahun</th>
                                                                                                <th width="12%">Aksi</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                        $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                                                                                        $no_tujuan_indikator_kinerja = 1;
                                                                                        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                                                                            $html .= '<tr>';
                                                                                                $html .= '<td>'.$no_tujuan_indikator_kinerja++.'</td>';
                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                $c = 1;
                                                                                                foreach ($tahuns as $tahun) {
                                                                                                    $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                                                                                                        ->where('tahun', $tahun)->first();
                                                                                                    if($cek_tujuan_target_satuan_rp_realisasi)
                                                                                                    {
                                                                                                        if($c == 1)
                                                                                                        {
                                                                                                                $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                            </button>
                                                                                                                        </td>';
                                                                                                            $html .='</tr>';
                                                                                                        } else {
                                                                                                            $html .= '<tr>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                </button>
                                                                                                                            </td>';
                                                                                                            $html .='</tr>';
                                                                                                        }
                                                                                                        $c++;
                                                                                                    } else {
                                                                                                        if($c == 1)
                                                                                                        {
                                                                                                                $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                            </button>
                                                                                                                        </td>';
                                                                                                            $html .='</tr>';
                                                                                                        } else {
                                                                                                            $html .= '<tr>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                                                                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                            </button>
                                                                                                                        </td>';
                                                                                                            $html .='</tr>';
                                                                                                        }
                                                                                                        $c++;
                                                                                                    }
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
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $request->tahun)
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
        $html = '<div class="data-table-rows slim" id="misi_div_table">
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
                                                            ->where('tahun_perubahan', $request->tahun)
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
                                $html .= '<tr style="background: #bbbbbb;">
                                            <td data-bs-toggle="collapse" data-bs-target="#misi_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                            <td data-bs-toggle="collapse" data-bs-target="#misi_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                                '.strtoupper($visi['deskripsi']).'
                                                <br>
                                                <span class="badge bg-primary text-uppercase misi-tagging">Visi</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary waves-effect waves-light mr-2 misi_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditMisiModal" title="Tambah Data Misi" data-visi-id="'.$visi['id'].'"><i class="fas fa-plus"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="hiddenRow">
                                                <div class="collapse show" id="misi_visi'.$visi['id'].'">
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
                                                                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-misi" data-misi-id="'.$misi['id'].'" data-tahun="'.$request->tahun.'" type="button" title="Detail Misi"><i class="fas fa-eye"></i></button>
                                                                            <button class="btn btn-icon btn-warning waves-effect waves-light edit-misi" data-misi-id="'.$misi['id'].'" data-visi-id="'.$visi['id'].'" data-tahun="'.$request->tahun.'" type="button" title="Edit Misi"><i class="fas fa-edit"></i></button>
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

    public function rpjmd_filter_program_status(Request $request)
    {
        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $request->tahun)
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

        $html = '<div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="50%"></th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase program-tagging">Visi</span>
                                    </td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse show" id="program_visi'.$visi['id'].'">
                                            <table class="table table-condensed table-striped">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                    $misis = [];
                                                    foreach ($get_misis as $get_misi) {
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->where('tahun_perubahan', $request->tahun)
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
                                                        $html .= '<tr style="background: #c04141;">
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                        '.strtoupper($misi['deskripsi']).'
                                                                        <br>';
                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi '.$request->visi.'</span>';
                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                    </td>
                                                                    <td width="50%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse show" id="program_misi'.$misi['id'].'">
                                                                            <table class="table table-condensed table-striped">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan', $request->tahun)
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
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="45%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
                                                                                                        <br>';
                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi '.$request->visi.'</span>';
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                    </td>
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="50%"></td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-bordered">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $request->tahun)
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
                                                                                                                                    <td width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td width="45%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>';
                                                                                                                                        $html .= '<span class="badge bg-primary text-uppercase program-tagging">Visi</span>';
                                                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase program-tagging">Misi '.$misi['kode'].'</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase program-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase program-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                                                                                                                                    </td>
                                                                                                                                    <td width="50%">';
                                                                                                                                        $html .= '<table class="table table-bordered">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="50%">Indikator Kinerja</th>
                                                                                                                                                        <th width="50%">Program RPJMD</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                                    foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                            $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                            if($request->value == 'semua')
                                                                                                                                                            {
                                                                                                                                                                $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                                                                                                                                                            }
                                                                                                                                                            if($request->value == 'prioritas')
                                                                                                                                                            {
                                                                                                                                                                $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                                                                                                                                            ->whereHas('program_rpjmd', function($q){
                                                                                                                                                                                                                $q->where('status_program', 'Prioritas');
                                                                                                                                                                                                            })->get();
                                                                                                                                                            }
                                                                                                                                                            if($request->value == 'pendukung')
                                                                                                                                                            {
                                                                                                                                                                $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                                                                                                                                            ->whereHas('program_rpjmd', function($q){
                                                                                                                                                                                                                $q->where('status_program', 'Pendukung');
                                                                                                                                                                                                            })->get();
                                                                                                                                                            }
                                                                                                                                                            $a = 1;
                                                                                                                                                            foreach($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd)
                                                                                                                                                            {
                                                                                                                                                                if($a == 1)
                                                                                                                                                                {
                                                                                                                                                                        $html .= '<td>'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi;
                                                                                                                                                                        if($pivot_sasaran_indikator_program_rpjmd->program_rpjmd->status_program == 'Prioritas')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program Prioritas"></i>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<button type="button" class="btn-close btn-hapus-pivot-sasaran-indikator-program-rpjmd"
                                                                                                                                                                        data-pivot-sasaran-indikator-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->id.'"
                                                                                                                                                                        data-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd_id.'"
                                                                                                                                                                        data-sasaran-indikator-kinerja-id="'.$pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_kinerja_id.'"></button></td>';
                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                } else{
                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                        $html .= '<td>'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd->program->deskripsi;
                                                                                                                                                                        if($pivot_sasaran_indikator_program_rpjmd->program_rpjmd->status_program == 'Prioritas')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program Prioritas"></i>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '<button type="button" class="btn-close btn-hapus-pivot-sasaran-indikator-program-rpjmd"
                                                                                                                                                                        data-pivot-sasaran-indikator-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->id.'"
                                                                                                                                                                        data-program-rpjmd-id="'.$pivot_sasaran_indikator_program_rpjmd->program_rpjmd_id.'"
                                                                                                                                                                        data-sasaran-indikator-kinerja-id="'.$pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_kinerja_id.'"></button></td>';
                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                }
                                                                                                                                                                $a++;
                                                                                                                                                            }
                                                                                                                                                    }
                                                                                                                                                $html .= '</tbody>
                                                                                                                                        </table>';
                                                                                                                                    $html .= '</td>
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
