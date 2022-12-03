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
use Auth;
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

class RenjaController extends Controller
{
    public function index()
    {
        return view('opd.renja.index');
    }

    public function get_tujuan()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::whereHas('misi', function($q){
            $q->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
                    $q->whereHas('sasaran_indikator_kinerja', function($q){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                            $q->whereHas('program_rpjmd', function($q){
                                $q->whereHas('program', function($q){
                                    $q->whereHas('program_indikator_kinerja', function($q){
                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            });
        })->get();
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

        $html = '<div class="data-table-rows slim" id="tujuan_div_table">
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
                                $html .= '<tr style="background: #bbbbbb;">
                                            <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                            <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                                '.$visi['deskripsi'].'
                                                <br>
                                                <span class="badge bg-primary text-uppercase renstra-tujuan-tagging">Visi</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="hiddenRow">
                                                <div class="collapse show" id="tujuan_visi'.$visi['id'].'">
                                                    <table class="table table-striped table-condesed">
                                                        <tbody>';
                                                        $get_misis = Misi::where('visi_id', $visi['id'])->whereHas('tujuan', function($q){
                                                            $q->whereHas('sasaran', function($q){
                                                                $q->whereHas('sasaran_indikator_kinerja', function($q){
                                                                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                        $q->whereHas('program_rpjmd', function($q){
                                                                            $q->whereHas('program', function($q){
                                                                                $q->whereHas('program_indikator_kinerja', function($q){
                                                                                    $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                    });
                                                                                });
                                                                            });
                                                                        });
                                                                    });
                                                                });
                                                            });
                                                        })->get();
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
                                                            $html .= '<tr style="background: #c04141;">
                                                                        <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                        <td width="95%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                            '.strtoupper($misi['deskripsi']).'
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
                                                                            <div class="collapse show" id="tujuan_misi'.$misi['id'].'">
                                                                                <table class="table table-striped table-condesed">
                                                                                    <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->whereHas('sasaran', function($q){
                                                                                        $q->whereHas('sasaran_indikator_kinerja', function($q){
                                                                                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                $q->whereHas('program_rpjmd', function($q){
                                                                                                    $q->whereHas('program', function($q){
                                                                                                        $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                            });
                                                                                                        });
                                                                                                    });
                                                                                                });
                                                                                            });
                                                                                        });
                                                                                    })->orderBy('kode', 'asc')->get();
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
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                            <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd'.$tujuan['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                            <td width="95%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                '.strtoupper($tujuan['deskripsi']).'
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
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td colspan="4" class="hiddenRow">
                                                                                                <div class="collapse show accordion-body" id="tujuan_tujuan_pd'.$tujuan['id'].'">
                                                                                                    <table class="table table-striped table-condesed">
                                                                                                        <thead>
                                                                                                            <tr>
                                                                                                                <th width="5%">Kode</th>
                                                                                                                <th width="55%">Tujuan PD</th>
                                                                                                                <th width="40%">Indikator Kinerja</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>';
                                                                                                        $get_tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                                                        $tujuan_pds = [];
                                                                                                        foreach ($get_tujuan_pds as $get_tujuan_pd) {
                                                                                                            $cek_perubahan_tujuan_opd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->latest()->first();
                                                                                                            if($cek_perubahan_tujuan_opd)
                                                                                                            {
                                                                                                                $tujuan_pds[] = [
                                                                                                                    'id' => $cek_perubahan_tujuan_opd->id,
                                                                                                                    'tujuan_pd_id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
                                                                                                                    'tujuan_id' => $cek_perubahan_tujuan_opd->tujuan_id,
                                                                                                                    'kode' => $cek_perubahan_tujuan_opd->kode,
                                                                                                                    'deskripsi' => $cek_perubahan_tujuan_opd->deskripsi,
                                                                                                                    'opd_id' => $cek_perubahan_tujuan_opd->opd_id,
                                                                                                                    'tahun_perubahan' => $cek_perubahan_tujuan_opd->tahun_perubahan,
                                                                                                                ];
                                                                                                            } else {
                                                                                                                $tujuan_pds[] = [
                                                                                                                    'id' => $get_tujuan_pd->id,
                                                                                                                    'tujuan_id' => $get_tujuan_pd->tujuan_id,
                                                                                                                    'kode' => $get_tujuan_pd->kode,
                                                                                                                    'deskripsi' => $get_tujuan_pd->deskripsi,
                                                                                                                    'opd_id' => $get_tujuan_pd->opd_id,
                                                                                                                    'tahun_perubahan' => $get_tujuan_pd->tahun_perubahan,
                                                                                                                ];
                                                                                                            }
                                                                                                        }
                                                                                                        foreach ($tujuan_pds as $tujuan_pd)
                                                                                                        {
                                                                                                            $html .= '<tr>';
                                                                                                                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['kode'].'</td>';
                                                                                                                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['deskripsi'].'</td>';
                                                                                                                $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                                                                                                                $html .= '<td><table class="table table-bordered">
                                                                                                                    <thead>
                                                                                                                        <th width="45%">Deskripsi</th>
                                                                                                                        <th width="20%">Satuan</th>
                                                                                                                        <th width="35%">Target Kinerja Awal</th>
                                                                                                                    </thead>
                                                                                                                    <tbody>';
                                                                                                                    foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                                                                                                        $html .= '<tr>';
                                                                                                                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.' (';
                                                                                                                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                                                                                                                {
                                                                                                                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                                                                                                                }
                                                                                                                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                                                                                                                {
                                                                                                                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                                                                                                                }
                                                                                                                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                                                                                                                {
                                                                                                                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                                                                                                                }
                                                                                                                            $html .= '</td>';
                                                                                                                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                                                                                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                        $html .= '</tr>';
                                                                                                                    }
                                                                                                                    $html .= '</tbody>
                                                                                                                </table></td>';
                                                                                                            $html .= '</tr>
                                                                                                            <tr>
                                                                                                                <td colspan="4" class="hiddenRow">
                                                                                                                    <div class="collapse" id="tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'">
                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                            <thead>
                                                                                                                                <tr>
                                                                                                                                    <th width="5%">No</th>
                                                                                                                                    <th width="30%">Indikator</th>
                                                                                                                                    <th width="20%">Target Kinerja Awal</th>
                                                                                                                                    <th width="10%">Target Kinerja</th>
                                                                                                                                    <th width="10%">Satuan</th>
                                                                                                                                    <th width="10%">Realisasi Kinerja</th>
                                                                                                                                    <th width="10%">Tahun</th>
                                                                                                                                    <th width="5%">Aksi</th>
                                                                                                                                </tr>
                                                                                                                            </thead>
                                                                                                                            <tbody>';
                                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                                                                                                                            $no_tujuan_pd_indikator_kinerja = 1;
                                                                                                                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                                            {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td>'.$no_tujuan_pd_indikator_kinerja++.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                    $b = 1;
                                                                                                                                    foreach ($tahuns as $tahun)
                                                                                                                                    {
                                                                                                                                        $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                        if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                        {
                                                                                                                                            if($b == 1)
                                                                                                                                            {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td><span class="tujuan-pd-span-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.' data-tujuan-pd-realisasi-renja-'.$cek_tujuan_pd_realisasi_renja->id.'">'.$cek_tujuan_pd_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td><input type="number" class="form-control tujuan-pd-add-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                    }
                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'" data-tujuan-pd-realisasi-renja-id="'.$cek_tujuan_pd_realisasi_renja->id.'">
                                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                </button>
                                                                                                                                                            </td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                    </button>
                                                                                                                                                                </td>';
                                                                                                                                                    }
                                                                                                                                                $html .='</tr>';
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td><span class="tujuan-pd-span-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.' data-tujuan-pd-realisasi-renja-'.$cek_tujuan_pd_realisasi_renja->id.'">'.$cek_tujuan_pd_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td><input type="number" class="form-control tujuan-pd-add-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                    }
                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'" data-tujuan-pd-realisasi-renja-id="'.$cek_tujuan_pd_realisasi_renja->id.'">
                                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                </button>
                                                                                                                                                            </td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                    </button>
                                                                                                                                                                </td>';
                                                                                                                                                    }
                                                                                                                                                $html .='</tr>';
                                                                                                                                            }
                                                                                                                                            $b++;
                                                                                                                                        } else {
                                                                                                                                            if($b == 1)
                                                                                                                                            {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                $html .='</tr>';
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                $html .='</tr>';
                                                                                                                                            }
                                                                                                                                            $b++;
                                                                                                                                        }
                                                                                                                                    }
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

        return response()->json(['html' => $html]);
    }

    public function get_sasaran()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::whereHas('misi', function($q){
            $q->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
                    $q->whereHas('sasaran_indikator_kinerja', function($q){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                            $q->whereHas('program_rpjmd', function($q){
                                $q->whereHas('program', function($q){
                                    $q->whereHas('program_indikator_kinerja', function($q){
                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            });
        })->get();
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

        $html = '<div class="data-table-rows slim" id="sasaran_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="55%">Deskripsi</th>
                                    <th width="40%">Indikator Kinerja</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr style="background: #bbbbbb;">
                                            <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle text-white"></td>
                                            <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                                '.strtoupper($visi['deskripsi']).'
                                                <br>
                                                <span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi</span>
                                            </td>
                                            <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle text-white"></td>
                                            <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle text-white"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="hiddenRow">
                                                <div class="collapse show" id="sasaran_visi'.$visi['id'].'">
                                                    <table class="table table-striped table-condensed">
                                                        <tbody>';
                                                        $get_misis = Misi::where('visi_id', $visi['id'])->whereHas('tujuan', function($q){
                                                            $q->whereHas('sasaran', function($q){
                                                                $q->whereHas('sasaran_indikator_kinerja', function($q){
                                                                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                        $q->whereHas('program_rpjmd', function($q){
                                                                            $q->whereHas('program', function($q){
                                                                                $q->whereHas('program_indikator_kinerja', function($q){
                                                                                    $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                    });
                                                                                });
                                                                            });
                                                                        });
                                                                    });
                                                                });
                                                            });
                                                        })->get();
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
                                                            $html .= '<tr style="background: #c04141;">
                                                                            <td width="5%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                            <td width="55%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                                '.strtoupper($misi['deskripsi']).'
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
                                                                            <td width="40%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="collapse show" id="sasaran_misi'.$misi['id'].'">
                                                                                    <table class="table table-striped table-condensed">
                                                                                        <tbody>';
                                                                                        $get_tujuans = Tujuan::where('misi_id', $misi['id'])->whereHas('sasaran', function($q){
                                                                                            $q->whereHas('sasaran_indikator_kinerja', function($q){
                                                                                                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                    $q->whereHas('program_rpjmd', function($q){
                                                                                                        $q->whereHas('program', function($q){
                                                                                                            $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                                $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                });
                                                                                                            });
                                                                                                        });
                                                                                                    });
                                                                                                });
                                                                                            });
                                                                                        })->get();
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
                                                                                            $html .= '<tr style="background: #41c0c0">
                                                                                                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                        <td width="55%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                            '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="40%"></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td colspan="4" class="hiddenRow">
                                                                                                            <div class="collapse show" id="sasaran_tujuan'.$tujuan['id'].'">
                                                                                                                <table class="table table-striped table-condensed">
                                                                                                                    <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->whereHas('sasaran_indikator_kinerja', function($q){
                                                                                                                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                                            $q->whereHas('program_rpjmd', function($q){
                                                                                                                                $q->whereHas('program', function($q){
                                                                                                                                    $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                        });
                                                                                                                                    });
                                                                                                                                });
                                                                                                                            });
                                                                                                                        });
                                                                                                                    })->get();
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
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_'.$sasaran['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_'.$sasaran['id'].'" class="accordion-toggle" width="55%">
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
                                                                                                                                    </td>';
                                                                                                                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                                                        $q->whereHas('program_rpjmd', function($q){
                                                                                                                                            $q->whereHas('program', function($q){
                                                                                                                                                $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                                                                    $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                    });
                                                                                                                                                });
                                                                                                                                            });
                                                                                                                                        });
                                                                                                                                    })->get();
                                                                                                                                    $html .= '<td width="40%" data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_'.$sasaran['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                        foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                        {
                                                                                                                                            $html .= '<li class="mb-2">'.$sasaran_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                        }
                                                                                                                                    $html .= '</ul></td>';
                                                                                                                                $html .= '</tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                        <div class="collapse show" id="sasaran_sasaran_'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="5%">Kode</th>
                                                                                                                                                        <th width="35%">Sasaran PD</th>
                                                                                                                                                        <th width="30%">Program Terkait</th>
                                                                                                                                                        <th width="30%">Indikator Kinerja</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
                                                                                                                                                $sasaran_pds = [];
                                                                                                                                                foreach ($get_sasaran_pds as $get_sasaran_pd) {
                                                                                                                                                    $cek_perubahan_sasaran_opd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)->latest()->first();
                                                                                                                                                    if($cek_perubahan_sasaran_opd)
                                                                                                                                                    {
                                                                                                                                                        $sasaran_pds[] = [
                                                                                                                                                            'id' => $cek_perubahan_sasaran_opd->id,
                                                                                                                                                            'sasaran_pd_id' => $cek_perubahan_sasaran_opd->sasaran_pd_id,
                                                                                                                                                            'sasaran_id' => $cek_perubahan_sasaran_opd->sasaran_id,
                                                                                                                                                            'kode' => $cek_perubahan_sasaran_opd->kode,
                                                                                                                                                            'deskripsi' => $cek_perubahan_sasaran_opd->deskripsi,
                                                                                                                                                            'opd_id' => $cek_perubahan_sasaran_opd->opd_id,
                                                                                                                                                            'tahun_perubahan' => $cek_perubahan_sasaran_opd->tahun_perubahan,
                                                                                                                                                        ];
                                                                                                                                                    } else {
                                                                                                                                                        $sasaran_pds[] = [
                                                                                                                                                            'id' => $get_sasaran_pd->id,
                                                                                                                                                            'sasaran_id' => $get_sasaran_pd->sasaran_id,
                                                                                                                                                            'kode' => $get_sasaran_pd->kode,
                                                                                                                                                            'deskripsi' => $get_sasaran_pd->deskripsi,
                                                                                                                                                            'opd_id' => $get_sasaran_pd->opd_id,
                                                                                                                                                            'tahun_perubahan' => $get_sasaran_pd->tahun_perubahan,
                                                                                                                                                        ];
                                                                                                                                                    }
                                                                                                                                                }
                                                                                                                                                foreach ($sasaran_pds as $sasaran_pd){
                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                        $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['kode'].'</td>';
                                                                                                                                                        $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['deskripsi'].'</td>';
                                                                                                                                                        $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                        $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                                                                                                                                                        foreach($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<li>'.$sasaran_pd_program_rpjmd->program_rpjmd->program->deskripsi.'</li>';
                                                                                                                                                        }
                                                                                                                                                        $html .= '</ul></td>';
                                                                                                                                                        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                                                                                                                                                        $html .= '<td><table class="table table-bordered">
                                                                                                                                                            <thead>
                                                                                                                                                                <th width="50%">Deskripsi</th>
                                                                                                                                                                <th width="20%">Satuan</th>
                                                                                                                                                                <th width="30%">T Awal</th>
                                                                                                                                                            </thead>
                                                                                                                                                            <tbody>';
                                                                                                                                                            foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.' (';
                                                                                                                                                                        if($sasaran_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                                                                                                                                                        }
                                                                                                                                                                        if($sasaran_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                                                                                                                                                        }
                                                                                                                                                                        if($sasaran_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                                                                                                                                                        }
                                                                                                                                                                    $html .= '</td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                $html .= '</tr>';
                                                                                                                                                            }
                                                                                                                                                            $html .='</tbody>
                                                                                                                                                        </table></td>';
                                                                                                                                                    $html .= '</tr>
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="5" class="hiddenRow">
                                                                                                                                                            <div class="collapse accordion-body" id="sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'">
                                                                                                                                                                <table class="table table-striped table-condesed">
                                                                                                                                                                    <thead>
                                                                                                                                                                        <tr>
                                                                                                                                                                            <th width="5%">No</th>
                                                                                                                                                                            <th width="30%">Indikator</th>
                                                                                                                                                                            <th width="20%">Target Kinerja Awal</th>
                                                                                                                                                                            <th width="10%">Target Kinerja</th>
                                                                                                                                                                            <th width="10%">Satuan</th>
                                                                                                                                                                            <th width="10%">Realisasi Kinerja</th>
                                                                                                                                                                            <th width="10%">Tahun</th>
                                                                                                                                                                            <th width="5%">Aksi</th>
                                                                                                                                                                        </tr>
                                                                                                                                                                    </thead>
                                                                                                                                                                    <tbody>';
                                                                                                                                                                        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                                                                                                                                                                        $no_sasaran_pd_indikator_kinerja = 1;
                                                                                                                                                                        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                $html .= '<td>'.$no_sasaran_pd_indikator_kinerja++.'</td>';
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                                $b = 1;
                                                                                                                                                                                foreach ($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                        ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($b == 1)
                                                                                                                                                                                        {
                                                                                                                                                                                                $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $html .= '<td><span class="sasaran-pd-span-realisasi '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.' data-sasaran-pd-target-satuan-rp-realisasi-'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.' data-sasaran-pd-realisasi-renja-'.$cek_sasaran_pd_realisasi_renja->id.'">'.$cek_sasaran_pd_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-realisasi '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.' data-sasaran-pd-target-satuan-rp-realisasi-'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-realisasi-renja" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi-id="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'" data-sasaran-pd-realisasi-renja-id="'.$cek_sasaran_pd_realisasi_renja->id.'">
                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                            </button>
                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-realisasi-renja" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi-id="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                </button>
                                                                                                                                                                                                            </td>';
                                                                                                                                                                                                }
                                                                                                                                                                                            $html .='</tr>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $html .= '<td><span class="sasaran-pd-span-realisasi '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.' data-sasaran-pd-target-satuan-rp-realisasi-'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.' data-sasaran-pd-realisasi-renja-'.$cek_sasaran_pd_realisasi_renja->id.'">'.$cek_sasaran_pd_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-realisasi '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.' data-sasaran-pd-target-satuan-rp-realisasi-'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-realisasi-renja" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi-id="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'" data-sasaran-pd-realisasi-renja-id="'.$cek_sasaran_pd_realisasi_renja->id.'">
                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                            </button>
                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-realisasi-renja" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi-id="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                </button>
                                                                                                                                                                                                            </td>';
                                                                                                                                                                                                }
                                                                                                                                                                                            $html .='</tr>';
                                                                                                                                                                                        }
                                                                                                                                                                                        $b++;
                                                                                                                                                                                    } else {
                                                                                                                                                                                        if($b == 1)
                                                                                                                                                                                        {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                            $html .='</tr>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                            $html .='</tr>';
                                                                                                                                                                                        }
                                                                                                                                                                                        $b++;
                                                                                                                                                                                    }
                                                                                                                                                                                }
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
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </div>';
        return response()->json(['html' => $html]);
    }

    public function get_program()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tws = MasterTw::all();

        $get_visis = Visi::whereHas('misi', function($q){
            $q->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
                    $q->whereHas('sasaran_indikator_kinerja', function($q){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                            $q->whereHas('program_rpjmd', function($q){
                                $q->whereHas('program', function($q){
                                    $q->whereHas('program_indikator_kinerja', function($q){
                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            });
        })->get();
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

        $html = '<div class="data-table-rows slim" id="program_div_table">
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
                                                <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                                <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                                    '.strtoupper($visi['deskripsi']).'
                                                    <br>
                                                    <span class="badge bg-primary text-uppercase renstra-program-tagging">Visi</span>
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                                <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="hiddenRow">
                                                    <div class="collapse show" id="program_visi'.$visi['id'].'">
                                                        <table class="table table-condensed table-striped">
                                                            <tbody>';
                                                            $get_misis = Misi::where('visi_id', $visi['id'])->whereHas('tujuan', function($q){
                                                                $q->whereHas('sasaran', function($q){
                                                                    $q->whereHas('sasaran_indikator_kinerja', function($q){
                                                                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                            $q->whereHas('program_rpjmd', function($q){
                                                                                $q->whereHas('program', function($q){
                                                                                    $q->whereHas('program_indikator_kinerja', function($q){
                                                                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                        });
                                                                                    });
                                                                                });
                                                                            });
                                                                        });
                                                                    });
                                                                });
                                                            })->get();
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
                                                            foreach ($misis as $misi)
                                                            {
                                                                $html .= '<tr style="background: #c04141;">
                                                                            <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                            <td width="45%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                                '.strtoupper($misi['deskripsi']).'
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
                                                                            <td width="30%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white"></td>
                                                                            <td width="20%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="collapse show" id="program_misi'.$misi['id'].'">
                                                                                    <table class="table table-condensed table-striped">
                                                                                        <tbody>';
                                                                                        $get_tujuans = Tujuan::where('misi_id', $misi['id'])->whereHas('sasaran', function($q){
                                                                                            $q->whereHas('sasaran_indikator_kinerja', function($q){
                                                                                                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                    $q->whereHas('program_rpjmd', function($q){
                                                                                                        $q->whereHas('program', function($q){
                                                                                                            $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                                $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                });
                                                                                                            });
                                                                                                        });
                                                                                                    });
                                                                                                });
                                                                                            });
                                                                                        })->get();
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
                                                                                        foreach ($tujuans as $tujuan)
                                                                                        {
                                                                                            $html .= '<tr style="background: #41c0c0">
                                                                                                        <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                        <td width="45%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
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
                                                                                                        <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="30%"></td>
                                                                                                        <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="20%"></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td colspan="4" class="hiddenRow">
                                                                                                            <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
                                                                                                                <table class="table table-condensed table-striped">
                                                                                                                    <tbody>';
                                                                                                                        $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->whereHas('sasaran_indikator_kinerja', function($q){
                                                                                                                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                                                $q->whereHas('program_rpjmd', function($q){
                                                                                                                                    $q->whereHas('program', function($q){
                                                                                                                                        $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                                                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                                $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                            });
                                                                                                                                        });
                                                                                                                                    });
                                                                                                                                });
                                                                                                                            });
                                                                                                                        })->get();
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
                                                                                                                        foreach ($sasarans as $sasaran)
                                                                                                                        {
                                                                                                                            $html .= '<tr style="background:#41c081">
                                                                                                                                        <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                        <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="45%">
                                                                                                                                        '.strtoupper($sasaran['deskripsi']).'
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
                                                                                                                                    </td>';
                                                                                                                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                                                        $q->whereHas('program_rpjmd', function($q){
                                                                                                                                            $q->whereHas('program', function($q){
                                                                                                                                                $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                                                                    $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                    });
                                                                                                                                                });
                                                                                                                                            });
                                                                                                                                        });
                                                                                                                                    })->get();
                                                                                                                                    $html .= '<td width="30%" data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                        foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                        {
                                                                                                                                            $html .= '<li class="mb-2 text-white">'.strtoupper($sasaran_indikator_kinerja->deskripsi).'</li>';
                                                                                                                                        }
                                                                                                                                    $html .= '</ul></td>
                                                                                                                                    <td width="20%"></td>';
                                                                                                                                    $html .= '</tr>
                                                                                                                                    <tr>
                                                                                                                                        <td colspan="4" class="hiddenRow">
                                                                                                                                            <div class="collapse show" id="program_sasaran_'.$sasaran['id'].'">
                                                                                                                                                <table class="table table-condensed table-striped">
                                                                                                                                                    <tbody>';
                                                                                                                                                    $get_programs = Program::whereHas('program_rpjmd', function($q) use ($sasaran){
                                                                                                                                                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                                                                                                                                                            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                                                                                                                                                $q->whereHas('sasaran', function($q) use ($sasaran) {
                                                                                                                                                                    $q->where('id', $sasaran['id']);
                                                                                                                                                                });
                                                                                                                                                            });
                                                                                                                                                        });
                                                                                                                                                    })->whereHas('program_indikator_kinerja', function($q){
                                                                                                                                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                        });
                                                                                                                                                    })->get();
                                                                                                                                                    $programs = [];
                                                                                                                                                    foreach($get_programs as $get_program)
                                                                                                                                                    {
                                                                                                                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                                                                                                                                                        if($cek_perubahan_program)
                                                                                                                                                        {
                                                                                                                                                            $programs[] = [
                                                                                                                                                                'id' => $cek_perubahan_program->program_id,
                                                                                                                                                                'kode' => $cek_perubahan_program->kode,
                                                                                                                                                                'deskripsi' => $cek_perubahan_program->deskripsi,
                                                                                                                                                                'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan
                                                                                                                                                            ];
                                                                                                                                                        } else {
                                                                                                                                                            $programs[] = [
                                                                                                                                                                'id' => $get_program->id,
                                                                                                                                                                'kode' => $get_program->kode,
                                                                                                                                                                'deskripsi' => $get_program->deskripsi,
                                                                                                                                                                'tahun_perubahan' => $get_program->tahun_perubahan
                                                                                                                                                            ];
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    foreach ($programs as $program) {
                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                            $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle">'.$program['kode'].'</td>';
                                                                                                                                                            $html .= '<td width="45%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle">'.$program['deskripsi'];
                                                                                                                                                            $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->where('status_program', 'Prioritas')->first();
                                                                                                                                                            if($cek_program_rjmd)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<i class="fas fa-star text-primary" title="Program Prioritas"> </i>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<br>';
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
                                                                                                                                                            <span class="badge bg-dark text-uppercase renstra-sasaran-tagging">Program '.$program['kode'].'</span></td>';
                                                                                                                                                            $html .= '<td width="30%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])
                                                                                                                                                                                                ->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                                                                })->get();
                                                                                                                                                                foreach($program_indikator_kinerjas as $program_indikator_kinerja)
                                                                                                                                                                {
                                                                                                                                                                    $html .= '<li>'.$program_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                                }
                                                                                                                                                            $html .= '</ul></td>';
                                                                                                                                                            $html .= '<td width="20%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"></td>';
                                                                                                                                                        $html .= '</tr>
                                                                                                                                                        <tr>
                                                                                                                                                            <td colspan="4" class="hiddenRow">
                                                                                                                                                                <div class="collapse accordion-body" id="program_program_'.$program['id'].'">
                                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                                        <thead>
                                                                                                                                                                            <tr>
                                                                                                                                                                                <th>No</th>
                                                                                                                                                                                <th>Indikator</th>
                                                                                                                                                                                <th>Target Kinerja Awal</th>
                                                                                                                                                                                <th>OPD</th>
                                                                                                                                                                                <th>Target Kinerja</th>
                                                                                                                                                                                <th>Satuan</th>
                                                                                                                                                                                <th>Target Anggaran</th>
                                                                                                                                                                                <th>Tahun</th>
                                                                                                                                                                                <th>Aksi</th>
                                                                                                                                                                            </tr>
                                                                                                                                                                        </thead>
                                                                                                                                                                        <tbody>';
                                                                                                                                                                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                                        })->get();
                                                                                                                                                                        $no_program_indikator_kinerja = 1;
                                                                                                                                                                        foreach ($program_indikator_kinerjas as $program_indikator_kinerja)
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                $html .= '<td>'.$no_program_indikator_kinerja++.'</td>';
                                                                                                                                                                                $html .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $html .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                                $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                                                                                                                                                ->where('opd_id', Auth::user()->opd->opd_id)
                                                                                                                                                                                                                ->get();
                                                                                                                                                                                $b = 1;
                                                                                                                                                                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                                                                                                                                                                    if($b == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        // Opd program indikator
                                                                                                                                                                                        $html .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                                                        $c = 1;
                                                                                                                                                                                        foreach ($tahuns as $tahun) {
                                                                                                                                                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                                                                                                                                                        ->where('tahun', $tahun)
                                                                                                                                                                                                                                        ->first();
                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                if($c == 1)
                                                                                                                                                                                                {
                                                                                                                                                                                                        $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td> Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                        $html .= '<td>
                                                                                                                                                                                                                    <button type="button"
                                                                                                                                                                                                                        class="btn btn-icon btn-primary waves-effect waves-light btn-open-program-tw-realisasi '.$tahun.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                        data-tahun="'.$tahun.'"
                                                                                                                                                                                                                        data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                        value="close"
                                                                                                                                                                                                                        data-bs-toggle="collapse"
                                                                                                                                                                                                                        data-bs-target="#program_indikator_'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                        class="accordion-toggle">
                                                                                                                                                                                                                            <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                </td>';
                                                                                                                                                                                                    $html .='</tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td colspan="10" class="hiddenRow">
                                                                                                                                                                                                            <div class="collapse accordion-body" id="program_indikator_'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                <table class="table table-striped table-condesed">
                                                                                                                                                                                                                    <thead>
                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                            <th width="18%">Target Kinerja</th>
                                                                                                                                                                                                                            <th width="18%">Satuan</th>
                                                                                                                                                                                                                            <th width="18%">Tahun</th>
                                                                                                                                                                                                                            <th width="18%">TW</th>
                                                                                                                                                                                                                            <th width="18%">Realisasi Kinerja</th>
                                                                                                                                                                                                                            <th width="10%">Aksi</th>
                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                    </thead>
                                                                                                                                                                                                                    <tbody>';
                                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                                            $html .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                                            $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $d = 1;
                                                                                                                                                                                                                            foreach ($tws as $tw) {
                                                                                                                                                                                                                                if($d == 1)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                        $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                        if($cek_program_tw_realisasi_renja)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $html .= '<td><span class="span-program-tw-realisasi-renja '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.' data-program-tw-realisasi-renja-'.$cek_program_tw_realisasi_renja->id.'">'.$cek_program_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-tw-realisasi-renja-id="'.$cek_program_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control input-program-tw-realisasi-renja-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                        $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                        if($cek_program_tw_realisasi_renja)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $html .= '<td><span class="span-program-tw-realisasi-renja '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.' data-program-tw-realisasi-renja-'.$cek_program_tw_realisasi_renja->id.'">'.$cek_program_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-tw-realisasi-renja-id="'.$cek_program_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control input-program-tw-realisasi-renja-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $d++;
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                    $html .= '</tbody>
                                                                                                                                                                                                                </table>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                    </tr>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                    $html .= '<td> Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                    $html .= '<td>
                                                                                                                                                                                                                    <button type="button"
                                                                                                                                                                                                                        class="btn btn-icon btn-primary waves-effect waves-light btn-open-program-tw-realisasi '.$tahun.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                        data-tahun="'.$tahun.'"
                                                                                                                                                                                                                        data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                        value="close"
                                                                                                                                                                                                                        data-bs-toggle="collapse"
                                                                                                                                                                                                                        data-bs-target="#program_indikator_'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                        class="accordion-toggle">
                                                                                                                                                                                                                            <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                </td>';
                                                                                                                                                                                                    $html .='</tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td colspan="10" class="hiddenRow">
                                                                                                                                                                                                            <div class="collapse accordion-body" id="program_indikator_'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                <table class="table table-striped table-condesed">
                                                                                                                                                                                                                    <thead>
                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                            <th width="18%">Target</th>
                                                                                                                                                                                                                            <th width="18%">Satuan</th>
                                                                                                                                                                                                                            <th width="18%">Tahun</th>
                                                                                                                                                                                                                            <th width="18%">TW</th>
                                                                                                                                                                                                                            <th width="18%">Realisasi</th>
                                                                                                                                                                                                                            <th width="10%">Aksi</th>
                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                    </thead>
                                                                                                                                                                                                                    <tbody>';
                                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                                            $html .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                                            $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $d = 1;
                                                                                                                                                                                                                            foreach ($tws as $tw) {
                                                                                                                                                                                                                                if($d == 1)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                        $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                        if($cek_program_tw_realisasi_renja)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $html .= '<td><span class="span-program-tw-realisasi-renja '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.' data-program-tw-realisasi-renja-'.$cek_program_tw_realisasi_renja->id.'">'.$cek_program_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-tw-realisasi-renja-id="'.$cek_program_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control input-program-tw-realisasi-renja-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                        $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                        if($cek_program_tw_realisasi_renja)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $html .= '<td><span class="span-program-tw-realisasi-renja '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.' data-program-tw-realisasi-renja-'.$cek_program_tw_realisasi_renja->id.'">'.$cek_program_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-tw-realisasi-renja-id="'.$cek_program_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control input-program-tw-realisasi-renja-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $d++;
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                    $html .= '</tbody>
                                                                                                                                                                                                                </table>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                    </tr>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $c++;
                                                                                                                                                                                            } else {
                                                                                                                                                                                                if($c == 1)
                                                                                                                                                                                                {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                        $html .= '<td>
                                                                                                                                                                                                            <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                    $html .='</>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                        $html .= '<td>
                                                                                                                                                                                                            <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $c++;
                                                                                                                                                                                            }
                                                                                                                                                                                        }
                                                                                                                                                                                    } else {
                                                                                                                                                                                        // Belum Opd program indikator
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                                                            $c = 1;
                                                                                                                                                                                            foreach ($tahuns as $tahun) {
                                                                                                                                                                                                $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                                                                                                                                                            ->where('tahun', $tahun)
                                                                                                                                                                                                                                            ->first();
                                                                                                                                                                                                if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                {
                                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                                    {
                                                                                                                                                                                                            $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                            $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                            $html .= '<td> Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-program-tw-realisasi '.$tahun.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            data-tahun="'.$tahun.'"
                                                                                                                                                                                                                            data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            value="close"
                                                                                                                                                                                                                            data-bs-toggle="collapse"
                                                                                                                                                                                                                            data-bs-target="#program_indikator_'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            class="accordion-toggle">
                                                                                                                                                                                                                                <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                        $html .='</tr>
                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                            <td colspan="10" class="hiddenRow">
                                                                                                                                                                                                                <div class="collapse accordion-body" id="program_indikator_'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                                                                                        <thead>
                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                <th width="18%">Target</th>
                                                                                                                                                                                                                                <th width="18%">Satuan</th>
                                                                                                                                                                                                                                <th width="18%">Tahun</th>
                                                                                                                                                                                                                                <th width="18%">TW</th>
                                                                                                                                                                                                                                <th width="18%">Realisasi</th>
                                                                                                                                                                                                                                <th width="10%">Aksi</th>
                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                        </thead>
                                                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                                                $html .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                                                $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                                $d = 1;
                                                                                                                                                                                                                                foreach ($tws as $tw) {
                                                                                                                                                                                                                                    if($d == 1)
                                                                                                                                                                                                                                    {
                                                                                                                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                            $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                            if($cek_program_tw_realisasi_renja)
                                                                                                                                                                                                                                            {
                                                                                                                                                                                                                                                $html .= '<td><span class="span-program-tw-realisasi-renja '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.' data-program-tw-realisasi-renja-'.$cek_program_tw_realisasi_renja->id.'">'.$cek_program_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-tw-realisasi-renja-id="'.$cek_program_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-program-tw-realisasi-renja-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                            $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                            if($cek_program_tw_realisasi_renja)
                                                                                                                                                                                                                                            {
                                                                                                                                                                                                                                                $html .= '<td><span class="span-program-tw-realisasi-renja '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.' data-program-tw-realisasi-renja-'.$cek_program_tw_realisasi_renja->id.'">'.$cek_program_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-tw-realisasi-renja-id="'.$cek_program_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-program-tw-realisasi-renja-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    $d++;
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                        $html .= '</tbody>
                                                                                                                                                                                                                    </table>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </td>
                                                                                                                                                                                                        </tr>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td> Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                        $html .= '<td>
                                                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-program-tw-realisasi '.$tahun.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            data-tahun="'.$tahun.'"
                                                                                                                                                                                                                            data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            value="close"
                                                                                                                                                                                                                            data-bs-toggle="collapse"
                                                                                                                                                                                                                            data-bs-target="#program_indikator_'.$cek_program_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            class="accordion-toggle">
                                                                                                                                                                                                                                <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                        $html .='</tr>
                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                            <td colspan="10" class="hiddenRow">
                                                                                                                                                                                                                <div class="collapse accordion-body" id="program_indikator_'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                                                                                        <thead>
                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                <th width="18%">Target</th>
                                                                                                                                                                                                                                <th width="18%">Satuan</th>
                                                                                                                                                                                                                                <th width="18%">Tahun</th>
                                                                                                                                                                                                                                <th width="18%">TW</th>
                                                                                                                                                                                                                                <th width="18%">Realisasi</th>
                                                                                                                                                                                                                                <th width="10%">Aksi</th>
                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                        </thead>
                                                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                                                $html .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                                                $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                                $d = 1;
                                                                                                                                                                                                                                foreach ($tws as $tw) {
                                                                                                                                                                                                                                    if($d == 1)
                                                                                                                                                                                                                                    {
                                                                                                                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                            $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                            if($cek_program_tw_realisasi_renja)
                                                                                                                                                                                                                                            {
                                                                                                                                                                                                                                                $html .= '<td><span class="span-program-tw-realisasi-renja '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.' data-program-tw-realisasi-renja-'.$cek_program_tw_realisasi_renja->id.'">'.$cek_program_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-tw-realisasi-renja-id="'.$cek_program_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-program-tw-realisasi-renja-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                            $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                            if($cek_program_tw_realisasi_renja)
                                                                                                                                                                                                                                            {
                                                                                                                                                                                                                                                $html .= '<td><span class="span-program-tw-realisasi-renja '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.' data-program-tw-realisasi-renja-'.$cek_program_tw_realisasi_renja->id.'">'.$cek_program_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'" data-program-tw-realisasi-renja-id="'.$cek_program_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-program-tw-realisasi-renja-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-program-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-program-target-satuan-rp-realisasi-id = "'.$cek_program_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    $d++;
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                        $html .= '</tbody>
                                                                                                                                                                                                                    </table>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </td>
                                                                                                                                                                                                        </tr>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $c++;
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                                    {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
                                                                                                                                                                                                            </td>';
                                                                                                                                                                                                        $html .='</>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
                                                                                                                                                                                                            </td>';
                                                                                                                                                                                                        $html .='</tr>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $c++;
                                                                                                                                                                                                }
                                                                                                                                                                                            }
                                                                                                                                                                                    }
                                                                                                                                                                                    $b++;
                                                                                                                                                                                }
                                                                                                                                                                            $html .= '</tr>';
                                                                                                                                                                        }
                                                                                                                                                                        $html .= '</tbody>
                                                                                                                                                                    </table>
                                                                                                                                                                </di>
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
                </div>';
        return response()->json(['html' => $html]);
    }

    public function get_kegiatan()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tws = MasterTw::all();

        $get_visis = Visi::whereHas('misi', function($q){
            $q->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
                    $q->whereHas('sasaran_indikator_kinerja', function($q){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                            $q->whereHas('program_rpjmd', function($q){
                                $q->whereHas('program', function($q){
                                    $q->whereHas('program_indikator_kinerja', function($q){
                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            });
        })->get();
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

        $html = '<div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="55%">Deskripsi</th>
                                    <th width="40%">Indikator Kinerja</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr style="background: #bbbbbb;">
                                            <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                            <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                                '.strtoupper($visi['deskripsi']).'
                                                <br>
                                                <span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi</span>
                                            </td>
                                            <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="hiddenRow">
                                                <div class="collapse show" id="program_visi'.$visi['id'].'">
                                                    <table class="table table-condensed table-striped">
                                                        <tbody>';
                                                        $get_misis = Misi::where('visi_id', $visi['id'])->whereHas('tujuan', function($q){
                                                            $q->whereHas('sasaran', function($q){
                                                                $q->whereHas('sasaran_indikator_kinerja', function($q){
                                                                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                        $q->whereHas('program_rpjmd', function($q){
                                                                            $q->whereHas('program', function($q){
                                                                                $q->whereHas('program_indikator_kinerja', function($q){
                                                                                    $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                    });
                                                                                });
                                                                            });
                                                                        });
                                                                    });
                                                                });
                                                            });
                                                        })->get();
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
                                                        foreach ($misis as $misi)
                                                        {
                                                            $html .= '<tr style="background: #c04141;">
                                                                        <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                        <td width="55%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                            '.strtoupper($misi['deskripsi']).'
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
                                                                        <td width="40%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="4" class="hiddenRow">
                                                                            <div class="collapse show" id="program_misi'.$misi['id'].'">
                                                                                <table class="table table-condensed table-striped">
                                                                                    <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->whereHas('sasaran', function($q){
                                                                                        $q->whereHas('sasaran_indikator_kinerja', function($q){
                                                                                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                $q->whereHas('program_rpjmd', function($q){
                                                                                                    $q->whereHas('program', function($q){
                                                                                                        $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                            });
                                                                                                        });
                                                                                                    });
                                                                                                });
                                                                                            });
                                                                                        });
                                                                                    })->get();
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
                                                                                    foreach ($tujuans as $tujuan)
                                                                                    {
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="55%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="40%"></td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                <tbody>';
                                                                                                                $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->whereHas('sasaran_indikator_kinerja', function($q){
                                                                                                                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                                        $q->whereHas('program_rpjmd', function($q){
                                                                                                                            $q->whereHas('program', function($q){
                                                                                                                                $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                                                    $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                    });
                                                                                                                                });
                                                                                                                            });
                                                                                                                        });
                                                                                                                    });
                                                                                                                })->get();
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
                                                                                                                foreach ($sasarans as $sasaran)
                                                                                                                {
                                                                                                                    $html .= '<tr style="background:#41c081">
                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="55%">
                                                                                                                                        '.strtoupper($sasaran['deskripsi']).'
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
                                                                                                                                </td>';
                                                                                                                                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                                                    $q->whereHas('program_rpjmd', function($q){
                                                                                                                                        $q->whereHas('program', function($q){
                                                                                                                                            $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                                                                $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                });
                                                                                                                                            });
                                                                                                                                        });
                                                                                                                                    });
                                                                                                                                })->get();
                                                                                                                                $html .= '<td width="40%" data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                    foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                    {
                                                                                                                                        $html .= '<li class="mb-2 text-white">'.strtoupper($sasaran_indikator_kinerja->deskripsi).'</li>';
                                                                                                                                    }
                                                                                                                                $html .= '</ul></td>';
                                                                                                                    $html .= '</tr>
                                                                                                                    <tr>
                                                                                                                        <td colspan="4" class="hiddenRow">
                                                                                                                            <div class="collapse show" id="program_sasaran_'.$sasaran['id'].'">
                                                                                                                                <table class="table table-condensed table-striped">
                                                                                                                                    <tbody>';
                                                                                                                                    $get_programs = Program::whereHas('program_rpjmd', function($q) use ($sasaran){
                                                                                                                                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                                                                                                                                            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                                                                                                                                $q->whereHas('sasaran', function($q) use ($sasaran) {
                                                                                                                                                    $q->where('id', $sasaran['id']);
                                                                                                                                                });
                                                                                                                                            });
                                                                                                                                        });
                                                                                                                                    })->whereHas('program_indikator_kinerja', function($q){
                                                                                                                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                        });
                                                                                                                                    })->get();
                                                                                                                                    $programs = [];
                                                                                                                                    foreach($get_programs as $get_program)
                                                                                                                                    {
                                                                                                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                                                                                                                                        if($cek_perubahan_program)
                                                                                                                                        {
                                                                                                                                            $programs[] = [
                                                                                                                                                'id' => $cek_perubahan_program->program_id,
                                                                                                                                                'kode' => $cek_perubahan_program->kode,
                                                                                                                                                'deskripsi' => $cek_perubahan_program->deskripsi,
                                                                                                                                                'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan
                                                                                                                                            ];
                                                                                                                                        } else {
                                                                                                                                            $programs[] = [
                                                                                                                                                'id' => $get_program->id,
                                                                                                                                                'kode' => $get_program->kode,
                                                                                                                                                'deskripsi' => $get_program->deskripsi,
                                                                                                                                                'tahun_perubahan' => $get_program->tahun_perubahan
                                                                                                                                            ];
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                    foreach($programs as $program)
                                                                                                                                    {
                                                                                                                                        $html .= '<tr style="background:#bbae7f;">';
                                                                                                                                                $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle text-white">'.$program['kode'].'</td>';
                                                                                                                                                $html .= '<td width="55%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle text-white">'.strtoupper($program['deskripsi']);
                                                                                                                                                $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->where('status_program', 'Prioritas')->first();
                                                                                                                                                if($cek_program_rjmd)
                                                                                                                                                {
                                                                                                                                                    $html .= '<i class="fas fa-star text-primary" title="Program Prioritas"> </i>';
                                                                                                                                                }
                                                                                                                                                $html .= '<br>';
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
                                                                                                                                                <span class="badge bg-dark text-uppercase renstra-kegiatan-tagging">Program '.$program['kode'].'</span></td>';
                                                                                                                                                $html .= '<td width="40%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])
                                                                                                                                                                                    ->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                                                    })->get();
                                                                                                                                                    foreach($program_indikator_kinerjas as $program_indikator_kinerja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<li class="text-white">'.strtoupper($program_indikator_kinerja->deskripsi).'</li>';
                                                                                                                                                    }
                                                                                                                                                $html .= '</ul></td>';
                                                                                                                                        $html .= '</tr>
                                                                                                                                        <tr>
                                                                                                                                            <td colspan="4" class="hiddenRow">
                                                                                                                                                <div class="collapse show" id="program_program_'.$program['id'].'">
                                                                                                                                                    <table class="table table-condensed table-striped">
                                                                                                                                                        <tbody>';
                                                                                                                                                        $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q){
                                                                                                                                                            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                                                                                                                $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                            });
                                                                                                                                                        })->get();
                                                                                                                                                        $kegiatans = [];
                                                                                                                                                        foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                                                                            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                                                                                                                                                                        ->latest()->first();
                                                                                                                                                            if($cek_perubahan_kegiatan)
                                                                                                                                                            {
                                                                                                                                                                $kegiatans[] = [
                                                                                                                                                                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                                                                    'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                                                                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                                                                    'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                                                                                                                                                                ];
                                                                                                                                                            } else {
                                                                                                                                                                $kegiatans[] = [
                                                                                                                                                                    'id' => $get_kegiatan->id,
                                                                                                                                                                    'kode' => $get_kegiatan->kode,
                                                                                                                                                                    'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                                                                                    'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                                                                                                                                                                ];
                                                                                                                                                            }
                                                                                                                                                        }
                                                                                                                                                        foreach($kegiatans as $kegiatan)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle">'.$program['kode'].'.'.$kegiatan['kode'].'</td>';
                                                                                                                                                                $html .= '<td width="55%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle">'.strtoupper($kegiatan['deskripsi']);
                                                                                                                                                                $html .= '<br>';
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
                                                                                                                                                                <span class="badge bg-dark text-uppercase renstra-kegiatan-tagging">Program '.$program['kode'].'</span>
                                                                                                                                                                <span class="badge bg-success text-uppercase renstra-kegiatan-tagging">Kegiatan '.$program['kode'].'.'.$kegiatan['kode'].'</span></td>';
                                                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                                                                                                                                                                ->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                                                                                                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                                                                })->get();
                                                                                                                                                                $html .= '<td width="40%"><ul>';
                                                                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                                                        if($kegiatan_indikator_kinerja->status_indikator == 'Target NSPK')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<li>'.$kegiatan_indikator_kinerja->deskripsi.' (<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)'.'</li>';
                                                                                                                                                                        }
                                                                                                                                                                        if($kegiatan_indikator_kinerja->status_indikator == 'Target IKK')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<li>'.$kegiatan_indikator_kinerja->deskripsi.' (<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)'.'</li>';
                                                                                                                                                                        }
                                                                                                                                                                        if($kegiatan_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<li>'.$kegiatan_indikator_kinerja->deskripsi.' (<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)'.'</li>';
                                                                                                                                                                        }
                                                                                                                                                                    }
                                                                                                                                                                $html .='</ul></td>';
                                                                                                                                                            $html .= '</tr>
                                                                                                                                                            <tr>
                                                                                                                                                                <td colspan="4" class="hiddenRow">
                                                                                                                                                                    <div class="collapse accordion-body" id="program_kegiatan_'.$kegiatan['id'].'">
                                                                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                                                                            <thead>
                                                                                                                                                                                <tr>
                                                                                                                                                                                    <th>No</th>
                                                                                                                                                                                    <th>Indikator</th>
                                                                                                                                                                                    <th>Target Kinerja Awal</th>
                                                                                                                                                                                    <th>Target Kinerja</th>
                                                                                                                                                                                    <th>Satuan</th>
                                                                                                                                                                                    <th>Target Anggaran</th>
                                                                                                                                                                                    <th>Tahun</th>
                                                                                                                                                                                    <th>Aksi</th>
                                                                                                                                                                                    <tbody>';
                                                                                                                                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                                                                    $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                                            $b = 1;
                                                                                                                                                                                            foreach ($tahuns as $tahun)
                                                                                                                                                                                            {
                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                                                                                                                                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                                                                    $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
                                                                                                                                                                                                })->where('tahun', $tahun)->first();
                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                {
                                                                                                                                                                                                    if($b == 1)
                                                                                                                                                                                                    {
                                                                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                            $html .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</td>';
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-kegiatan-tw-realisasi '.$tahun.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            data-tahun="'.$tahun.'"
                                                                                                                                                                                                                            data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            value="close"
                                                                                                                                                                                                                            data-bs-toggle="collapse"
                                                                                                                                                                                                                            data-bs-target="#kegiatan_indikator_'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            class="accordion-toggle">
                                                                                                                                                                                                                                <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                        $html .='</tr>
                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                            <td colspan="10" class="hiddenRow">
                                                                                                                                                                                                                <div class="collapse accordion-body" id="kegiatan_indikator_'.$cek_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                                                                                        <thead>
                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                <th width="18%">Target Kinerja</th>
                                                                                                                                                                                                                                <th width="18%">Satuan</th>
                                                                                                                                                                                                                                <th width="18%">Tahun</th>
                                                                                                                                                                                                                                <th width="18%">TW</th>
                                                                                                                                                                                                                                <th width="18%">Realisasi Kinerja</th>
                                                                                                                                                                                                                                <th width="10%">Aksi</th>
                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                        </thead>
                                                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $d = 1;
                                                                                                                                                                                                                            foreach ($tws as $tw) {
                                                                                                                                                                                                                                if($d == 1)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                        $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                        if($cek_kegiatan_tw_realisasi_renja)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                        $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                        if($cek_kegiatan_tw_realisasi_renja)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $d++;
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                        $html .= '</tbody>
                                                                                                                                                                                                                    </table>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </td>
                                                                                                                                                                                                        </tr>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                            $html .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</td>';
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-kegiatan-tw-realisasi '.$tahun.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            data-tahun="'.$tahun.'"
                                                                                                                                                                                                                            data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            value="close"
                                                                                                                                                                                                                            data-bs-toggle="collapse"
                                                                                                                                                                                                                            data-bs-target="#kegiatan_indikator_'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                            class="accordion-toggle">
                                                                                                                                                                                                                                <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                        $html .='</tr>
                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                            <td colspan="10" class="hiddenRow">
                                                                                                                                                                                                                <div class="collapse accordion-body" id="kegiatan_indikator_'.$cek_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                                                                                        <thead>
                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                <th width="18%">Target</th>
                                                                                                                                                                                                                                <th width="18%">Satuan</th>
                                                                                                                                                                                                                                <th width="18%">Tahun</th>
                                                                                                                                                                                                                                <th width="18%">TW</th>
                                                                                                                                                                                                                                <th width="18%">Realisasi</th>
                                                                                                                                                                                                                                <th width="10%">Aksi</th>
                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                        </thead>
                                                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $d = 1;
                                                                                                                                                                                                                            foreach ($tws as $tw) {
                                                                                                                                                                                                                                if($d == 1)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                        $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                        if($cek_kegiatan_tw_realisasi_renja)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                        $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                        if($cek_kegiatan_tw_realisasi_renja)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $d++;
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                        $html .= '</tbody>
                                                                                                                                                                                                                    </table>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </td>
                                                                                                                                                                                                        </tr>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $b++;
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    if($b == 1)
                                                                                                                                                                                                    {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                        <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                        $html .='</tr>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                        <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                        $html .='</tr>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $b++;
                                                                                                                                                                                                }
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $html .= '</tbody>
                                                                                                                                                                                </tr>
                                                                                                                                                                            </thead>
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

    public function get_sub_kegiatan()
    {
        $tws = MasterTw::all();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::whereHas('misi', function($q){
            $q->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
                    $q->whereHas('sasaran_indikator_kinerja', function($q){
                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                            $q->whereHas('program_rpjmd', function($q){
                                $q->whereHas('program', function($q){
                                    $q->whereHas('program_indikator_kinerja', function($q){
                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            });
        })->get();
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

        $html = '<div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="55%">Deskripsi</th>
                                    <th width="40%">Indikator Kinerja</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi)
                            {
                                $html .= '<tr style="background: #bbbbbb;">
                                            <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                            <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                                '.strtoupper($visi['deskripsi']).'
                                                <br>
                                                <span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi</span>
                                            </td>
                                            <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="hiddenRow">
                                                <div class="collapse show" id="sub_kegiatan_visi'.$visi['id'].'">
                                                    <table class="table table-condensed table-striped">
                                                        <tbody>';
                                                        $get_misis = Misi::where('visi_id', $visi['id'])->whereHas('tujuan', function($q){
                                                            $q->whereHas('sasaran', function($q){
                                                                $q->whereHas('sasaran_indikator_kinerja', function($q){
                                                                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                        $q->whereHas('program_rpjmd', function($q){
                                                                            $q->whereHas('program', function($q){
                                                                                $q->whereHas('program_indikator_kinerja', function($q){
                                                                                    $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                    });
                                                                                });
                                                                            });
                                                                        });
                                                                    });
                                                                });
                                                            });
                                                        })->get();
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
                                                        foreach ($misis as $misi)
                                                        {
                                                            $html .= '<tr style="background: #c04141;">
                                                                        <td width="5%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                        <td width="55%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                            '.strtoupper($misi['deskripsi']).'
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
                                                                        <td width="40%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_misi'.$misi['id'].'" class="accordion-toggle text-white"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="4" class="hiddenRow">
                                                                            <div class="collapse show" id="sub_kegiatan_misi'.$misi['id'].'">
                                                                                <table class="table table-condensed table-striped">
                                                                                    <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->whereHas('sasaran', function($q){
                                                                                        $q->whereHas('sasaran_indikator_kinerja', function($q){
                                                                                            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                $q->whereHas('program_rpjmd', function($q){
                                                                                                    $q->whereHas('program', function($q){
                                                                                                        $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                            });
                                                                                                        });
                                                                                                    });
                                                                                                });
                                                                                            });
                                                                                        });
                                                                                    })->get();
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
                                                                                    foreach ($tujuans as $tujuan)
                                                                                    {
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="55%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="40%"></td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse show" id="sub_kegiatan_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-condensed table-striped">
                                                                                                                <tbody>';
                                                                                                                $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->whereHas('sasaran_indikator_kinerja', function($q){
                                                                                                                    $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                                        $q->whereHas('program_rpjmd', function($q){
                                                                                                                            $q->whereHas('program', function($q){
                                                                                                                                $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                                                    $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                    });
                                                                                                                                });
                                                                                                                            });
                                                                                                                        });
                                                                                                                    });
                                                                                                                })->get();
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
                                                                                                                foreach ($sasarans as $sasaran)
                                                                                                                {
                                                                                                                    $html .= '<tr style="background:#41c081">
                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="55%">
                                                                                                                                        '.strtoupper($sasaran['deskripsi']).'
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
                                                                                                                                </td>';
                                                                                                                                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                                                                                                                                    $q->whereHas('program_rpjmd', function($q){
                                                                                                                                        $q->whereHas('program', function($q){
                                                                                                                                            $q->whereHas('program_indikator_kinerja', function($q){
                                                                                                                                                $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                });
                                                                                                                                            });
                                                                                                                                        });
                                                                                                                                    });
                                                                                                                                })->get();
                                                                                                                                $html .= '<td width="40%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sasaran_'.$sasaran['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                    foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                    {
                                                                                                                                        $html .= '<li class="mb-2 text-white">'.strtoupper($sasaran_indikator_kinerja->deskripsi).'</li>';
                                                                                                                                    }
                                                                                                                                $html .= '</ul></td>';
                                                                                                                    $html .= '</tr>
                                                                                                                    <tr>
                                                                                                                        <td colspan="4" class="hiddenRow">
                                                                                                                            <div class="collapse show" id="sub_kegiatan_sasaran_'.$sasaran['id'].'">
                                                                                                                                <table class="table table-condensed table-striped">
                                                                                                                                    <tbody>';
                                                                                                                                    $get_programs = Program::whereHas('program_rpjmd', function($q) use ($sasaran){
                                                                                                                                        $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran) {
                                                                                                                                            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($sasaran){
                                                                                                                                                $q->whereHas('sasaran', function($q) use ($sasaran) {
                                                                                                                                                    $q->where('id', $sasaran['id']);
                                                                                                                                                });
                                                                                                                                            });
                                                                                                                                        });
                                                                                                                                    })->whereHas('program_indikator_kinerja', function($q){
                                                                                                                                        $q->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                        });
                                                                                                                                    })->get();
                                                                                                                                    $programs = [];
                                                                                                                                    foreach($get_programs as $get_program)
                                                                                                                                    {
                                                                                                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                                                                                                                                        if($cek_perubahan_program)
                                                                                                                                        {
                                                                                                                                            $programs[] = [
                                                                                                                                                'id' => $cek_perubahan_program->program_id,
                                                                                                                                                'kode' => $cek_perubahan_program->kode,
                                                                                                                                                'deskripsi' => $cek_perubahan_program->deskripsi,
                                                                                                                                                'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan
                                                                                                                                            ];
                                                                                                                                        } else {
                                                                                                                                            $programs[] = [
                                                                                                                                                'id' => $get_program->id,
                                                                                                                                                'kode' => $get_program->kode,
                                                                                                                                                'deskripsi' => $get_program->deskripsi,
                                                                                                                                                'tahun_perubahan' => $get_program->tahun_perubahan
                                                                                                                                            ];
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                    foreach($programs as $program)
                                                                                                                                    {
                                                                                                                                        $html .= '<tr style="background:#bbae7f;">';
                                                                                                                                            $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program_'.$program['id'].'" class="accordion-toggle text-white">'.$program['kode'].'</td>';
                                                                                                                                            $html .= '<td width="55%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program_'.$program['id'].'" class="accordion-toggle text-white">'.strtoupper($program['deskripsi']);
                                                                                                                                            $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->where('status_program', 'Prioritas')->first();
                                                                                                                                            if($cek_program_rjmd)
                                                                                                                                            {
                                                                                                                                                $html .= '<i class="fas fa-star text-primary" title="Program Prioritas"> </i>';
                                                                                                                                            }
                                                                                                                                            $html .= '<br>';
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
                                                                                                                                            <span class="badge bg-dark text-uppercase renstra-kegiatan-tagging">Program '.$program['kode'].'</span></td>';
                                                                                                                                            $html .= '<td width="40%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program_'.$program['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])
                                                                                                                                                                                ->whereHas('opd_program_indikator_kinerja', function($q){
                                                                                                                                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                                                })->get();
                                                                                                                                                foreach($program_indikator_kinerjas as $program_indikator_kinerja)
                                                                                                                                                {
                                                                                                                                                    $html .= '<li class="text-white">'.strtoupper($program_indikator_kinerja->deskripsi).'</li>';
                                                                                                                                                }
                                                                                                                                            $html .= '</ul></td>';
                                                                                                                                        $html .= '</tr>
                                                                                                                                        <tr>
                                                                                                                                            <td colspan="4" class="hiddenRow">
                                                                                                                                                <div class="collapse show" id="sub_kegiatan_program_'.$program['id'].'">
                                                                                                                                                    <table class="table table-condensed table-striped">
                                                                                                                                                        <tbody>';
                                                                                                                                                        $get_kegiatans = Kegiatan::where('program_id', $program['id'])->whereHas('kegiatan_indikator_kinerja', function($q){
                                                                                                                                                            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                                                                                                                $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                            });
                                                                                                                                                        })->get();
                                                                                                                                                        $kegiatans = [];
                                                                                                                                                        foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                                                                            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                                                                                                                                                                        ->latest()->first();
                                                                                                                                                            if($cek_perubahan_kegiatan)
                                                                                                                                                            {
                                                                                                                                                                $kegiatans[] = [
                                                                                                                                                                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                                                                    'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                                                                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                                                                    'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                                                                                                                                                                ];
                                                                                                                                                            } else {
                                                                                                                                                                $kegiatans[] = [
                                                                                                                                                                    'id' => $get_kegiatan->id,
                                                                                                                                                                    'kode' => $get_kegiatan->kode,
                                                                                                                                                                    'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                                                                                    'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                                                                                                                                                                ];
                                                                                                                                                            }
                                                                                                                                                        }
                                                                                                                                                        foreach($kegiatans as $kegiatan)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr style="background: #41c0c0">';
                                                                                                                                                                $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle text-white">'.$program['kode'].'.'.$kegiatan['kode'].'</td>';
                                                                                                                                                                $html .= '<td width="55%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle text-white">'.strtoupper($kegiatan['deskripsi']);
                                                                                                                                                                $html .= '<br>';
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
                                                                                                                                                                <span class="badge bg-dark text-uppercase renstra-kegiatan-tagging">Program '.$program['kode'].'</span>
                                                                                                                                                                <span class="badge bg-success text-uppercase renstra-kegiatan-tagging">Kegiatan '.$program['kode'].'.'.$kegiatan['kode'].'</span></td>';
                                                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                                                                                                                                                                ->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                                                                                                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                                                                })->get();
                                                                                                                                                                $html .= '<td width="40%"><ul>';
                                                                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                                                        if($kegiatan_indikator_kinerja->status_indikator == 'Target NSPK')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<li class="text-white">'.$kegiatan_indikator_kinerja->deskripsi.' (<i class="fas fa-n text-white ml-1" title="Target NSPK"></i>)'.'</li>';
                                                                                                                                                                        }
                                                                                                                                                                        if($kegiatan_indikator_kinerja->status_indikator == 'Target IKK')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<li class="text-white">'.$kegiatan_indikator_kinerja->deskripsi.' (<i class="fas fa-i text-white ml-1" title="Target IKK"></i>)'.'</li>';
                                                                                                                                                                        }
                                                                                                                                                                        if($kegiatan_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                                                                                                                                                        {
                                                                                                                                                                            $html .= '<li class="text-white">'.$kegiatan_indikator_kinerja->deskripsi.' (<i class="fas fa-t text-white ml-1" title="Target Indikator Lainnya"></i>)'.'</li>';
                                                                                                                                                                        }
                                                                                                                                                                    }
                                                                                                                                                                $html .='</ul></td>';
                                                                                                                                                            $html .= '</tr>
                                                                                                                                                            <tr>
                                                                                                                                                                <td colspan="4" class="hiddenRow">
                                                                                                                                                                    <div class="collapse show" id="sub_kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                                                                        <table class="table table-condensed table-striped">
                                                                                                                                                                            <tbody>';
                                                                                                                                                                            $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan['id'])->orderBy('kode', 'asc')->get();
                                                                                                                                                                            $sub_kegiatans = [];
                                                                                                                                                                            foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                                                                                                                                                                $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)->orderBy('tahun_perubahan', 'asc')->latest()->first();
                                                                                                                                                                                if($cek_perubahan_sub_kegiatan)
                                                                                                                                                                                {
                                                                                                                                                                                    $sub_kegiatans[] = [
                                                                                                                                                                                        'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                                                                                                                                                                                        'kegiatan_id' => $cek_perubahan_sub_kegiatan->kegiatan_id,
                                                                                                                                                                                        'kode' => $cek_perubahan_sub_kegiatan->kode,
                                                                                                                                                                                        'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi,
                                                                                                                                                                                        'tahun_perubahan' => $cek_perubahan_sub_kegiatan->tahun_perubahan,
                                                                                                                                                                                    ];
                                                                                                                                                                                } else {
                                                                                                                                                                                    $sub_kegiatans[] = [
                                                                                                                                                                                        'id' => $get_sub_kegiatan->id,
                                                                                                                                                                                        'kegiatan_id' => $get_sub_kegiatan->kegiatan_id,
                                                                                                                                                                                        'kode' => $get_sub_kegiatan->kode,
                                                                                                                                                                                        'deskripsi' => $get_sub_kegiatan->deskripsi,
                                                                                                                                                                                        'tahun_perubahan' => $get_sub_kegiatan->tahun_perubahan,
                                                                                                                                                                                    ];
                                                                                                                                                                                }
                                                                                                                                                                            }
                                                                                                                                                                            foreach ($sub_kegiatans as $sub_kegiatan)
                                                                                                                                                                            {
                                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                                    $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan_'.$sub_kegiatan['id'].'" class="accordion-toggle">'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'</td>';
                                                                                                                                                                                    $html .= '<td width="55%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan_'.$sub_kegiatan['id'].'" class="accordion-toggle">'.$sub_kegiatan['deskripsi'];
                                                                                                                                                                                    $html .= '<br>';
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
                                                                                                                                                                                    <span class="badge bg-dark text-uppercase renstra-kegiatan-tagging">Program '.$program['kode'].'</span>
                                                                                                                                                                                    <span class="badge bg-success text-uppercase renstra-kegiatan-tagging">Kegiatan '.$program['kode'].'.'.$kegiatan['kode'].'</span>
                                                                                                                                                                                    <span class="badge bg-quaternary text-uppercase renstra-kegiatan-tagging">Sub Kegiatan '.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'</span></td>';
                                                                                                                                                                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                                                    $html .= '<td width="30%"><table class="table table-bordered">
                                                                                                                                                                                                <tbody>';
                                                                                                                                                                                                foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                                        $html .= '<td width="75%">'.$sub_kegiatan_indikator_kinerja->deskripsi;
                                                                                                                                                                                                        if($sub_kegiatan_indikator_kinerja->status_indikator == 'Target NSPK')
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<i class="fas fa-n text-white ml-1" title="Target NSPK">';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if($sub_kegiatan_indikator_kinerja->status_indikator == 'Target IKK')
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<i class="fas fa-i text-white ml-1" title="Target IKK">';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if($sub_kegiatan_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<i class="fas fa-t text-white ml-1" title="Target Indikator Lainnya">';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '</td>';
                                                                                                                                                                                                        $html .= '<td width="25%">
                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sub-kegiatan-indikator-kinerja mr-1" data-id="'.$sub_kegiatan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sub Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sub-kegiatan-indikator-kinerja" type="button" title="Hapus Indikator" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-sub-kegiatan-indikator-kinerja-id="'.$sub_kegiatan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $html .= '</tbody>
                                                                                                                                                                                            </table></td>';
                                                                                                                                                                                    $html .= '<td width="10%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan_'.$sub_kegiatan['id'].'" class="accordion-toggle">
                                                                                                                                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sub-kegiatan-indikator-kinerja" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" type="button" title="Tambah Indikator Kinerja Sub Kegiatan"><i class="fas fa-lock"></i></button>
                                                                                                                                                                                            </td>';
                                                                                                                                                                                $html .= '</tr>
                                                                                                                                                                                <tr>
                                                                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                                                                        <div class="collapse accordion-body" id="sub_kegiatan_sub_kegiatan_'.$sub_kegiatan['id'].'">
                                                                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                                                                <thead>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <th>No</th>
                                                                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                                                                        <th>Target Kinerja</th>
                                                                                                                                                                                                        <th>Satuan</th>
                                                                                                                                                                                                        <th>Target Anggaran Awal</th>
                                                                                                                                                                                                        <th>Target Anggaran Perubahan</th>
                                                                                                                                                                                                        <th>Tahun</th>
                                                                                                                                                                                                        <th>Aksi</th>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                </thead>
                                                                                                                                                                                                <tbody>';
                                                                                                                                                                                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                                                                    $no_sub_kegiatan_indikator_kinerja = 1;
                                                                                                                                                                                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                            $html .= '<td>'.$no_sub_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                                                                                            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                                            $b = 1;
                                                                                                                                                                                                            foreach($tahuns as $tahun)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja){
                                                                                                                                                                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                                                                                                                                    $q->where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id);
                                                                                                                                                                                                                })->where('tahun', $tahun)->first();

                                                                                                                                                                                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    if($b == 1)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                            $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                                                                                                                            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                            $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target-anggaran-renja-awal data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-target-anggaran-renja-awal="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_awal.'">Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_awal,2).'</span></td>';
                                                                                                                                                                                                                            $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target-anggaran-renja-perubahan data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-target-anggaran-renja-perubahan="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan.'">Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan,2).'</span></td>';
                                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mr-2 button-sub-kegiatan-edit-target-satuan-rp-realisasi"
                                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                                data-sub-kegiatan-indikator-kinerja-id="'.$sub_kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                                                                data-tahun="'.$tahun.'"
                                                                                                                                                                                                                                                data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-sub-kegiatan-tw-realisasi '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                                            data-tahun="'.$tahun.'"
                                                                                                                                                                                                                                            data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                                            value="close"
                                                                                                                                                                                                                                            data-bs-toggle="collapse"
                                                                                                                                                                                                                                            data-bs-target="#sub_kegiatan_indikator_'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                                            class="accordion-toggle">
                                                                                                                                                                                                                                                <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                        $html .='</tr>
                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                            <td colspan="10" class="hiddenRow">
                                                                                                                                                                                                                                <div class="collapse accordion-body" id="sub_kegiatan_indikator_'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                                                                                                        <thead>
                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                <th width="15%">Target Kinerja</th>
                                                                                                                                                                                                                                                <th width="15%">Satuan</th>
                                                                                                                                                                                                                                                <th width="15%">Tahun</th>
                                                                                                                                                                                                                                                <th width="15%">TW</th>
                                                                                                                                                                                                                                                <th width="15%">Realisasi Kinerja</th>
                                                                                                                                                                                                                                                <th width="15%">Realisasi Anggaran</th>
                                                                                                                                                                                                                                                <th width="10%">Aksi</th>
                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                        </thead>
                                                                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                                                                $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                                                                $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                                                $c = 1;
                                                                                                                                                                                                                                                foreach($tws as $tw)
                                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                                                                                    {
                                                                                                                                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                                            $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                                            if($cek_sub_kegiatan_tw_realisasi_renja)
                                                                                                                                                                                                                                                            {
                                                                                                                                                                                                                                                                $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                                                $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                                                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                                                                data-tw-id = "'.$tw->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                                                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                                                                data-tw-id = "'.$tw->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                                            $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                                            if($cek_sub_kegiatan_tw_realisasi_renja)
                                                                                                                                                                                                                                                            {
                                                                                                                                                                                                                                                                $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                                                $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                                                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                                                                data-tw-id = "'.$tw->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                                                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                                                                data-tw-id = "'.$tw->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    $c++;
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                            $html .= '</tr>';
                                                                                                                                                                                                                                        $html .= '</tbody>
                                                                                                                                                                                                                                    </table>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                        </tr>';
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                            $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                                                                                                                            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                            $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target-anggaran-renja-awal data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-target-anggaran-renja-awal="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_awal.'">Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_awal,2).'</span></td>';
                                                                                                                                                                                                                            $html .= '<td><span class="span-sub-kegiatan-indikator-kinerja-target-anggaran-renja-perubahan data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-target-anggaran-renja-perubahan="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan.'">Rp. '.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan,2).'</span></td>';
                                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                                                                        <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mr-2 button-sub-kegiatan-edit-target-satuan-rp-realisasi"
                                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                                data-sub-kegiatan-indikator-kinerja-id="'.$sub_kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                                                                data-tahun="'.$tahun.'"
                                                                                                                                                                                                                                                data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-sub-kegiatan-tw-realisasi '.$tahun.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                                            data-tahun="'.$tahun.'"
                                                                                                                                                                                                                                            data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                                            value="close"
                                                                                                                                                                                                                                            data-bs-toggle="collapse"
                                                                                                                                                                                                                                            data-bs-target="#sub_kegiatan_indikator_'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                                            class="accordion-toggle">
                                                                                                                                                                                                                                                <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                        $html .='</tr>
                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                            <td colspan="10" class="hiddenRow">
                                                                                                                                                                                                                                <div class="collapse accordion-body" id="sub_kegiatan_indikator_'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                                                                                                        <thead>
                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                <th width="15%">Target Kinerja</th>
                                                                                                                                                                                                                                                <th width="15%">Satuan</th>
                                                                                                                                                                                                                                                <th width="15%">Tahun</th>
                                                                                                                                                                                                                                                <th width="15%">TW</th>
                                                                                                                                                                                                                                                <th width="15%">Realisasi Kinerja</th>
                                                                                                                                                                                                                                                <th width="15%">Realisasi Anggaran</th>
                                                                                                                                                                                                                                                <th width="10%">Aksi</th>
                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                        </thead>
                                                                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                                                                $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                                                                $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                                                $c = 1;
                                                                                                                                                                                                                                                foreach($tws as $tw)
                                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                                                                                    {
                                                                                                                                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                                            $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                                            if($cek_sub_kegiatan_tw_realisasi_renja)
                                                                                                                                                                                                                                                            {
                                                                                                                                                                                                                                                                $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                                                $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                                                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                                                                data-tw-id = "'.$tw->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                                                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                                                                data-tw-id = "'.$tw->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                                                                                                                                            $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                                                                                                                                                                                            if($cek_sub_kegiatan_tw_realisasi_renja)
                                                                                                                                                                                                                                                            {
                                                                                                                                                                                                                                                                $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'">'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                                                                                                                                                                                                                $html .= '<td><span class="span-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-renja-'.$cek_sub_kegiatan_tw_realisasi_renja->id.'" data-realisasi-rp="'.$cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp.'">Rp. '.number_format($cek_sub_kegiatan_tw_realisasi_renja->realisasi_rp, 2).'</span></td>';
                                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sub-kegiatan-tw-realisasi"
                                                                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                                                                data-tw-id = "'.$tw->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-tw-realisasi-renja-id="'.$cek_sub_kegiatan_tw_realisasi_renja->id.'">
                                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                                $html .= '<td><input type="number" class="form-control input-sub-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sub-kegiatan-tw-realisasi"
                                                                                                                                                                                                                                                                                type="button"
                                                                                                                                                                                                                                                                                data-tw-id = "'.$tw->id.'"
                                                                                                                                                                                                                                                                                data-sub-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'">
                                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                        </td>';
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    $c++;
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                            $html .= '</tr>';
                                                                                                                                                                                                                                        $html .= '</tbody>
                                                                                                                                                                                                                                    </table>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                        </tr>';
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    $b++;
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    if($b == 1)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.'"></td>';
                                                                                                                                                                                                                            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target-anggaran-renja-awal data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.'"></td>';
                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target-anggaran-renja-perubahan data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.'"></td>';
                                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mr-2 button-add-sub-kegiatan-target-satuan-rp-realisasi"
                                                                                                                                                                                                                                            type="button"
                                                                                                                                                                                                                                            data-sub-kegiatan-indikator-kinerja-id="'.$sub_kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                                                            data-tahun="'.$tahun.'">
                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                        <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
                                                                                                                                                                                                                                    </td>';
                                                                                                                                                                                                                        $html .='</tr>';
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.'"></td>';
                                                                                                                                                                                                                            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target-anggaran-renja-awal data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.'"></td>';
                                                                                                                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target-anggaran-renja-perubahan data-sub-kegiatan-indikator-kinerja-'.$sub_kegiatan_indikator_kinerja->id.' '.$tahun.'"></td>';
                                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mr-2 button-add-sub-kegiatan-target-satuan-rp-realisasi"
                                                                                                                                                                                                                                            type="button"
                                                                                                                                                                                                                                            data-sub-kegiatan-indikator-kinerja-id="'.$sub_kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                                                            data-tahun="'.$tahun.'">
                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                        <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
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
                </div>';

        return response()->json(['html' => $html]);
    }
}
