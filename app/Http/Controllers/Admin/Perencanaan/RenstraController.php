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
use App\Models\TujuanPdRealisasiRenja;
use App\Models\SasaranPd;
use App\Models\PivotPerubahanSasaranPd;
use App\Models\SasaranPdIndikatorKinerja;
use App\Models\SasaranPdTargetSatuanRpRealisasi;
use App\Models\ProgramIndikatorKinerja;
use App\Models\OpdProgramIndikatorKinerja;
use App\Models\ProgramTargetSatuanRpRealisasi;
use App\Models\KegiatanIndikatorKinerja;
use App\Models\KegiatanTargetSatuanRpRealisasi;
use App\Models\OpdKegiatanIndikatorKinerja;
use App\Models\SasaranPdRealisasiRenja;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanTwRealisasi;

class RenstraController extends Controller
{
    public function renstra_get_tujuan(Request $request)
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
                                                                                    <table class="table table-striped table-condesed">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>OPD</th>
                                                                                                <th>Kode</th>
                                                                                                <th>Tujuan PD</th>
                                                                                                <th>Indikator</th>
                                                                                                <th>Aksi</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                        $opds = MasterOpd::all();
                                                                                        foreach ($opds as $opd) {
                                                                                            $html .= '<tr>';
                                                                                                $html .= '<td>'.$opd->nama.'</td>';
                                                                                                $tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])
                                                                                                                ->where('opd_id', $opd->id)->get();
                                                                                                $b = 1;
                                                                                                foreach ($tujuan_pds as $tujuan_pd) {
                                                                                                    if($b == 1)
                                                                                                    {
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $html .= '<td><ul>';
                                                                                                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                                                                                                $html .= '<li>'.$tujuan_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                            }
                                                                                                            $html .= '</ul></td>';
                                                                                                            $html .= '<td>
                                                                                                                <button type="button"
                                                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-pd data-tujuan-pd-'.$tujuan_pd->id.'"
                                                                                                                data-tujuan-pd-id="'.$tujuan_pd->id.'"
                                                                                                                value="close"
                                                                                                                data-bs-toggle="collapse"
                                                                                                                data-bs-target="#tujuan_tujuan_pd_'.$tujuan_pd->id.'"
                                                                                                                class="accordion-toggle">
                                                                                                                    <i class="fas fa-chevron-right"></i>
                                                                                                                </button>
                                                                                                            </td>';
                                                                                                            $html .= '</tr>
                                                                                                            <tr>
                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_'.$tujuan_pd->id.'">
                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                            <thead>
                                                                                                                                <tr>
                                                                                                                                    <th>Indikator</th>
                                                                                                                                    <th>Target Kinerja Awal</th>
                                                                                                                                    <th>Target</th>
                                                                                                                                    <th>Realisasi</th>
                                                                                                                                    <th>Tahun</th>
                                                                                                                                </tr>
                                                                                                                            </thead>
                                                                                                                            <tbody>';
                                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                                            {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                    $c = 1;
                                                                                                                                    foreach($tahuns as $tahun)
                                                                                                                                    {
                                                                                                                                        if($c == 1)
                                                                                                                                        {
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        } else {
                                                                                                                                            $html .= '<tr>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        }
                                                                                                                                        $c++;
                                                                                                                                    }
                                                                                                                            }
                                                                                                                            $html .= '</tbody>
                                                                                                                        </table>
                                                                                                                    </div>
                                                                                                                </td>
                                                                                                            </tr>';
                                                                                                    } else {
                                                                                                        $html .= '<tr>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $html .= '<td><ul>';
                                                                                                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                                                                                                $html .= '<li>'.$tujuan_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                            }
                                                                                                            $html .= '</ul></td>';
                                                                                                            $html .= '<td>
                                                                                                                <button type="button"
                                                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-pd data-tujuan-pd-'.$tujuan_pd->id.'"
                                                                                                                data-tujuan-pd-id="'.$tujuan_pd->id.'"
                                                                                                                value="close"
                                                                                                                data-bs-toggle="collapse"
                                                                                                                data-bs-target="#tujuan_tujuan_pd_'.$tujuan_pd->id.'"
                                                                                                                class="accordion-toggle">
                                                                                                                    <i class="fas fa-chevron-right"></i>
                                                                                                                </button>
                                                                                                            </td>';
                                                                                                        $html .= '</tr>
                                                                                                            <tr>
                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_'.$tujuan_pd->id.'">
                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                            <thead>
                                                                                                                                <tr>
                                                                                                                                    <th>Indikator</th>
                                                                                                                                    <th>Target Kinerja Awal</th>
                                                                                                                                    <th>Target</th>
                                                                                                                                    <th>Realisasi</th>
                                                                                                                                    <th>Tahun</th>
                                                                                                                                </tr>
                                                                                                                            </thead>
                                                                                                                            <tbody>';
                                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                                            {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                    $c = 1;
                                                                                                                                    foreach($tahuns as $tahun)
                                                                                                                                    {
                                                                                                                                        if($c == 1)
                                                                                                                                        {
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        } else {
                                                                                                                                            $html .= '<tr>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        }
                                                                                                                                        $c++;
                                                                                                                                    }
                                                                                                                            }
                                                                                                                            $html .= '</tbody>
                                                                                                                        </table>
                                                                                                                    </div>
                                                                                                                </td>
                                                                                                            </tr>';
                                                                                                    }
                                                                                                    $b++;
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
        $tahun_awal = $get_periode->tahun_awal;
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
                                                                                    <table class="table table-striped table-condesed">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>OPD</th>
                                                                                                <th>Kode</th>
                                                                                                <th>Tujuan PD</th>
                                                                                                <th>Indikator</th>
                                                                                                <th>Aksi</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                        $opds = MasterOpd::all();
                                                                                        foreach ($opds as $opd) {
                                                                                            $html .= '<tr>';
                                                                                                $html .= '<td>'.$opd->nama.'</td>';
                                                                                                $tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])
                                                                                                                ->where('opd_id', $opd->id)->get();
                                                                                                $b = 1;
                                                                                                foreach ($tujuan_pds as $tujuan_pd) {
                                                                                                    if($b == 1)
                                                                                                    {
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $html .= '<td><ul>';
                                                                                                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                                                                                                $html .= '<li>'.$tujuan_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                            }
                                                                                                            $html .= '</ul></td>';
                                                                                                            $html .= '<td>
                                                                                                                <button type="button"
                                                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-pd data-tujuan-pd-'.$tujuan_pd->id.'"
                                                                                                                data-tujuan-pd-id="'.$tujuan_pd->id.'"
                                                                                                                value="close"
                                                                                                                data-bs-toggle="collapse"
                                                                                                                data-bs-target="#tujuan_tujuan_pd_'.$tujuan_pd->id.'"
                                                                                                                class="accordion-toggle">
                                                                                                                    <i class="fas fa-chevron-right"></i>
                                                                                                                </button>
                                                                                                            </td>';
                                                                                                            $html .= '</tr>
                                                                                                            <tr>
                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_'.$tujuan_pd->id.'">
                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                            <thead>
                                                                                                                                <tr>
                                                                                                                                    <th>Indikator</th>
                                                                                                                                    <th>Target Kinerja Awal</th>
                                                                                                                                    <th>Target</th>
                                                                                                                                    <th>Realisasi</th>
                                                                                                                                    <th>Tahun</th>
                                                                                                                                </tr>
                                                                                                                            </thead>
                                                                                                                            <tbody>';
                                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                                            {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                    $c = 1;
                                                                                                                                    foreach($tahuns as $tahun)
                                                                                                                                    {
                                                                                                                                        if($c == 1)
                                                                                                                                        {
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        } else {
                                                                                                                                            $html .= '<tr>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        }
                                                                                                                                        $c++;
                                                                                                                                    }
                                                                                                                            }
                                                                                                                            $html .= '</tbody>
                                                                                                                        </table>
                                                                                                                    </div>
                                                                                                                </td>
                                                                                                            </tr>';
                                                                                                    } else {
                                                                                                        $html .= '<tr>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $html .= '<td><ul>';
                                                                                                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                                                                                                $html .= '<li>'.$tujuan_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                            }
                                                                                                            $html .= '</ul></td>';
                                                                                                            $html .= '<td>
                                                                                                                <button type="button"
                                                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-pd data-tujuan-pd-'.$tujuan_pd->id.'"
                                                                                                                data-tujuan-pd-id="'.$tujuan_pd->id.'"
                                                                                                                value="close"
                                                                                                                data-bs-toggle="collapse"
                                                                                                                data-bs-target="#tujuan_tujuan_pd_'.$tujuan_pd->id.'"
                                                                                                                class="accordion-toggle">
                                                                                                                    <i class="fas fa-chevron-right"></i>
                                                                                                                </button>
                                                                                                            </td>';
                                                                                                        $html .= '</tr>
                                                                                                            <tr>
                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_'.$tujuan_pd->id.'">
                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                            <thead>
                                                                                                                                <tr>
                                                                                                                                    <th>Indikator</th>
                                                                                                                                    <th>Target Kinerja Awal</th>
                                                                                                                                    <th>Target</th>
                                                                                                                                    <th>Realisasi</th>
                                                                                                                                    <th>Tahun</th>
                                                                                                                                </tr>
                                                                                                                            </thead>
                                                                                                                            <tbody>';
                                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                                            {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                    $c = 1;
                                                                                                                                    foreach($tahuns as $tahun)
                                                                                                                                    {
                                                                                                                                        if($c == 1)
                                                                                                                                        {
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        } else {
                                                                                                                                            $html .= '<tr>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        }
                                                                                                                                        $c++;
                                                                                                                                    }
                                                                                                                            }
                                                                                                                            $html .= '</tbody>
                                                                                                                        </table>
                                                                                                                    </div>
                                                                                                                </td>
                                                                                                            </tr>';
                                                                                                    }
                                                                                                    $b++;
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
                                                                                    <table class="table table-striped table-condesed">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>OPD</th>
                                                                                                <th>Kode</th>
                                                                                                <th>Tujuan PD</th>
                                                                                                <th>Indikator</th>
                                                                                                <th>Aksi</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>';
                                                                                        $opds = MasterOpd::all();
                                                                                        foreach ($opds as $opd) {
                                                                                            $html .= '<tr>';
                                                                                                $html .= '<td>'.$opd->nama.'</td>';
                                                                                                $tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])
                                                                                                                ->where('opd_id', $opd->id)->get();
                                                                                                $b = 1;
                                                                                                foreach ($tujuan_pds as $tujuan_pd) {
                                                                                                    if($b == 1)
                                                                                                    {
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $html .= '<td><ul>';
                                                                                                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                                                                                                $html .= '<li>'.$tujuan_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                            }
                                                                                                            $html .= '</ul></td>';
                                                                                                            $html .= '<td>
                                                                                                                <button type="button"
                                                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-pd data-tujuan-pd-'.$tujuan_pd->id.'"
                                                                                                                data-tujuan-pd-id="'.$tujuan_pd->id.'"
                                                                                                                value="close"
                                                                                                                data-bs-toggle="collapse"
                                                                                                                data-bs-target="#tujuan_tujuan_pd_'.$tujuan_pd->id.'"
                                                                                                                class="accordion-toggle">
                                                                                                                    <i class="fas fa-chevron-right"></i>
                                                                                                                </button>
                                                                                                            </td>';
                                                                                                            $html .= '</tr>
                                                                                                            <tr>
                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_'.$tujuan_pd->id.'">
                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                            <thead>
                                                                                                                                <tr>
                                                                                                                                    <th>Indikator</th>
                                                                                                                                    <th>Target Kinerja Awal</th>
                                                                                                                                    <th>Target</th>
                                                                                                                                    <th>Realisasi</th>
                                                                                                                                    <th>Tahun</th>
                                                                                                                                </tr>
                                                                                                                            </thead>
                                                                                                                            <tbody>';
                                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                                            {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                    $c = 1;
                                                                                                                                    foreach($tahuns as $tahun)
                                                                                                                                    {
                                                                                                                                        if($c == 1)
                                                                                                                                        {
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        } else {
                                                                                                                                            $html .= '<tr>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        }
                                                                                                                                        $c++;
                                                                                                                                    }
                                                                                                                            }
                                                                                                                            $html .= '</tbody>
                                                                                                                        </table>
                                                                                                                    </div>
                                                                                                                </td>
                                                                                                            </tr>';
                                                                                                    } else {
                                                                                                        $html .= '<tr>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->kode.'</td>';
                                                                                                            $html .= '<td>'.$tujuan_pd->deskripsi.'</td>';
                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                            $html .= '<td><ul>';
                                                                                                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                                                                                                $html .= '<li>'.$tujuan_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                            }
                                                                                                            $html .= '</ul></td>';
                                                                                                            $html .= '<td>
                                                                                                                <button type="button"
                                                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-pd data-tujuan-pd-'.$tujuan_pd->id.'"
                                                                                                                data-tujuan-pd-id="'.$tujuan_pd->id.'"
                                                                                                                value="close"
                                                                                                                data-bs-toggle="collapse"
                                                                                                                data-bs-target="#tujuan_tujuan_pd_'.$tujuan_pd->id.'"
                                                                                                                class="accordion-toggle">
                                                                                                                    <i class="fas fa-chevron-right"></i>
                                                                                                                </button>
                                                                                                            </td>';
                                                                                                        $html .= '</tr>
                                                                                                            <tr>
                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_'.$tujuan_pd->id.'">
                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                            <thead>
                                                                                                                                <tr>
                                                                                                                                    <th>Indikator</th>
                                                                                                                                    <th>Target Kinerja Awal</th>
                                                                                                                                    <th>Target</th>
                                                                                                                                    <th>Realisasi</th>
                                                                                                                                    <th>Tahun</th>
                                                                                                                                </tr>
                                                                                                                            </thead>
                                                                                                                            <tbody>';
                                                                                                                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd->id)->get();
                                                                                                                            foreach($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
                                                                                                                            {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                    $c = 1;
                                                                                                                                    foreach($tahuns as $tahun)
                                                                                                                                    {
                                                                                                                                        if($c == 1)
                                                                                                                                        {
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        } else {
                                                                                                                                            $html .= '<tr>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }

                                                                                                                                                $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                                                                                                                                {
                                                                                                                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                    if($cek_tujuan_pd_realisasi_renja)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                }
                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                            $html .='</tr>';
                                                                                                                                        }
                                                                                                                                        $c++;
                                                                                                                                    }
                                                                                                                            }
                                                                                                                            $html .= '</tbody>
                                                                                                                        </table>
                                                                                                                    </div>
                                                                                                                </td>
                                                                                                            </tr>';
                                                                                                    }
                                                                                                    $b++;
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
        $tahun_awal = $get_periode->tahun_awal;
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
                                                                                                                                                        <th>OPD</th>
                                                                                                                                                        <th>Kode</th>
                                                                                                                                                        <th>Tujuan PD</th>
                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                        <th>Aksi</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $opds = MasterOpd::all();
                                                                                                                                                foreach ($opds as $opd) {
                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                        $html .= '<td>'.$opd->nama.'</td>';
                                                                                                                                                        $sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])
                                                                                                                                                                        ->where('opd_id', $opd->id)->get();
                                                                                                                                                        $b = 1;
                                                                                                                                                        foreach($sasaran_pds as $sasaran_pd)
                                                                                                                                                        {
                                                                                                                                                            if($b == 1)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                $html .= '<td><ul>';
                                                                                                                                                                foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                                                                                                                                                    $html .= '<li>'.$sasaran_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '</ul></td>';
                                                                                                                                                                $html .= '<td>
                                                                                                                                                                    <button type="button"
                                                                                                                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-pd data-sasaran-pd-'.$sasaran_pd->id.'"
                                                                                                                                                                    data-sasaran-pd-id="'.$sasaran_pd->id.'"
                                                                                                                                                                    value="close"
                                                                                                                                                                    data-bs-toggle="collapse"
                                                                                                                                                                    data-bs-target="#sasaran_sasaran_pd_'.$sasaran_pd->id.'"
                                                                                                                                                                    class="accordion-toggle">
                                                                                                                                                                        <i class="fas fa-chevron-right"></i>
                                                                                                                                                                    </button>
                                                                                                                                                                </td>';
                                                                                                                                                                $html .= '</tr>
                                                                                                                                                                <tr>
                                                                                                                                                                    <td colspan="12" class="hiddenRow">
                                                                                                                                                                        <div class="collapse accordion-body" id="sasaran_sasaran_pd_'.$sasaran_pd->id.'">
                                                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                                                                        <th>Target</th>
                                                                                                                                                                                        <th>Realisasi</th>
                                                                                                                                                                                        <th>Tahun</th>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>';
                                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                                foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                                {
                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                                        $c = 1;
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($c == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            }
                                                                                                                                                                                            $c++;
                                                                                                                                                                                        }
                                                                                                                                                                                }
                                                                                                                                                                                $html .= '</tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </div>
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                $html .= '<td><ul>';
                                                                                                                                                                foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                                                                                                                                                    $html .= '<li>'.$sasaran_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '</ul></td>';
                                                                                                                                                                $html .= '<td>
                                                                                                                                                                    <button type="button"
                                                                                                                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-pd data-sasaran-pd-'.$sasaran_pd->id.'"
                                                                                                                                                                    data-sasaran-pd-id="'.$sasaran_pd->id.'"
                                                                                                                                                                    value="close"
                                                                                                                                                                    data-bs-toggle="collapse"
                                                                                                                                                                    data-bs-target="#sasaran_sasaran_pd_'.$sasaran_pd->id.'"
                                                                                                                                                                    class="accordion-toggle">
                                                                                                                                                                        <i class="fas fa-chevron-right"></i>
                                                                                                                                                                    </button>
                                                                                                                                                                </td>';
                                                                                                                                                                $html .= '</tr>
                                                                                                                                                                <tr>
                                                                                                                                                                    <td colspan="12" class="hiddenRow">
                                                                                                                                                                        <div class="collapse accordion-body" id="sasaran_sasaran_pd_'.$sasaran_pd->id.'">
                                                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                                                                        <th>Target</th>
                                                                                                                                                                                        <th>Realisasi</th>
                                                                                                                                                                                        <th>Tahun</th>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>';
                                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                                foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                                {
                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                                        $c = 1;
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($c == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            }
                                                                                                                                                                                            $c++;
                                                                                                                                                                                        }
                                                                                                                                                                                }
                                                                                                                                                                                $html .= '</tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </div>
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>';
                                                                                                                                                            }
                                                                                                                                                            $b++;
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
                                                                                                                                                        <th>OPD</th>
                                                                                                                                                        <th>Kode</th>
                                                                                                                                                        <th>Tujuan PD</th>
                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                        <th>Aksi</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $opds = MasterOpd::all();
                                                                                                                                                foreach ($opds as $opd) {
                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                        $html .= '<td>'.$opd->nama.'</td>';
                                                                                                                                                        $sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])
                                                                                                                                                                        ->where('opd_id', $opd->id)->get();
                                                                                                                                                        $b = 1;
                                                                                                                                                        foreach($sasaran_pds as $sasaran_pd)
                                                                                                                                                        {
                                                                                                                                                            if($b == 1)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                $html .= '<td><ul>';
                                                                                                                                                                foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                                                                                                                                                    $html .= '<li>'.$sasaran_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '</ul></td>';
                                                                                                                                                                $html .= '<td>
                                                                                                                                                                    <button type="button"
                                                                                                                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-pd data-sasaran-pd-'.$sasaran_pd->id.'"
                                                                                                                                                                    data-sasaran-pd-id="'.$sasaran_pd->id.'"
                                                                                                                                                                    value="close"
                                                                                                                                                                    data-bs-toggle="collapse"
                                                                                                                                                                    data-bs-target="#sasaran_sasaran_pd_'.$sasaran_pd->id.'"
                                                                                                                                                                    class="accordion-toggle">
                                                                                                                                                                        <i class="fas fa-chevron-right"></i>
                                                                                                                                                                    </button>
                                                                                                                                                                </td>';
                                                                                                                                                                $html .= '</tr>
                                                                                                                                                                <tr>
                                                                                                                                                                    <td colspan="12" class="hiddenRow">
                                                                                                                                                                        <div class="collapse accordion-body" id="sasaran_sasaran_pd_'.$sasaran_pd->id.'">
                                                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                                                                        <th>Target</th>
                                                                                                                                                                                        <th>Realisasi</th>
                                                                                                                                                                                        <th>Tahun</th>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>';
                                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                                foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                                {
                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                                        $c = 1;
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($c == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            }
                                                                                                                                                                                            $c++;
                                                                                                                                                                                        }
                                                                                                                                                                                }
                                                                                                                                                                                $html .= '</tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </div>
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                $html .= '<td><ul>';
                                                                                                                                                                foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                                                                                                                                                    $html .= '<li>'.$sasaran_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '</ul></td>';
                                                                                                                                                                $html .= '<td>
                                                                                                                                                                    <button type="button"
                                                                                                                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-pd data-sasaran-pd-'.$sasaran_pd->id.'"
                                                                                                                                                                    data-sasaran-pd-id="'.$sasaran_pd->id.'"
                                                                                                                                                                    value="close"
                                                                                                                                                                    data-bs-toggle="collapse"
                                                                                                                                                                    data-bs-target="#sasaran_sasaran_pd_'.$sasaran_pd->id.'"
                                                                                                                                                                    class="accordion-toggle">
                                                                                                                                                                        <i class="fas fa-chevron-right"></i>
                                                                                                                                                                    </button>
                                                                                                                                                                </td>';
                                                                                                                                                                $html .= '</tr>
                                                                                                                                                                <tr>
                                                                                                                                                                    <td colspan="12" class="hiddenRow">
                                                                                                                                                                        <div class="collapse accordion-body" id="sasaran_sasaran_pd_'.$sasaran_pd->id.'">
                                                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                                                                        <th>Target</th>
                                                                                                                                                                                        <th>Realisasi</th>
                                                                                                                                                                                        <th>Tahun</th>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>';
                                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                                foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                                {
                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                                        $c = 1;
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($c == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            }
                                                                                                                                                                                            $c++;
                                                                                                                                                                                        }
                                                                                                                                                                                }
                                                                                                                                                                                $html .= '</tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </div>
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>';
                                                                                                                                                            }
                                                                                                                                                            $b++;
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
                                                                                                                                                        <th>OPD</th>
                                                                                                                                                        <th>Kode</th>
                                                                                                                                                        <th>Tujuan PD</th>
                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                        <th>Aksi</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';
                                                                                                                                                $opds = MasterOpd::all();
                                                                                                                                                foreach ($opds as $opd) {
                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                        $html .= '<td>'.$opd->nama.'</td>';
                                                                                                                                                        $sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])
                                                                                                                                                                        ->where('opd_id', $opd->id)->get();
                                                                                                                                                        $b = 1;
                                                                                                                                                        foreach($sasaran_pds as $sasaran_pd)
                                                                                                                                                        {
                                                                                                                                                            if($b == 1)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                $html .= '<td><ul>';
                                                                                                                                                                foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                                                                                                                                                    $html .= '<li>'.$sasaran_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '</ul></td>';
                                                                                                                                                                $html .= '<td>
                                                                                                                                                                    <button type="button"
                                                                                                                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-pd data-sasaran-pd-'.$sasaran_pd->id.'"
                                                                                                                                                                    data-sasaran-pd-id="'.$sasaran_pd->id.'"
                                                                                                                                                                    value="close"
                                                                                                                                                                    data-bs-toggle="collapse"
                                                                                                                                                                    data-bs-target="#sasaran_sasaran_pd_'.$sasaran_pd->id.'"
                                                                                                                                                                    class="accordion-toggle">
                                                                                                                                                                        <i class="fas fa-chevron-right"></i>
                                                                                                                                                                    </button>
                                                                                                                                                                </td>';
                                                                                                                                                                $html .= '</tr>
                                                                                                                                                                <tr>
                                                                                                                                                                    <td colspan="12" class="hiddenRow">
                                                                                                                                                                        <div class="collapse accordion-body" id="sasaran_sasaran_pd_'.$sasaran_pd->id.'">
                                                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                                                                        <th>Target</th>
                                                                                                                                                                                        <th>Realisasi</th>
                                                                                                                                                                                        <th>Tahun</th>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>';
                                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                                foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                                {
                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                                        $c = 1;
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($c == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            }
                                                                                                                                                                                            $c++;
                                                                                                                                                                                        }
                                                                                                                                                                                }
                                                                                                                                                                                $html .= '</tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </div>
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td>'.$sasaran_pd->kode.'</td>';
                                                                                                                                                                $html .= '<td>'.$sasaran_pd->deskripsi.'</td>';
                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                $html .= '<td><ul>';
                                                                                                                                                                foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                                                                                                                                                    $html .= '<li>'.$sasaran_pd_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '</ul></td>';
                                                                                                                                                                $html .= '<td>
                                                                                                                                                                    <button type="button"
                                                                                                                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-pd data-sasaran-pd-'.$sasaran_pd->id.'"
                                                                                                                                                                    data-sasaran-pd-id="'.$sasaran_pd->id.'"
                                                                                                                                                                    value="close"
                                                                                                                                                                    data-bs-toggle="collapse"
                                                                                                                                                                    data-bs-target="#sasaran_sasaran_pd_'.$sasaran_pd->id.'"
                                                                                                                                                                    class="accordion-toggle">
                                                                                                                                                                        <i class="fas fa-chevron-right"></i>
                                                                                                                                                                    </button>
                                                                                                                                                                </td>';
                                                                                                                                                                $html .= '</tr>
                                                                                                                                                                <tr>
                                                                                                                                                                    <td colspan="12" class="hiddenRow">
                                                                                                                                                                        <div class="collapse accordion-body" id="sasaran_sasaran_pd_'.$sasaran_pd->id.'">
                                                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                                                <thead>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                                                                        <th>Target</th>
                                                                                                                                                                                        <th>Realisasi</th>
                                                                                                                                                                                        <th>Tahun</th>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>';
                                                                                                                                                                                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                                                                                                                                                                                foreach($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja)
                                                                                                                                                                                {
                                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                        $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                                        $c = 1;
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($c == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sasaran_pd_realisasi_renja)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            }
                                                                                                                                                                                            $c++;
                                                                                                                                                                                        }
                                                                                                                                                                                }
                                                                                                                                                                                $html .= '</tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </div>
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>';
                                                                                                                                                            }
                                                                                                                                                            $b++;
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
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
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
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase renstra-program-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse show" id="program_visi'.$visi['id'].'">
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
                                                        $html .= '<tr style="background: #c04141;">
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
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
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse show" id="program_misi'.$misi['id'].'">
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
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                        <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
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
                                                                                                                        $html .= '<tr style="background:#41c081">
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran'.$sasaran['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran'.$sasaran['id'].'" class="accordion-toggle text-white" width="95%">
                                                                                                                                        '.strtoupper($sasaran['deskripsi']).'
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
                                                                                                                                                        <span class="badge bg-dark text-uppercase renstra-program-tagging">Program '.$program['kode'].'</span></td>';
                                                                                                                                                        $html .= '<td width="50%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                                                                                                                                            foreach($program_indikator_kinerjas as $program_indikator_kinerja)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<li>'.$program_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                            }
                                                                                                                                                        $html .= '</ul></td>';
                                                                                                                                                    $html .= '</tr>
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="4" class="hiddenRow">
                                                                                                                                                            <div class="collapse accordion-body" id="program_program_'.$program['id'].'">
                                                                                                                                                                <table class="table table-striped table-condesed">
                                                                                                                                                                    <thead>
                                                                                                                                                                        <tr>
                                                                                                                                                                            <th>No</th>
                                                                                                                                                                            <th>Indikator</th>
                                                                                                                                                                            <th>Satuan</th>
                                                                                                                                                                            <th>Target Kinerja Awal</th>
                                                                                                                                                                            <th>Target Anggaran Awal</th>
                                                                                                                                                                            <th>OPD</th>
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
                                                                                                                                                                            $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                            $html .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                            $html .= '<td>Rp.'.number_format($program_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                                                            $html .= '<td><ul>';
                                                                                                                                                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                                                                                                                                            foreach($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja)
                                                                                                                                                                            {
                                                                                                                                                                                $master_opd = MasterOpd::find($opd_program_indikator_kinerja->opd_id);
                                                                                                                                                                                if($master_opd)
                                                                                                                                                                                {
                                                                                                                                                                                    $html .= '<li>'.$master_opd->nama.'</li>';
                                                                                                                                                                                }
                                                                                                                                                                            }
                                                                                                                                                                            $html .= '</ul></td>';
                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                        class="btn btn-icon btn-primary waves-effect waves-light btn-open-program-indikator-kinerja data-program-indikator-kinerja-'.$program_indikator_kinerja->id.'"
                                                                                                                                                                                        data-program-indikator-kinerja-id="'.$program_indikator_kinerja->id.'"
                                                                                                                                                                                        value="close"
                                                                                                                                                                                        data-bs-toggle="collapse"
                                                                                                                                                                                        data-bs-target="#program_program_indikator-kinerja_'.$program_indikator_kinerja->id.'"
                                                                                                                                                                                        class="accordion-toggle">
                                                                                                                                                                                            <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                        </button>
                                                                                                                                                                                    </td>';
                                                                                                                                                                        $html .= '</tr>
                                                                                                                                                                        <tr>
                                                                                                                                                                            <td colspan="12" class="hiddenRow">
                                                                                                                                                                                <div class="collapse accordion-body" id="program_program_indikator-kinerja_'.$program_indikator_kinerja->id.'">
                                                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                                                        <thead>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <th>OPD</th>
                                                                                                                                                                                                <th>Target</th>
                                                                                                                                                                                                <th>Target Anggaran</th>
                                                                                                                                                                                                <th>Realisasi</th>
                                                                                                                                                                                                <th>Realisasi Anggaran</th>
                                                                                                                                                                                                <th>Tahun</th>
                                                                                                                                                                                            </tr>
                                                                                                                                                                                        </thead>
                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                        foreach($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                $master_opd = MasterOpd::find($opd_program_indikator_kinerja->opd_id);
                                                                                                                                                                                                if($master_opd)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $html .= '<td>'.$master_opd->nama.'</td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $b = 1;
                                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                                {
                                                                                                                                                                                                    if($b == 1)
                                                                                                                                                                                                    {
                                                                                                                                                                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $html .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }

                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                if($cek_program_tw_realisasi)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $html .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                }
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $html .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }

                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                if($cek_program_tw_realisasi)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $html .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                }
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $b++;
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
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

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
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase renstra-program-tagging">Visi</span>
                                    </td>
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
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
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
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                        <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
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
                                                                                                                        $html .= '<tr style="background:#41c081">
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran'.$sasaran['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran'.$sasaran['id'].'" class="accordion-toggle text-white" width="95%">
                                                                                                                                        '.strtoupper($sasaran['deskripsi']).'
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
                                                                                                                                                        <span class="badge bg-dark text-uppercase renstra-program-tagging">Program '.$program['kode'].'</span></td>';
                                                                                                                                                        $html .= '<td width="50%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                                                                                                                                            foreach($program_indikator_kinerjas as $program_indikator_kinerja)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<li>'.$program_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                            }
                                                                                                                                                        $html .= '</ul></td>';
                                                                                                                                                    $html .= '</tr>
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="4" class="hiddenRow">
                                                                                                                                                            <div class="collapse accordion-body" id="program_program_'.$program['id'].'">
                                                                                                                                                                <table class="table table-striped table-condesed">
                                                                                                                                                                    <thead>
                                                                                                                                                                        <tr>
                                                                                                                                                                            <th>No</th>
                                                                                                                                                                            <th>Indikator</th>
                                                                                                                                                                            <th>Satuan</th>
                                                                                                                                                                            <th>Target Kinerja Awal</th>
                                                                                                                                                                            <th>Target Anggaran Awal</th>
                                                                                                                                                                            <th>OPD</th>
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
                                                                                                                                                                            $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                            $html .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                            $html .= '<td>Rp.'.number_format($program_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                                                            $html .= '<td><ul>';
                                                                                                                                                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                                                                                                                                            foreach($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja)
                                                                                                                                                                            {
                                                                                                                                                                                $master_opd = MasterOpd::find($opd_program_indikator_kinerja->opd_id);
                                                                                                                                                                                if($master_opd)
                                                                                                                                                                                {
                                                                                                                                                                                    $html .= '<li>'.$master_opd->nama.'</li>';
                                                                                                                                                                                }
                                                                                                                                                                            }
                                                                                                                                                                            $html .= '</ul></td>';
                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                        class="btn btn-icon btn-primary waves-effect waves-light btn-open-program-indikator-kinerja data-program-indikator-kinerja-'.$program_indikator_kinerja->id.'"
                                                                                                                                                                                        data-program-indikator-kinerja-id="'.$program_indikator_kinerja->id.'"
                                                                                                                                                                                        value="close"
                                                                                                                                                                                        data-bs-toggle="collapse"
                                                                                                                                                                                        data-bs-target="#program_program_indikator-kinerja_'.$program_indikator_kinerja->id.'"
                                                                                                                                                                                        class="accordion-toggle">
                                                                                                                                                                                            <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                        </button>
                                                                                                                                                                                    </td>';
                                                                                                                                                                        $html .= '</tr>
                                                                                                                                                                        <tr>
                                                                                                                                                                            <td colspan="12" class="hiddenRow">
                                                                                                                                                                                <div class="collapse accordion-body" id="program_program_indikator-kinerja_'.$program_indikator_kinerja->id.'">
                                                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                                                        <thead>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <th>OPD</th>
                                                                                                                                                                                                <th>Target</th>
                                                                                                                                                                                                <th>Target Anggaran</th>
                                                                                                                                                                                                <th>Realisasi</th>
                                                                                                                                                                                                <th>Realisasi Anggaran</th>
                                                                                                                                                                                                <th>Tahun</th>
                                                                                                                                                                                            </tr>
                                                                                                                                                                                        </thead>
                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                        foreach($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                $master_opd = MasterOpd::find($opd_program_indikator_kinerja->opd_id);
                                                                                                                                                                                                if($master_opd)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $html .= '<td>'.$master_opd->nama.'</td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $b = 1;
                                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                                {
                                                                                                                                                                                                    if($b == 1)
                                                                                                                                                                                                    {
                                                                                                                                                                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $html .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }

                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                if($cek_program_tw_realisasi)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $html .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                }
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $html .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }

                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                if($cek_program_tw_realisasi)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $html .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                }
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $b++;
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
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase renstra-program-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
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
                                                    $a = 1;
                                                    foreach ($misis as $misi) {
                                                        $html .= '<tr style="background: #c04141;">
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
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
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
                                                                                                        '.strtoupper($tujuan['deskripsi']).'
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
                                                                                                        <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
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
                                                                                                                        $html .= '<tr style="background:#41c081">
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran'.$sasaran['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran'.$sasaran['id'].'" class="accordion-toggle text-white" width="95%">
                                                                                                                                        '.strtoupper($sasaran['deskripsi']).'
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
                                                                                                                                                        <span class="badge bg-dark text-uppercase renstra-program-tagging">Program '.$program['kode'].'</span></td>';
                                                                                                                                                        $html .= '<td width="50%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                                                                                                                                            foreach($program_indikator_kinerjas as $program_indikator_kinerja)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<li>'.$program_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                            }
                                                                                                                                                        $html .= '</ul></td>';
                                                                                                                                                    $html .= '</tr>
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="4" class="hiddenRow">
                                                                                                                                                            <div class="collapse accordion-body" id="program_program_'.$program['id'].'">
                                                                                                                                                                <table class="table table-striped table-condesed">
                                                                                                                                                                    <thead>
                                                                                                                                                                        <tr>
                                                                                                                                                                            <th>No</th>
                                                                                                                                                                            <th>Indikator</th>
                                                                                                                                                                            <th>Satuan</th>
                                                                                                                                                                            <th>Target Kinerja Awal</th>
                                                                                                                                                                            <th>Target Anggaran Awal</th>
                                                                                                                                                                            <th>OPD</th>
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
                                                                                                                                                                            $html .= '<td>'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                            $html .= '<td>'.$program_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                                                            $html .= '<td>Rp.'.number_format($program_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                                                            $html .= '<td><ul>';
                                                                                                                                                                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                                                                                                                                                                            foreach($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja)
                                                                                                                                                                            {
                                                                                                                                                                                $master_opd = MasterOpd::find($opd_program_indikator_kinerja->opd_id);
                                                                                                                                                                                if($master_opd)
                                                                                                                                                                                {
                                                                                                                                                                                    $html .= '<li>'.$master_opd->nama.'</li>';
                                                                                                                                                                                }
                                                                                                                                                                            }
                                                                                                                                                                            $html .= '</ul></td>';
                                                                                                                                                                            $html .= '<td>
                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                        class="btn btn-icon btn-primary waves-effect waves-light btn-open-program-indikator-kinerja data-program-indikator-kinerja-'.$program_indikator_kinerja->id.'"
                                                                                                                                                                                        data-program-indikator-kinerja-id="'.$program_indikator_kinerja->id.'"
                                                                                                                                                                                        value="close"
                                                                                                                                                                                        data-bs-toggle="collapse"
                                                                                                                                                                                        data-bs-target="#program_program_indikator-kinerja_'.$program_indikator_kinerja->id.'"
                                                                                                                                                                                        class="accordion-toggle">
                                                                                                                                                                                            <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                        </button>
                                                                                                                                                                                    </td>';
                                                                                                                                                                        $html .= '</tr>
                                                                                                                                                                        <tr>
                                                                                                                                                                            <td colspan="12" class="hiddenRow">
                                                                                                                                                                                <div class="collapse accordion-body" id="program_program_indikator-kinerja_'.$program_indikator_kinerja->id.'">
                                                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                                                        <thead>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <th>OPD</th>
                                                                                                                                                                                                <th>Target</th>
                                                                                                                                                                                                <th>Target Anggaran</th>
                                                                                                                                                                                                <th>Realisasi</th>
                                                                                                                                                                                                <th>Realisasi Anggaran</th>
                                                                                                                                                                                                <th>Tahun</th>
                                                                                                                                                                                            </tr>
                                                                                                                                                                                        </thead>
                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                        foreach($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja)
                                                                                                                                                                                        {
                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                $master_opd = MasterOpd::find($opd_program_indikator_kinerja->opd_id);
                                                                                                                                                                                                if($master_opd)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $html .= '<td>'.$master_opd->nama.'</td>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                }
                                                                                                                                                                                                $b = 1;
                                                                                                                                                                                                foreach($tahuns as $tahun)
                                                                                                                                                                                                {
                                                                                                                                                                                                    if($b == 1)
                                                                                                                                                                                                    {
                                                                                                                                                                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $html .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }

                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                if($cek_program_tw_realisasi)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $html .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                }
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $html .= '<td>'.$cek_program_target_satuan_rp_realisasi->target.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }

                                                                                                                                                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                if($cek_program_tw_realisasi)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $html .= '<td>'.$cek_program_tw_realisasi->realisasi.'/'.$program_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                }
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                        $html .= '</tr>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $b++;
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
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

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
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="collapse show" id="program_visi'.$visi['id'].'">
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
                                                        $html .= '<tr style="background: #c04141;">
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
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
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="collapse show" id="program_misi'.$misi['id'].'">
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
                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
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
                                                                                                        <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
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
                                                                                                                        $html .= '<tr style="background:#41c081">
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="45%">
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
                                                                                                                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                    $html .= '<td width="30%" data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                        foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                        {
                                                                                                                                            $html .= '<li class="mb-2 text-white">'.strtoupper($sasaran_indikator_kinerja->deskripsi).'</li>';
                                                                                                                                        }
                                                                                                                                    $html .= '</ul></td>';

                                                                                                                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle" width="20%"></td>
                                                                                                                                </tr>
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
                                                                                                                                                        $html .= '<td width="45%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle text-white">'.strtoupper($program['deskripsi']);
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
                                                                                                                                                        $html .= '<td width="30%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                                                                                                                                            foreach($program_indikator_kinerjas as $program_indikator_kinerja)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<li class="text-white">'.strtoupper($program_indikator_kinerja->deskripsi).'</li>';
                                                                                                                                                            }
                                                                                                                                                        $html .= '</ul></td>';
                                                                                                                                                        $html .= '<td width="20%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"></td>';
                                                                                                                                                    $html .= '</tr>
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="4" class="hiddenRow">
                                                                                                                                                            <div class="collapse show" id="program_program_'.$program['id'].'">
                                                                                                                                                                <table class="table table-condensed table-striped">
                                                                                                                                                                    <tbody>';
                                                                                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
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
                                                                                                                                                                            $html .= '<td width="45%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle">'.strtoupper($kegiatan['deskripsi']);
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
                                                                                                                                                                            $html .= '<td width="30%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                                            $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                                                            foreach($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja)
                                                                                                                                                                            {
                                                                                                                                                                                $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                                            }
                                                                                                                                                                            $html .= '</ul></td>';
                                                                                                                                                                            $html .= '<td width="20%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle"></td>';
                                                                                                                                                                        $html .='</tr>
                                                                                                                                                                        <tr>
                                                                                                                                                                            <td colspan="4" class="hiddenRow">
                                                                                                                                                                                <div class="collapse accordion-body" id="program_kegiatan_'.$kegiatan['id'].'">
                                                                                                                                                                                    <table class="table table-condensed table-striped">
                                                                                                                                                                                        <thead>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <th>No</th>
                                                                                                                                                                                                <th>Indikator</th>
                                                                                                                                                                                                <th>Target Kinerja Awal</th>
                                                                                                                                                                                                <th>Target Anggaran Awal</th>
                                                                                                                                                                                                <th>Aksi</th>
                                                                                                                                                                                            </tr>
                                                                                                                                                                                        </thead>
                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                        $kegiatan_indikator_kinerja = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                                                                        $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                                                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal, 2 , ',', '.').'</td>';
                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                            <button type="button"
                                                                                                                                                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-kegiatan-indikator-kinerja data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                            data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                            value="close"
                                                                                                                                                                                                            data-bs-toggle="collapse"
                                                                                                                                                                                                            data-bs-target="#program_kegiatan_indikator_kinerja_'.$kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                            class="accordion-toggle">
                                                                                                                                                                                                                <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                                            </button>
                                                                                                                                                                                                        </td>';
                                                                                                                                                                                            $html .= '</tr>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                                                                                                    <div class="collapse accordion-body" id="program_kegiatan_indikator_kinerja_'.$kegiatan_indikator_kinerja->id.'">
                                                                                                                                                                                                        <table class="table table-bordered">
                                                                                                                                                                                                            <thead>
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <th>OPD</th>
                                                                                                                                                                                                                    <th>Target Kinerja</th>
                                                                                                                                                                                                                    <th>Target Anggaran</th>
                                                                                                                                                                                                                    <th>Realisasi Kinerja</th>
                                                                                                                                                                                                                    <th>Realisasi Anggaran</th>
                                                                                                                                                                                                                    <th>Tahun</th>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                            </thead>
                                                                                                                                                                                                            <tbody>';
                                                                                                                                                                                                            $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                                                                                                                                                                            foreach($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                                                                    $master_opd = MasterOpd::find($opd_kegiatan_indikator_kinerja->opd_id);
                                                                                                                                                                                                                    if($master_opd)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        $html .= '<td>'.$master_opd->nama.'</td>';
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    $b = 1;
                                                                                                                                                                                                                    foreach($tahuns as $tahun)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        if($b == 1)
                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                    $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                                    if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                                                                                    {
                                                                                                                                                                                                                                        $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                                                        $kegiatan_realisasi = [];
                                                                                                                                                                                                                                        foreach($kegiatan_tw_realisasies as $kegiatan_tw_realisasi)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        $html .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $html .= '</tr>';
                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                    $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                                    if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                                                                                    {
                                                                                                                                                                                                                                        $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                                                        $kegiatan_realisasi = [];
                                                                                                                                                                                                                                        foreach($kegiatan_tw_realisasies as $kegiatan_tw_realisasi)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        $html .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $html .= '</tr>';
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        $b++;
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
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi</span>
                                    </td>
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
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
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
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
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
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
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
                                                                                                                        $html .= '<tr style="background:#41c081">
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="45%">
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
                                                                                                                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                    $html .= '<td width="30%" data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                        foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                        {
                                                                                                                                            $html .= '<li class="mb-2 text-white">'.strtoupper($sasaran_indikator_kinerja->deskripsi).'</li>';
                                                                                                                                        }
                                                                                                                                    $html .= '</ul></td>';

                                                                                                                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle" width="20%"></td>
                                                                                                                                </tr>
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
                                                                                                                                                        $html .= '<td width="45%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle text-white">'.strtoupper($program['deskripsi']);
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
                                                                                                                                                        $html .= '<td width="30%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                                                                                                                                            foreach($program_indikator_kinerjas as $program_indikator_kinerja)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<li class="text-white">'.strtoupper($program_indikator_kinerja->deskripsi).'</li>';
                                                                                                                                                            }
                                                                                                                                                        $html .= '</ul></td>';
                                                                                                                                                        $html .= '<td width="20%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"></td>';
                                                                                                                                                    $html .= '</tr>
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="4" class="hiddenRow">
                                                                                                                                                            <div class="collapse show" id="program_program_'.$program['id'].'">
                                                                                                                                                                <table class="table table-condensed table-striped">
                                                                                                                                                                    <tbody>';
                                                                                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
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
                                                                                                                                                                            $html .= '<td width="45%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle">'.strtoupper($kegiatan['deskripsi']);
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
                                                                                                                                                                            $html .= '<td width="30%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                                            $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                                                            foreach($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja)
                                                                                                                                                                            {
                                                                                                                                                                                $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                                            }
                                                                                                                                                                            $html .= '</ul></td>';
                                                                                                                                                                            $html .= '<td width="20%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle"></td>';
                                                                                                                                                                        $html .='</tr>
                                                                                                                                                                        <tr>
                                                                                                                                                                            <td colspan="4" class="hiddenRow">
                                                                                                                                                                                <div class="collapse accordion-body" id="program_kegiatan_'.$kegiatan['id'].'">
                                                                                                                                                                                    <table class="table table-condensed table-striped">
                                                                                                                                                                                        <thead>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <th>No</th>
                                                                                                                                                                                                <th>Indikator</th>
                                                                                                                                                                                                <th>Target Kinerja Awal</th>
                                                                                                                                                                                                <th>Target Anggaran Awal</th>
                                                                                                                                                                                                <th>Aksi</th>
                                                                                                                                                                                            </tr>
                                                                                                                                                                                        </thead>
                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                        $kegiatan_indikator_kinerja = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                                                                        $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                                                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal, 2 , ',', '.').'</td>';
                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                            <button type="button"
                                                                                                                                                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-kegiatan-indikator-kinerja data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                            data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                            value="close"
                                                                                                                                                                                                            data-bs-toggle="collapse"
                                                                                                                                                                                                            data-bs-target="#program_kegiatan_indikator_kinerja_'.$kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                            class="accordion-toggle">
                                                                                                                                                                                                                <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                                            </button>
                                                                                                                                                                                                        </td>';
                                                                                                                                                                                            $html .= '</tr>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                                                                                                    <div class="collapse accordion-body" id="program_kegiatan_indikator_kinerja_'.$kegiatan_indikator_kinerja->id.'">
                                                                                                                                                                                                        <table class="table table-bordered">
                                                                                                                                                                                                            <thead>
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <th>OPD</th>
                                                                                                                                                                                                                    <th>Target Kinerja</th>
                                                                                                                                                                                                                    <th>Target Anggaran</th>
                                                                                                                                                                                                                    <th>Realisasi Kinerja</th>
                                                                                                                                                                                                                    <th>Realisasi Anggaran</th>
                                                                                                                                                                                                                    <th>Tahun</th>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                            </thead>
                                                                                                                                                                                                            <tbody>';
                                                                                                                                                                                                            $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                                                                                                                                                                            foreach($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                                                                    $master_opd = MasterOpd::find($opd_kegiatan_indikator_kinerja->opd_id);
                                                                                                                                                                                                                    if($master_opd)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        $html .= '<td>'.$master_opd->nama.'</td>';
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    $b = 1;
                                                                                                                                                                                                                    foreach($tahuns as $tahun)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        if($b == 1)
                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                    $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                                    if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                                                                                    {
                                                                                                                                                                                                                                        $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                                                        $kegiatan_realisasi = [];
                                                                                                                                                                                                                                        foreach($kegiatan_tw_realisasies as $kegiatan_tw_realisasi)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        $html .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $html .= '</tr>';
                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                    $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                                    if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                                                                                    {
                                                                                                                                                                                                                                        $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                                                        $kegiatan_realisasi = [];
                                                                                                                                                                                                                                        foreach($kegiatan_tw_realisasies as $kegiatan_tw_realisasi)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        $html .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $html .= '</tr>';
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        $b++;
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
                                $html .= '<tr style="background: #bbbbbb;">
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#program_visi'.$visi['id'].'" class="accordion-toggle text-white">
                                        '.strtoupper($visi['deskripsi']).'
                                        <br>
                                        <span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
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
                                                    $a = 1;
                                                    foreach ($misis as $misi) {
                                                        $html .= '<tr style="background: #c04141;">
                                                                    <td width="5%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">'.$misi['kode'].'</td>
                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_misi'.$misi['id'].'" class="accordion-toggle text-white">
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
                                                                                                    <td width="95%" data-bs-toggle="collapse" data-bs-target="#program_tujuan'.$tujuan['id'].'" class="accordion-toggle text-white">
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
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="collapse show" id="program_tujuan'.$tujuan['id'].'">
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
                                                                                                                        $html .= '<tr style="background:#41c081">
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle text-white" width="45%">
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
                                                                                                                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                                    $html .= '<td width="30%" data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                        foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                                                                                                                                        {
                                                                                                                                            $html .= '<li class="mb-2 text-white">'.strtoupper($sasaran_indikator_kinerja->deskripsi).'</li>';
                                                                                                                                        }
                                                                                                                                    $html .= '</ul></td>';

                                                                                                                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#program_sasaran_'.$sasaran['id'].'" class="accordion-toggle" width="20%"></td>
                                                                                                                                </tr>
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
                                                                                                                                                        $html .= '<td width="45%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle text-white">'.strtoupper($program['deskripsi']);
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
                                                                                                                                                        $html .= '<td width="30%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                            $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                                                                                                                                                            foreach($program_indikator_kinerjas as $program_indikator_kinerja)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<li class="text-white">'.strtoupper($program_indikator_kinerja->deskripsi).'</li>';
                                                                                                                                                            }
                                                                                                                                                        $html .= '</ul></td>';
                                                                                                                                                        $html .= '<td width="20%" data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle"></td>';
                                                                                                                                                    $html .= '</tr>
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="4" class="hiddenRow">
                                                                                                                                                            <div class="collapse show" id="program_program_'.$program['id'].'">
                                                                                                                                                                <table class="table table-condensed table-striped">
                                                                                                                                                                    <tbody>';
                                                                                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
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
                                                                                                                                                                            $html .= '<td width="45%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle">'.strtoupper($kegiatan['deskripsi']);
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
                                                                                                                                                                            $html .= '<td width="30%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                                            $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                                                            foreach($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja)
                                                                                                                                                                            {
                                                                                                                                                                                $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'</li>';
                                                                                                                                                                            }
                                                                                                                                                                            $html .= '</ul></td>';
                                                                                                                                                                            $html .= '<td width="20%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle"></td>';
                                                                                                                                                                        $html .='</tr>
                                                                                                                                                                        <tr>
                                                                                                                                                                            <td colspan="4" class="hiddenRow">
                                                                                                                                                                                <div class="collapse accordion-body" id="program_kegiatan_'.$kegiatan['id'].'">
                                                                                                                                                                                    <table class="table table-condensed table-striped">
                                                                                                                                                                                        <thead>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <th>No</th>
                                                                                                                                                                                                <th>Indikator</th>
                                                                                                                                                                                                <th>Target Kinerja Awal</th>
                                                                                                                                                                                                <th>Target Anggaran Awal</th>
                                                                                                                                                                                                <th>Aksi</th>
                                                                                                                                                                                            </tr>
                                                                                                                                                                                        </thead>
                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                        $kegiatan_indikator_kinerja = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                                                                        $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                                                                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal, 2 , ',', '.').'</td>';
                                                                                                                                                                                                $html .= '<td>
                                                                                                                                                                                                            <button type="button"
                                                                                                                                                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-kegiatan-indikator-kinerja data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                            data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                            value="close"
                                                                                                                                                                                                            data-bs-toggle="collapse"
                                                                                                                                                                                                            data-bs-target="#program_kegiatan_indikator_kinerja_'.$kegiatan_indikator_kinerja->id.'"
                                                                                                                                                                                                            class="accordion-toggle">
                                                                                                                                                                                                                <i class="fas fa-chevron-right"></i>
                                                                                                                                                                                                            </button>
                                                                                                                                                                                                        </td>';
                                                                                                                                                                                            $html .= '</tr>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                                                                                                    <div class="collapse accordion-body" id="program_kegiatan_indikator_kinerja_'.$kegiatan_indikator_kinerja->id.'">
                                                                                                                                                                                                        <table class="table table-bordered">
                                                                                                                                                                                                            <thead>
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <th>OPD</th>
                                                                                                                                                                                                                    <th>Target Kinerja</th>
                                                                                                                                                                                                                    <th>Target Anggaran</th>
                                                                                                                                                                                                                    <th>Realisasi Kinerja</th>
                                                                                                                                                                                                                    <th>Realisasi Anggaran</th>
                                                                                                                                                                                                                    <th>Tahun</th>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                            </thead>
                                                                                                                                                                                                            <tbody>';
                                                                                                                                                                                                            $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                                                                                                                                                                                                            foreach($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja)
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                                                                    $master_opd = MasterOpd::find($opd_kegiatan_indikator_kinerja->opd_id);
                                                                                                                                                                                                                    if($master_opd)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        $html .= '<td>'.$master_opd->nama.'</td>';
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    $b = 1;
                                                                                                                                                                                                                    foreach($tahuns as $tahun)
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        if($b == 1)
                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                    $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                                    if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                                                                                    {
                                                                                                                                                                                                                                        $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                                                        $kegiatan_realisasi = [];
                                                                                                                                                                                                                                        foreach($kegiatan_tw_realisasies as $kegiatan_tw_realisasi)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        $html .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $html .= '</tr>';
                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                    $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)->where('tahun', $tahun)->first();
                                                                                                                                                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                                                {
                                                                                                                                                                                                                                    $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                                                    if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                                                                                    {
                                                                                                                                                                                                                                        $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                                                        $kegiatan_realisasi = [];
                                                                                                                                                                                                                                        foreach($kegiatan_tw_realisasies as $kegiatan_tw_realisasi)
                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                            $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        $html .= '<td>'.array_sum($kegiatan_realisasi).'/'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                                            $html .= '</tr>';
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        $b++;
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
