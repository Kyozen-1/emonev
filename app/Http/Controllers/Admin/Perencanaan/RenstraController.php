<?php

namespace App\Http\Controllers\Admin\Perencanaan;

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
use App\Models\TargetRpPertahunRenstraKegiatan;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\TujuanIndikatorKinerja;
use App\Models\TujuanTargetSatuanRpRealisasi;
use App\Models\SasaranIndikatorKinerja;
use App\Models\SasaranTargetSatuanRpRealisasi;
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

class RenstraController extends Controller
{
    public function renstra_get_tujuan(Request $request)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal-1;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

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
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse show" id="tujuan_visi'.$visi['id'].'">
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
                                                    $html .= '<tr style="background: #c04141;">
                                                        <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                        <td width="95%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                            '.strtoupper($misi['deskripsi']).'
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
                                                            <div class="collapse show" id="tujuan_misi'.$misi['id'].'">
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
                                                                            <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                            <td width="95%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
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
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                                                                                    <table class="table table-bordered">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th width="20%">OPD</th>
                                                                                                <th width="2%">Kode</th>
                                                                                                <th width="18%">Tujuan PD</th>
                                                                                                <th width="20%">Indikator</th>
                                                                                                <th width="10%">Target</th>
                                                                                                <th width="10%">Satuan</th>
                                                                                                <th width="10%">Realisasi</th>
                                                                                                <th width="10%">Tahun</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                        $opds = MasterOpd::all();
                                                                                        foreach ($opds as $opd) {
                                                                                            $html .= '<tr>';
                                                                                                $html .= '<td>'.$opd->nama.'</td>';
                                                                                                $tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])
                                                                                                                ->where('opd_id', $opd->id)->get();
                                                                                                $a = 1;
                                                                                                foreach ($tujuan_pds as $tujuan_pd) {
                                                                                                    if($a == 1)
                                                                                                    {
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $b = 1;
                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                            {
                                                                                                                if($b == 1)
                                                                                                                {
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                                $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                } else {
                                                                                                                    $html .= '<tr>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                            $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                }
                                                                                                            }
                                                                                                    } else {
                                                                                                        $html .= '<tr>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $b = 1;
                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                            {
                                                                                                                if($b == 1)
                                                                                                                {
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                                $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                } else {
                                                                                                                    $html .= '<tr>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                            $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                }
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

    public function renstra_get_tujuan_tahun($tahun)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal-1;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

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
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse show" id="tujuan_visi'.$visi['id'].'">
                                            <table class="table table-striped table-condesed">
                                                <tbody>';
                                                $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                $misis = [];
                                                foreach ($get_misis as $get_misi) {
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
                                                            <div class="collapse show" id="tujuan_misi'.$misi['id'].'">
                                                                <table class="table table-striped table-condesed">
                                                                    <tbody>';
                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->orderBy('kode', 'asc')->get();
                                                                    $tujuans = [];
                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                                                                                    ->where('tahun_perubahan', $tahun)
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
                                                                            <td width="95%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
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
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                                                                                    <table class="table table-bordered">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th width="20%">OPD</th>
                                                                                                <th width="2%">Kode</th>
                                                                                                <th width="18%">Tujuan PD</th>
                                                                                                <th width="20%">Indikator</th>
                                                                                                <th width="10%">Target</th>
                                                                                                <th width="10%">Satuan</th>
                                                                                                <th width="10%">Realisasi</th>
                                                                                                <th width="10%">Tahun</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                        $opds = MasterOpd::all();
                                                                                        foreach ($opds as $opd) {
                                                                                            $html .= '<tr>';
                                                                                                $html .= '<td>'.$opd->nama.'</td>';
                                                                                                $tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])
                                                                                                                ->where('opd_id', $opd->id)->get();
                                                                                                $a = 1;
                                                                                                foreach ($tujuan_pds as $tujuan_pd) {
                                                                                                    if($a == 1)
                                                                                                    {
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $b = 1;
                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                            {
                                                                                                                if($b == 1)
                                                                                                                {
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                                $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                } else {
                                                                                                                    $html .= '<tr>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                            $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                }
                                                                                                            }
                                                                                                    } else {
                                                                                                        $html .= '<tr>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $b = 1;
                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                            {
                                                                                                                if($b == 1)
                                                                                                                {
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                                $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                } else {
                                                                                                                    $html .= '<tr>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                            $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                }
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
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal-1;
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
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
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
                                                            $html .= '<span class="badge bg-primary text-uppercase tujuan-renstra-tagging">Visi '.$request->visi.'</span>';
                                                            $html .= ' <span class="badge bg-warning text-uppercase tujuan-renstra-tagging">Misi '.$misi['kode'].' </span>
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
                                                                            <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                            <td width="95%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
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
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                                                                                    <table class="table table-bordered">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th width="20%">OPD</th>
                                                                                                <th width="2%">Kode</th>
                                                                                                <th width="18%">Tujuan PD</th>
                                                                                                <th width="20%">Indikator</th>
                                                                                                <th width="10%">Target</th>
                                                                                                <th width="10%">Satuan</th>
                                                                                                <th width="10%">Realisasi</th>
                                                                                                <th width="10%">Tahun</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                        $opds = MasterOpd::all();
                                                                                        foreach ($opds as $opd) {
                                                                                            $html .= '<tr>';
                                                                                                $html .= '<td>'.$opd->nama.'</td>';
                                                                                                $tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])
                                                                                                                ->where('opd_id', $opd->id)->get();
                                                                                                $a = 1;
                                                                                                foreach ($tujuan_pds as $tujuan_pd) {
                                                                                                    if($a == 1)
                                                                                                    {
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $b = 1;
                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                            {
                                                                                                                if($b == 1)
                                                                                                                {
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                                $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                } else {
                                                                                                                    $html .= '<tr>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                            $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                }
                                                                                                            }
                                                                                                    } else {
                                                                                                        $html .= '<tr>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $b = 1;
                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                            {
                                                                                                                if($b == 1)
                                                                                                                {
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                                $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                } else {
                                                                                                                    $html .= '<tr>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td></td>';
                                                                                                                        $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                        $c = 1;
                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                        {
                                                                                                                            $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)->first();
                                                                                                                            if($c == 1)
                                                                                                                            {
                                                                                                                                if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                {
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                } else {
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                }
                                                                                                                            $html .= '</tr>';
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    $html .= '<td></td>';
                                                                                                                                    if($tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                    {
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                        $html .= '<td>'.$tujuan_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    } else {
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                    }
                                                                                                                                $html .= '</tr>';
                                                                                                                            }
                                                                                                                            $c++;
                                                                                                                        }
                                                                                                                }
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
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal-1;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $get_visis = Visi::all();
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $tahun_awal)
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
                                    <th width="95%">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($visis as $visi) {
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse show" id="sasaran_visi'.$visi['id'].'">
                                            <table class="table table-striped table-condensed">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                    $misis = [];
                                                    foreach ($get_misis as $get_misi) {
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->where('tahun_perubahan', $tahun_awal)
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
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                        '.strtoupper($misi['deskripsi']).'
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
                                                                        <div class="collapse show" id="sasaran_misi'.$misi['id'].'">
                                                                            <table class="table table-striped table-condensed">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan',$tahun_awal)
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
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                        <div class="collapse show" id="sasaran_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-striped table-condensed">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $tahun_awal)
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
                                                                                                                                        <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-bordered">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="20%">OPD</th>
                                                                                                                                                        <th width="2%">Kode</th>
                                                                                                                                                        <th width="18%">Tujuan PD</th>
                                                                                                                                                        <th width="20%">Indikator</th>
                                                                                                                                                        <th width="10%">Target</th>
                                                                                                                                                        <th width="10%">Satuan</th>
                                                                                                                                                        <th width="10%">Realisasi</th>
                                                                                                                                                        <th width="10%">Tahun</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $opds = MasterOpd::all();
                                                                                                                                                foreach ($opds as $opd) {
                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                        $html .= '<td>'.$opd->nama.'</td>';
                                                                                                                                                        $sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])
                                                                                                                                                                        ->where('opd_id', $opd->id)->get();
                                                                                                                                                        $a = 1;
                                                                                                                                                        foreach ($sasaran_pds as $sasaran_pd) {
                                                                                                                                                            if($a == 1)
                                                                                                                                                            {
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                    $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                    $b = 1;
                                                                                                                                                                    foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                    {
                                                                                                                                                                        if($b == 1)
                                                                                                                                                                        {
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        } else {
                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        }
                                                                                                                                                                    }
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                    $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                    $b = 1;
                                                                                                                                                                    foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                    {
                                                                                                                                                                        if($b == 1)
                                                                                                                                                                        {
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        } else {
                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        }
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

    public function renstra_get_sasaran_tahun($tahun)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal-1;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::all();
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
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

        $html = '<div class="data-table-rows slim" id="sasaran_div_table">
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
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle  text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase sasaran-renstra-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse show" id="sasaran_visi'.$visi['id'].'">
                                            <table class="table table-striped table-condensed">
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
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                        '.strtoupper($misi['deskripsi']).'
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
                                                                        <div class="collapse show" id="sasaran_misi'.$misi['id'].'">
                                                                            <table class="table table-striped table-condensed">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan',$tahun)
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
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                        <div class="collapse show" id="sasaran_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-striped table-condensed">
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
                                                                                                                                        <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-bordered">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="20%">OPD</th>
                                                                                                                                                        <th width="2%">Kode</th>
                                                                                                                                                        <th width="18%">Tujuan PD</th>
                                                                                                                                                        <th width="20%">Indikator</th>
                                                                                                                                                        <th width="10%">Target</th>
                                                                                                                                                        <th width="10%">Satuan</th>
                                                                                                                                                        <th width="10%">Realisasi</th>
                                                                                                                                                        <th width="10%">Tahun</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $opds = MasterOpd::all();
                                                                                                                                                foreach ($opds as $opd) {
                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                        $html .= '<td>'.$opd->nama.'</td>';
                                                                                                                                                        $sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])
                                                                                                                                                                        ->where('opd_id', $opd->id)->get();
                                                                                                                                                        $a = 1;
                                                                                                                                                        foreach ($sasaran_pds as $sasaran_pd) {
                                                                                                                                                            if($a == 1)
                                                                                                                                                            {
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                    $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                    $b = 1;
                                                                                                                                                                    foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                    {
                                                                                                                                                                        if($b == 1)
                                                                                                                                                                        {
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        } else {
                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        }
                                                                                                                                                                    }
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                    $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                    $b = 1;
                                                                                                                                                                    foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                    {
                                                                                                                                                                        if($b == 1)
                                                                                                                                                                        {
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        } else {
                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        }
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
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal-1;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

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

        $html = '<div class="data-table-rows slim" id="sasaran_div_table">
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
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
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
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle text-white">
                                                                        '.strtoupper($misi['deskripsi']).'
                                                                        <br>';
                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-sasaran-tagging">'.$misi['kode'].' Misi</span>
                                                                    </td>
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
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
                                                                                                        <br>';
                                                                                                        $html .= '<span class="badge bg-primary text-uppercase renstra-sasaran-tagging">Visi '.$request->visi.'</span>';
                                                                                                        $html .= ' <span class="badge bg-warning text-uppercase renstra-sasaran-tagging">Misi '.$misi['kode'].'</span>
                                                                                                        <span class="badge bg-secondary text-uppercase renstra-sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
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
                                                                                                                                        <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                                                                                                                            <table class="table table-bordered">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="20%">OPD</th>
                                                                                                                                                        <th width="2%">Kode</th>
                                                                                                                                                        <th width="18%">Tujuan PD</th>
                                                                                                                                                        <th width="20%">Indikator</th>
                                                                                                                                                        <th width="10%">Target</th>
                                                                                                                                                        <th width="10%">Satuan</th>
                                                                                                                                                        <th width="10%">Realisasi</th>
                                                                                                                                                        <th width="10%">Tahun</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $opds = MasterOpd::all();
                                                                                                                                                foreach ($opds as $opd) {
                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                        $html .= '<td>'.$opd->nama.'</td>';
                                                                                                                                                        $sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])
                                                                                                                                                                        ->where('opd_id', $opd->id)->get();
                                                                                                                                                        $a = 1;
                                                                                                                                                        foreach ($sasaran_pds as $sasaran_pd) {
                                                                                                                                                            if($a == 1)
                                                                                                                                                            {
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                    $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                    $b = 1;
                                                                                                                                                                    foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                    {
                                                                                                                                                                        if($b == 1)
                                                                                                                                                                        {
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        } else {
                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        }
                                                                                                                                                                    }
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                    $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                    $b = 1;
                                                                                                                                                                    foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                    {
                                                                                                                                                                        if($b == 1)
                                                                                                                                                                        {
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        } else {
                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                $c = 1;
                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                {
                                                                                                                                                                                    $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                                                                                                                                                            ->where('tahun', $tahun)->first();
                                                                                                                                                                                    if($c == 1)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                        }
                                                                                                                                                                                    $html .= '</tr>';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                            if($sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$sasaran_pd_target_satuan_rp_realisasi->realisasi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                            }
                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $c++;
                                                                                                                                                                                }
                                                                                                                                                                        }
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

        $html = '<div class="data-table-rows slim" id="program_div_table">
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

    public function renstra_get_program_tahun($tahun)
    {
        $get_visis = Visi::all();
        $visis = [];
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
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
                                                                                                                                                                                                                    ->where('tahun_perubahan', $tahun)->latest()->first();
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
                                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_target_rps)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $get_target_rps = TargetRpPertahunProgram::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                                    ->where('tahun', $tahun)
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
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan',$request->tahun)
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
                                                                                                                                                                                                                    ->where('tahun_perubahan', $request->tahun)->latest()->first();
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
                                                                                                                                                                                                                    ->where('tahun', $request->tahun)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_target_rps)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $get_target_rps = TargetRpPertahunProgram::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                                    ->where('tahun', $request->tahun)
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
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $get_visis = Visi::all();
        $visis = [];
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $tahun_awal)
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
                                                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->where('tahun_perubahan', $tahun_awal)
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
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan', $tahun_awal)
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
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $tahun_awal)
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
                                                                                                                                                                                                                    ->where('tahun_perubahan', $tahun_awal)->latest()->first();
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
                                                                                                                                                                                                                    ->where('tahun', $tahun_awal)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_target_rps)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $get_target_rps = TargetRpPertahunProgram::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                                    ->where('tahun', $tahun_awal)
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
                                                                                                                                                                                                                                            ->where('tahun_perubahan', $tahun_awal)
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
                                                                                                                                                                                                                    ->where('tahun', $tahun_awal)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                                    if($cek_target_rp_pertahun_renstra_kegiatan)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        $get_target_rp_pertahun_renstra_kegiatans = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                                                                                                                                                                                                        ->where('tahun', $tahun_awal)
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

    public function renstra_get_kegiatan_tahun($tahun)
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
                                                                                                                                                                                                                    ->where('tahun_perubahan', $tahun)->latest()->first();
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
                                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_target_rps)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $get_target_rps = TargetRpPertahunProgram::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                                    ->where('tahun', $tahun)
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
                                                                                                                                                                                                                                            ->where('tahun_perubahan', $tahun)
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
                                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                                    if($cek_target_rp_pertahun_renstra_kegiatan)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        $get_target_rp_pertahun_renstra_kegiatans = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                                                                                                                                                                                                        ->where('tahun', $tahun)
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

    public function renstra_filter_kegiatan(Request $request)
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
                                                                                                                                                                                                                    ->where('tahun_perubahan', $request->tahun)->latest()->first();
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
                                                                                                                                                                                                                    ->where('tahun', $request->tahun)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_target_rps)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $get_target_rps = TargetRpPertahunProgram::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                                    ->where('tahun', $request->tahun)
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
                                                                                                                                                                                                                                            ->where('tahun_perubahan', $request->tahun)
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
                                                                                                                                                                                                                    ->where('tahun', $request->tahun)
                                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                                    if($cek_target_rp_pertahun_renstra_kegiatan)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        $get_target_rp_pertahun_renstra_kegiatans = TargetRpPertahunRenstraKegiatan::where('renstra_kegiatan_id', $kegiatan['renstra_kegiatan_id'])
                                                                                                                                                                                                                                        ->where('tahun', $request->tahun)
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
