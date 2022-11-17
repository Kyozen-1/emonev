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
use App\Models\SubKegiatanTwRealisasi;
use App\Models\OpdKegiatanIndikatorKinerja;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\SubKegiatanIndikatorKinerja;
use App\Models\SubKegiatanTargetSatuanRpRealisasi;
use App\Models\OpdSubKegiatanIndikatorKinerja;

class RenjaController extends Controller
{
    public function index()
    {
        return view('opd.renja.index');
    }

    public function get_program()
    {
        $tws = MasterTw::all();
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal-1;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_programs = Program::whereHas('program_rpjmd')->whereHas('program_indikator_kinerja', function($q){
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

        $html = '<div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="75%">Deskripsi</th>
                                    <th width="20%">Indikator Kinerja</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($programs as $program) {
                                $html .= '<tr style="background:#bbae7f;">';
                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle text-white">'.$program['kode'].'</td>';
                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle text-white">'.strtoupper($program['deskripsi']).'</td>';
                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#program_program_'.$program['id'].'" class="accordion-toggle text-white"><ul>';
                                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])
                                                                        ->whereHas('opd_program_indikator_kinerja', function($q){
                                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                        })->get();
                                        foreach($program_indikator_kinerjas as $program_indikator_kinerja)
                                        {
                                            $html .= '<li>'.$program_indikator_kinerja->deskripsi.'</li>';
                                        }
                                    $html .= '</ul></td>';
                                $html .= '</tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse accordion-body show" id="program_program_'.$program['id'].'">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Indikator</th>
                                                        <th>OPD</th>
                                                        <th>Target</th>
                                                        <th>Satuan</th>
                                                        <th>Target RP</th>
                                                        <th>Tahun</th>
                                                        <th>TW</th>
                                                        <th>Realisasi</th>
                                                        <th>Realisasi RP</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->whereHas('opd_program_indikator_kinerja', function($q){
                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                })->get();
                                                $no_program_indikator_kinerja = 1;
                                                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                                                    $html .= '<tr>';
                                                        $html .= '<td>'.$no_program_indikator_kinerja++.'</td>';
                                                        $html .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                                        $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                                                            ->where('opd_id', Auth::user()->opd->opd_id)
                                                                                            ->get();
                                                        $a = 1;
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
                                                                            $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                            $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                            $html .= '<td> Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                            $c = 1;
                                                                            foreach ($tws as $tw) {
                                                                                if($c == 1)
                                                                                {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_program_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td> <span class="program-span-realisasi '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasi->id.'">'.$cek_program_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td> <span class="program-span-realisasi-rp '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasi->id.'">'.$cek_program_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-tw-realisasi" type="button" data-program-tw-realisasi-id="'.$cek_program_tw_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                    </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control program-add-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td><input type="number" class="form-control program-add-realisasi-rp '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-tw-realisasi" type="button" data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                        $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_program_tw_realisasi)
                                                                                        {
                                                                                            $html .= '<td> <span class="program-span-realisasi '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasia->id.'">'.$cek_program_tw_realisasi->realisasi.'</span></td>';
                                                                                            $html .= '<td> <span class="program-span-realisasi-rp '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasi->id.'">'.$cek_program_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-tw-realisasi" type="button" data-program-tw-realisasi-id="'.$cek_program_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                        </button>
                                                                                                        </td>';
                                                                                        } else {
                                                                                            $html .= '<td><input type="number" class="form-control program-add-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                            $html .= '<td><input type="number" class="form-control program-add-realisasi-rp '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-tw-realisasi" type="button" data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                        }
                                                                                    $html .= '</tr>';
                                                                                }
                                                                                $c++;
                                                                            }
                                                                        } else {
                                                                            $html .= '<td></td>';
                                                                            $html .= '<td></td>';
                                                                            $html .= '<td></td>';
                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                            $c = 1;
                                                                            foreach ($tws as $tw) {
                                                                                if($c == 1)
                                                                                {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $html .= '<td></td>';
                                                                                    $html .= '<td></td>';
                                                                                    $html .= '</tr>';
                                                                                } else {
                                                                                    $html .= '<tr>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                    $html .= '</tr>';
                                                                                }
                                                                                $c++;
                                                                            }
                                                                            $html .= '</tr>';
                                                                        }
                                                                    } else {
                                                                        $html .= '<tr>';
                                                                        $html .= '<td></td>';
                                                                        $html .= '<td></td>';
                                                                        $html .= '<td></td>';
                                                                        $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                        ->where('tahun', $tahun)
                                                                        ->first();
                                                                        if($cek_program_target_satuan_rp_realisasi)
                                                                        {
                                                                            $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                            $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                            $html .= '<td> Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                            $c = 1;
                                                                            foreach ($tws as $tw) {
                                                                                if($c == 1)
                                                                                {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_program_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td> <span class="program-span-realisasi '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasia->id.'">'.$cek_program_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td> <span class="program-span-realisasi-rp '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasi->id.'">'.$cek_program_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-tw-realisasi" type="button" data-program-tw-realisasi-id="'.$cek_program_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                    </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control program-add-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td><input type="number" class="form-control program-add-realisasi-rp '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-tw-realisasi" type="button" data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                        $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_program_tw_realisasi)
                                                                                        {
                                                                                            $html .= '<td> <span class="program-span-realisasi '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasia->id.'">'.$cek_program_tw_realisasi->realisasi.'</span></td>';
                                                                                            $html .= '<td> <span class="program-span-realisasi-rp '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasi->id.'">'.$cek_program_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-tw-realisasi" type="button" data-program-tw-realisasi-id="'.$cek_program_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                        </button>
                                                                                                        </td>';
                                                                                        } else {
                                                                                            $html .= '<td><input type="number" class="form-control program-add-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                            $html .= '<td><input type="number" class="form-control program-add-realisasi-rp '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-tw-realisasi" type="button" data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                        }
                                                                                    $html .= '</tr>';
                                                                                }
                                                                                $c++;
                                                                            }
                                                                        } else {
                                                                            $html .= '<td></td>';
                                                                            $html .= '<td></td>';
                                                                            $html .= '<td></td>';
                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                            $c = 1;
                                                                            foreach ($tws as $tw) {
                                                                                if($c == 1)
                                                                                {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $html .= '<td></td>';
                                                                                    $html .= '<td></td>';
                                                                                    $html .= '</tr>';
                                                                                } else {
                                                                                    $html .= '<tr>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                    $html .= '</tr>';
                                                                                }
                                                                                $c++;
                                                                            }
                                                                            $html .= '</tr>';
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
                                                                                $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                $html .= '<td> Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $c = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($c == 1)
                                                                                    {
                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                        $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_program_tw_realisasi)
                                                                                        {
                                                                                            $html .= '<td> <span class="program-span-realisasi '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasi->id.'">'.$cek_program_tw_realisasi->realisasi.'</span></td>';
                                                                                            $html .= '<td> <span class="program-span-realisasi-rp '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasi->id.'">'.$cek_program_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-tw-realisasi" type="button" data-program-tw-realisasi-id="'.$cek_program_tw_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                        </button>
                                                                                                        </td>';
                                                                                        } else {
                                                                                            $html .= '<td><input type="number" class="form-control program-add-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                            $html .= '<td><input type="number" class="form-control program-add-realisasi-rp '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-tw-realisasi" type="button" data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_program_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td> <span class="program-span-realisasi '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasia->id.'">'.$cek_program_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td> <span class="program-span-realisasi-rp '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasi->id.'">'.$cek_program_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-tw-realisasi" type="button" data-program-tw-realisasi-id="'.$cek_program_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                            </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control program-add-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td><input type="number" class="form-control program-add-realisasi-rp '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-tw-realisasi" type="button" data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                </button>
                                                                                                            </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $c++;
                                                                                }
                                                                            } else {
                                                                                $html .= '<td></td>';
                                                                                $html .= '<td></td>';
                                                                                $html .= '<td></td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $c = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($c == 1)
                                                                                    {
                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $c++;
                                                                                }
                                                                                $html .= '</tr>';
                                                                            }
                                                                        } else {
                                                                            $html .= '<tr>';
                                                                            $html .= '<td></td>';
                                                                            $html .= '<td></td>';
                                                                            $html .= '<td></td>';
                                                                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                            ->where('tahun', $tahun)
                                                                            ->first();
                                                                            if($cek_program_target_satuan_rp_realisasi)
                                                                            {
                                                                                $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td> '.$cek_program_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                $html .= '<td> Rp.'.number_format($cek_program_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $c = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($c == 1)
                                                                                    {
                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                        $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                                        if($cek_program_tw_realisasi)
                                                                                        {
                                                                                            $html .= '<td> <span class="program-span-realisasi '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasia->id.'">'.$cek_program_tw_realisasi->realisasi.'</span></td>';
                                                                                            $html .= '<td> <span class="program-span-realisasi-rp '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasi->id.'">'.$cek_program_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-tw-realisasi" type="button" data-program-tw-realisasi-id="'.$cek_program_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                        </button>
                                                                                                        </td>';
                                                                                        } else {
                                                                                            $html .= '<td><input type="number" class="form-control program-add-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                            $html .= '<td><input type="number" class="form-control program-add-realisasi-rp '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-tw-realisasi" type="button" data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_program_tw_realisasi = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)
                                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_program_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td> <span class="program-span-realisasi '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasia->id.'">'.$cek_program_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td> <span class="program-span-realisasi-rp '.$tahun.' '.$tw->id.' data-program-tw-realisasi-'.$cek_program_tw_realisasi->id.'">'.$cek_program_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-program-edit-tw-realisasi" type="button" data-program-tw-realisasi-id="'.$cek_program_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                            </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control program-add-realisasi '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td><input type="number" class="form-control program-add-realisasi-rp '.$tw->id.' data-program-target-satuan-rp-realisasi-'.$cek_program_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-program-tw-realisasi" type="button" data-program-target-satuan-rp-realisasi-id="'.$cek_program_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                </button>
                                                                                                            </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $c++;
                                                                                }
                                                                            } else {
                                                                                $html .= '<td></td>';
                                                                                $html .= '<td></td>';
                                                                                $html .= '<td></td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $c = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($c == 1)
                                                                                    {
                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '<td></td>';
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $c++;
                                                                                }
                                                                                $html .= '</tr>';
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
                </div>';
        return response()->json(['html' => $html]);
    }

    public function get_kegiatan()
    {
        $tws = MasterTw::all();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal-1;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_programs = Program::whereHas('program_rpjmd')->whereHas('program_indikator_kinerja', function($q){
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

        $html = '<div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="75%">Deskripsi</th>
                                    <th width="20%">Indikator Kinerja</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($programs as $program) {
                                $html .= '<tr style="background: #bbbbbb;">';
                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#kegiatan_program_'.$program['id'].'" class="accordion-toggle text-white">'.$program['kode'].'</td>';
                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#kegiatan_program_'.$program['id'].'" class="accordion-toggle text-white">'.strtoupper($program['deskripsi']).'</td>';
                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#kegiatan_program_'.$program['id'].'" class="accordion-toggle text-white"><ul>';
                                        $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])
                                                                        ->whereHas('opd_program_indikator_kinerja', function($q){
                                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                        })->get();
                                        foreach($program_indikator_kinerjas as $program_indikator_kinerja)
                                        {
                                            $html .= '<li>'.$program_indikator_kinerja->deskripsi.'</li>';
                                        }
                                    $html .= '</ul></td>';
                                $html .= '</tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse show" id="kegiatan_program_'.$program['id'].'">
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
                                                    $html .= '<tr style="background: #41c0c0">';
                                                        $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle text-white">'.$program['kode'].'.'.$kegiatan['kode'].'</td>';
                                                        $html .= '<td width="75%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle text-white">'.strtoupper($kegiatan['deskripsi']).'</td>';
                                                        $html .= '<td width="20%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle text-white"><ul>';
                                                        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                                                        ->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                        })->get();
                                                        foreach($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja)
                                                        {
                                                            $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'</li>';
                                                        }
                                                        $html .= '</ul></td>';
                                                    $html .='</tr>
                                                    <tr>
                                                        <td colspan="4" class="hiddenRow">
                                                            <div class="collapse accordion-body show" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No</th>
                                                                            <th>Indikator</th>
                                                                            <th>OPD</th>
                                                                            <th>Target</th>
                                                                            <th>Satuan</th>
                                                                            <th>Target RP</th>
                                                                            <th>Tahun</th>
                                                                            <th>TW</th>
                                                                            <th>Realisasi</th>
                                                                            <th>Realisasi RP</th>
                                                                            <th>Aksi</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>';
                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                    })->get();
                                                                    $no_kegiatan_indikator_kinerja = 1;
                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                        $html .= '<tr>';
                                                                            $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                            $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                ->where('opd_id', Auth::user()->opd->opd_id)
                                                                                                                ->get();
                                                                            $a = 1;
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
                                                                                                $html .= '<td> '.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                $html .= '<td> '.$cek_kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $html .= '<td> Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                $c = 1;
                                                                                                foreach ($tws as $tw) {
                                                                                                    if($c == 1)
                                                                                                    {
                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                                        {
                                                                                                            $html .= '<td> <span class="kegiatan-span-realisasi '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                            $html .= '<td> <span class="kegiatan-span-realisasi-rp '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-tw-realisasi" type="button" data-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                        </button>
                                                                                                                        </td>';
                                                                                                        } else {
                                                                                                            $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                            $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-tw-realisasi" type="button" data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                            {
                                                                                                                $html .= '<td> <span class="kegiatan-span-realisasi '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasia->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                $html .= '<td> <span class="kegiatan-span-realisasi-rp '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-tw-realisasi" type="button" data-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                            </button>
                                                                                                                            </td>';
                                                                                                            } else {
                                                                                                                $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-tw-realisasi" type="button" data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                </button>
                                                                                                                            </td>';
                                                                                                            }
                                                                                                        $html .= '</tr>';
                                                                                                    }
                                                                                                    $c++;
                                                                                                }
                                                                                            } else {
                                                                                                $html .= '<td></td>';
                                                                                                $html .= '<td></td>';
                                                                                                $html .= '<td></td>';
                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                $c = 1;
                                                                                                foreach ($tws as $tw) {
                                                                                                    if($c == 1)
                                                                                                    {
                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                        $html .= '<td></td>';
                                                                                                        $html .= '<td></td>';
                                                                                                        $html .= '</tr>';
                                                                                                    } else {
                                                                                                        $html .= '<tr>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                        $html .= '</tr>';
                                                                                                    }
                                                                                                    $c++;
                                                                                                }
                                                                                                $html .= '</tr>';
                                                                                            }
                                                                                        } else {
                                                                                            $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)
                                                                                            ->first();
                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                            {
                                                                                                $html .= '<td> '.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                $html .= '<td> '.$cek_kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                $html .= '<td> Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                $c = 1;
                                                                                                foreach ($tws as $tw) {
                                                                                                    if($c == 1)
                                                                                                    {
                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                                        {
                                                                                                            $html .= '<td> <span class="kegiatan-span-realisasi '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasia->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                            $html .= '<td> <span class="kegiatan-span-realisasi-rp '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-tw-realisasi" type="button" data-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                        </button>
                                                                                                                        </td>';
                                                                                                        } else {
                                                                                                            $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                            $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-tw-realisasi" type="button" data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                            {
                                                                                                                $html .= '<td> <span class="kegiatan-span-realisasi '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasia->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                $html .= '<td> <span class="kegiatan-span-realisasi-rp '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-tw-realisasi" type="button" data-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                            </button>
                                                                                                                            </td>';
                                                                                                            } else {
                                                                                                                $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-tw-realisasi" type="button" data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                </button>
                                                                                                                            </td>';
                                                                                                            }
                                                                                                        $html .= '</tr>';
                                                                                                    }
                                                                                                    $c++;
                                                                                                }
                                                                                            } else {
                                                                                                $html .= '<td></td>';
                                                                                                $html .= '<td></td>';
                                                                                                $html .= '<td></td>';
                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                $c = 1;
                                                                                                foreach ($tws as $tw) {
                                                                                                    if($c == 1)
                                                                                                    {
                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                        $html .= '<td></td>';
                                                                                                        $html .= '<td></td>';
                                                                                                        $html .= '</tr>';
                                                                                                    } else {
                                                                                                        $html .= '<tr>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                        $html .= '</tr>';
                                                                                                    }
                                                                                                    $c++;
                                                                                                }
                                                                                                $html .= '</tr>';
                                                                                            }
                                                                                        }
                                                                                        $b++;
                                                                                    }
                                                                                } else {
                                                                                    $html .= '<tr>';
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
                                                                                                    $html .= '<td> '.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                    $html .= '<td> '.$cek_kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                    $html .= '<td> Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                    $c = 1;
                                                                                                    foreach ($tws as $tw) {
                                                                                                        if($c == 1)
                                                                                                        {
                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                            {
                                                                                                                $html .= '<td> <span class="kegiatan-span-realisasi '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                $html .= '<td> <span class="kegiatan-span-realisasi-rp '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-tw-realisasi" type="button" data-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                            </button>
                                                                                                                            </td>';
                                                                                                            } else {
                                                                                                                $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-tw-realisasi" type="button" data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                {
                                                                                                                    $html .= '<td> <span class="kegiatan-span-realisasi '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasia->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                    $html .= '<td> <span class="kegiatan-span-realisasi-rp '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-tw-realisasi" type="button" data-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                </button>
                                                                                                                                </td>';
                                                                                                                } else {
                                                                                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-tw-realisasi" type="button" data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                    </button>
                                                                                                                                </td>';
                                                                                                                }
                                                                                                            $html .= '</tr>';
                                                                                                        }
                                                                                                        $c++;
                                                                                                    }
                                                                                                } else {
                                                                                                    $html .= '<td></td>';
                                                                                                    $html .= '<td></td>';
                                                                                                    $html .= '<td></td>';
                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                    $c = 1;
                                                                                                    foreach ($tws as $tw) {
                                                                                                        if($c == 1)
                                                                                                        {
                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '</tr>';
                                                                                                        } else {
                                                                                                            $html .= '<tr>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                            $html .= '</tr>';
                                                                                                        }
                                                                                                        $c++;
                                                                                                    }
                                                                                                    $html .= '</tr>';
                                                                                                }
                                                                                            } else {
                                                                                                $html .= '<tr>';
                                                                                                $html .= '<td></td>';
                                                                                                $html .= '<td></td>';
                                                                                                $html .= '<td></td>';
                                                                                                $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                ->where('tahun', $tahun)
                                                                                                ->first();
                                                                                                if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                {
                                                                                                    $html .= '<td> '.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                    $html .= '<td> '.$cek_kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                                                                    $html .= '<td> Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</td>';
                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                    $c = 1;
                                                                                                    foreach ($tws as $tw) {
                                                                                                        if($c == 1)
                                                                                                        {
                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                            {
                                                                                                                $html .= '<td> <span class="kegiatan-span-realisasi '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasia->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                $html .= '<td> <span class="kegiatan-span-realisasi-rp '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-tw-realisasi" type="button" data-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                            </button>
                                                                                                                            </td>';
                                                                                                            } else {
                                                                                                                $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-tw-realisasi" type="button" data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                {
                                                                                                                    $html .= '<td> <span class="kegiatan-span-realisasi '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasia->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                    $html .= '<td> <span class="kegiatan-span-realisasi-rp '.$tahun.' '.$tw->id.' data-kegiatan-tw-realisasi-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-tw-realisasi" type="button" data-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'" data-tahun="'.$tahun.'" data-tw-id="'.$tw->id.'">
                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                </button>
                                                                                                                                </td>';
                                                                                                                } else {
                                                                                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-tw-realisasi" type="button" data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                    </button>
                                                                                                                                </td>';
                                                                                                                }
                                                                                                            $html .= '</tr>';
                                                                                                        }
                                                                                                        $c++;
                                                                                                    }
                                                                                                } else {
                                                                                                    $html .= '<td></td>';
                                                                                                    $html .= '<td></td>';
                                                                                                    $html .= '<td></td>';
                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                    $c = 1;
                                                                                                    foreach ($tws as $tw) {
                                                                                                        if($c == 1)
                                                                                                        {
                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '<td></td>';
                                                                                                            $html .= '</tr>';
                                                                                                        } else {
                                                                                                            $html .= '<tr>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                $html .= '<td></td>';
                                                                                                                $html .= '<td></td>';
                                                                                                            $html .= '</tr>';
                                                                                                        }
                                                                                                        $c++;
                                                                                                    }
                                                                                                    $html .= '</tr>';
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
        return response()->json(['html' => $html]);
    }

    public function get_sub_kegiatan()
    {
        $tws = MasterTw::all();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal-1;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_programs = Program::whereHas('program_rpjmd')->whereHas('program_indikator_kinerja', function($q){
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

        $html = '<div class="data-table-rows slim" id="program_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="65%">Deskripsi</th>
                                    <th width="20%">Indikator Kinerja</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($programs as $program) {
                                $html .= '<tr style="background: #bbbbbb;">';
                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program_'.$program['id'].'" class="accordion-toggle text-white">'.$program['kode'].'</td>';
                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program_'.$program['id'].'" class="accordion-toggle text-white">'.strtoupper($program['deskripsi']).'</td>';
                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program_'.$program['id'].'" class="accordion-toggle text-white"></td>';
                                    $html .= '<td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program_'.$program['id'].'" class="accordion-toggle text-white"></td>';
                                $html .='</tr>
                                <tr>
                                    <td colspan="4" class="hiddenRow">
                                        <div class="collapse show" id="sub_kegiatan_program_'.$program['id'].'">
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
                                                        $html .= '<tr style="background: #41c0c0">';
                                                            $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle text-white">'.$program['kode'].'.'.$kegiatan['kode'].'</td>';
                                                            $html .= '<td width="65%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle text-white">'.strtoupper($kegiatan['deskripsi']).'</td>';
                                                            $html .= '<td width="20%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle text-white"></td>';
                                                            $html .= '<td width="10%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle text-white"></td>';
                                                        $html .='</tr>
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
                                                                            foreach ($sub_kegiatans as $sub_kegiatan) {
                                                                                $html .= '<tr style="background:#41c081">';
                                                                                    $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan_'.$sub_kegiatan['id'].'" class="accordion-toggle text-white">'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'</td>';
                                                                                    $html .= '<td width="65%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan_'.$sub_kegiatan['id'].'" class="accordion-toggle text-white">'.$sub_kegiatan['deskripsi'].'</td>';
                                                                                    $html .= '<td width="20%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan_'.$sub_kegiatan['id'].'" class="accordion-toggle text-white"><ul>';
                                                                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                                                                        $html .= '<li class="mb-2">'.$sub_kegiatan_indikator_kinerja->deskripsi.' <button type="button" class="btn-close btn-hapus-sub-kegiatan-indikator-kinerja" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-sub-kegiatan-indikator-kinerja-id="'.$sub_kegiatan_indikator_kinerja->id.'"></button></li>';
                                                                                    }
                                                                                    $html .= '</ul></td>';
                                                                                    $html .= '<td width="10%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan_'.$sub_kegiatan['id'].'" class="accordion-toggle text-white">
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
                                                                                                        <th>OPD</th>
                                                                                                        <th>Target</th>
                                                                                                        <th>Satuan</th>
                                                                                                        <th>Target RP</th>
                                                                                                        <th>Tahun</th>
                                                                                                        <th>Aksi Target</th>
                                                                                                        <th>Tw</th>
                                                                                                        <th>Realisasi</th>
                                                                                                        <th>Realisasi RP</th>
                                                                                                        <th>Aksi Realisasi</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>';
                                                                                                $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q){
                                                                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                })->get();
                                                                                                $no_sub_kegiatan_indikator_kinerja = 1;
                                                                                                foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                                                                                    $html .= '<tr>';
                                                                                                        $html .= '<td>'.$no_sub_kegiatan_indikator_kinerja++.'</td>';
                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                        $a = 1;
                                                                                                        $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)
                                                                                                                                            ->where('opd_id', Auth::user()->opd->opd_id)
                                                                                                                                            ->get();
                                                                                                        foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja) {
                                                                                                            if($a == 1)
                                                                                                            {
                                                                                                                $html .= '<td>'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                $b = 1;
                                                                                                                foreach ($tahuns as $tahun) {
                                                                                                                    if($b == 1)
                                                                                                                    {
                                                                                                                        $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                    ->first();
                                                                                                                        if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                        {
                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-target '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-satuan '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->satuan.'</span></td>';
                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-target-rp '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'" data-target-rp="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-opd-sub-kegiatan-indikator-kinerja-id="'.$opd_sub_kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sub-kegiatan-target-satuan-rp-realisasi="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'">
                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                        </button>
                                                                                                                                        </td>';
                                                                                                                            $c = 1;
                                                                                                                            foreach ($tws as $tw) {
                                                                                                                                if($c == 1)
                                                                                                                                {
                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                        $sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                                                                        if($sub_kegiatan_tw_realisasi)
                                                                                                                                        {
                                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$sub_kegiatan_tw_realisasi->id.'">'.$sub_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$sub_kegiatan_tw_realisasi->id.'">'.$sub_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-sub-kegiatan-tw-realisasi" type="button" data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'" data-sub-kegiatan-tw-realisasi-id="'.$sub_kegiatan_tw_realisasi->id.'">
                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                        </button>
                                                                                                                                                        </td>';
                                                                                                                                        } else {
                                                                                                                                            $html .= '<td><input type="text" class="form-control sub-kegiatan-add-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-sub-kegiatan-tw-realisasi" type="button"  data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                        $sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                                                                        if($sub_kegiatan_tw_realisasi)
                                                                                                                                        {
                                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-sub-kegiatan-tw-realisasi" type="button" data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'"data-sub-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'">
                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                        </button>
                                                                                                                                                        </td>';
                                                                                                                                        } else {
                                                                                                                                            $html .= '<td><input type="text" class="form-control sub-kegiatan-add-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-sub-kegiatan-tw-realisasi" type="button"  data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                            </button>
                                                                                                                                                        </td>';
                                                                                                                                        }
                                                                                                                                    $html .='</tr>';
                                                                                                                                }
                                                                                                                                $c++;
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                                $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                                $html .= '<td><input type="text" class="form-control sub-kegiatan-add-satuan '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                                $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target-rp '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-target-satuan-rp-realisasi" type="button" data-opd-sub-kegiatan-indikator-kinerja-id="'.$opd_sub_kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                </button>
                                                                                                                                            </td>';
                                                                                                                            $html .= '</tr>';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                    ->first();
                                                                                                                        if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                        {
                                                                                                                            $html .= '<tr>';
                                                                                                                            $html .= '<td></td>';
                                                                                                                            $html .= '<td></td>';
                                                                                                                            $html .= '<td></td>';
                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-target '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-satuan '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->satuan.'</span></td>';
                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-target-rp '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'" data-target-rp="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-opd-sub-kegiatan-indikator-kinerja-id="'.$opd_sub_kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sub-kegiatan-target-satuan-rp-realisasi="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'">
                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                        </button>
                                                                                                                                        </td>';
                                                                                                                            $c = 1;
                                                                                                                            foreach ($tws as $tw) {
                                                                                                                                if($c == 1)
                                                                                                                                {
                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                        $sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                                                                        if($sub_kegiatan_tw_realisasi)
                                                                                                                                        {
                                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$sub_kegiatan_tw_realisasi->id.'">'.$sub_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$sub_kegiatan_tw_realisasi->id.'">'.$sub_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-sub-kegiatan-tw-realisasi" type="button" data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'" data-sub-kegiatan-tw-realisasi-id="'.$sub_kegiatan_tw_realisasi->id.'">
                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                        </button>
                                                                                                                                                        </td>';
                                                                                                                                        } else {
                                                                                                                                            $html .= '<td><input type="text" class="form-control sub-kegiatan-add-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-sub-kegiatan-tw-realisasi" type="button"  data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td></td>';
                                                                                                                                        $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                        $sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                                                                        if($sub_kegiatan_tw_realisasi)
                                                                                                                                        {
                                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                                            $html .= '<td> <span class="sub-kegiatan-span-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-sub-kegiatan-tw-realisasi" type="button" data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'"data-sub-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'">
                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                        </button>
                                                                                                                                                        </td>';
                                                                                                                                        } else {
                                                                                                                                            $html .= '<td><input type="text" class="form-control sub-kegiatan-add-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-sub-kegiatan-tw-realisasi" type="button"  data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                            </button>
                                                                                                                                                        </td>';
                                                                                                                                        }
                                                                                                                                    $html .='</tr>';
                                                                                                                                }
                                                                                                                                $c++;
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            $html .= '<tr>';
                                                                                                                            $html .= '<td></td>';
                                                                                                                            $html .= '<td></td>';
                                                                                                                            $html .= '<td></td>';
                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                            $html .= '<td><input type="text" class="form-control sub-kegiatan-add-satuan '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                            $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target-rp '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-target-satuan-rp-realisasi" type="button" data-opd-sub-kegiatan-indikator-kinerja-id="'.$opd_sub_kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
                                                                                                                    $html .= '<td>'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                    $b = 1;
                                                                                                                    foreach ($tahuns as $tahun) {
                                                                                                                        if($b == 1)
                                                                                                                        {
                                                                                                                            $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                    ->first();
                                                                                                                            if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                            {
                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-target '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-satuan '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->satuan.'</span></td>';
                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-target-rp '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'" data-target-rp="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-opd-sub-kegiatan-indikator-kinerja-id="'.$opd_sub_kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sub-kegiatan-target-satuan-rp-realisasi="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'">
                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                            </button>
                                                                                                                                            </td>';
                                                                                                                                $c = 1;
                                                                                                                                foreach ($tws as $tw) {
                                                                                                                                    if($c == 1)
                                                                                                                                    {
                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                            $sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                            if($sub_kegiatan_tw_realisasi)
                                                                                                                                            {
                                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$sub_kegiatan_tw_realisasi->id.'">'.$sub_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$sub_kegiatan_tw_realisasi->id.'">'.$sub_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-sub-kegiatan-tw-realisasi" type="button" data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'" data-sub-kegiatan-tw-realisasi-id="'.$sub_kegiatan_tw_realisasi->id.'">
                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                            </button>
                                                                                                                                                            </td>';
                                                                                                                                            } else {
                                                                                                                                                $html .= '<td><input type="text" class="form-control sub-kegiatan-add-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                $html .= '<td><input type="number" class="form-control sub-kegiatan-add-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-sub-kegiatan-tw-realisasi" type="button"  data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                                                                            $html .= '<td></td>';
                                                                                                                                            $html .= '<td></td>';
                                                                                                                                            $html .= '<td></td>';
                                                                                                                                            $html .= '<td></td>';
                                                                                                                                            $html .= '<td></td>';
                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                            $sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                            if($sub_kegiatan_tw_realisasi)
                                                                                                                                            {
                                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-sub-kegiatan-tw-realisasi" type="button" data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'"data-sub-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'">
                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                            </button>
                                                                                                                                                            </td>';
                                                                                                                                            } else {
                                                                                                                                                $html .= '<td><input type="text" class="form-control sub-kegiatan-add-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                $html .= '<td><input type="number" class="form-control sub-kegiatan-add-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-sub-kegiatan-tw-realisasi" type="button"  data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                </button>
                                                                                                                                                            </td>';
                                                                                                                                            }
                                                                                                                                        $html .='</tr>';
                                                                                                                                    }
                                                                                                                                    $c++;
                                                                                                                                }
                                                                                                                            } else {
                                                                                                                                $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                                $html .= '<td><input type="text" class="form-control sub-kegiatan-add-satuan '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                                $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target-rp '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-target-satuan-rp-realisasi" type="button" data-opd-sub-kegiatan-indikator-kinerja-id="'.$opd_sub_kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                </button>
                                                                                                                                            </td>';
                                                                                                                                $html .='</tr>';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                    ->first();
                                                                                                                            if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                            {
                                                                                                                                $html .= '<tr>';
                                                                                                                                $html .= '<td></td>';
                                                                                                                                $html .= '<td></td>';
                                                                                                                                $html .= '<td></td>';
                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-target '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-satuan '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'">'.$cek_sub_kegiatan_target_satuan_rp_realisasi->satuan.'</span></td>';
                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-target-rp '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.' data-sub-kegiatan-id-'.$sub_kegiatan['id'].'" data-target-rp="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2).'</span></td>';
                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-opd-sub-kegiatan-indikator-kinerja-id="'.$opd_sub_kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sub-kegiatan-target-satuan-rp-realisasi="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'">
                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                            </button>
                                                                                                                                            </td>';
                                                                                                                                $c = 1;
                                                                                                                                foreach ($tws as $tw) {
                                                                                                                                    if($c == 1)
                                                                                                                                    {
                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                            $sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                            if($sub_kegiatan_tw_realisasi)
                                                                                                                                            {
                                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$sub_kegiatan_tw_realisasi->id.'">'.$sub_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$sub_kegiatan_tw_realisasi->id.'">'.$sub_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-sub-kegiatan-tw-realisasi" type="button" data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'" data-sub-kegiatan-tw-realisasi-id="'.$sub_kegiatan_tw_realisasi->id.'">
                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                            </button>
                                                                                                                                                            </td>';
                                                                                                                                            } else {
                                                                                                                                                $html .= '<td><input type="text" class="form-control sub-kegiatan-add-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                $html .= '<td><input type="number" class="form-control sub-kegiatan-add-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-sub-kegiatan-tw-realisasi" type="button"  data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
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
                                                                                                                                            $html .= '<td></td>';
                                                                                                                                            $html .= '<td></td>';
                                                                                                                                            $html .= '<td></td>';
                                                                                                                                            $html .= '<td></td>';
                                                                                                                                            $html .= '<td></td>';
                                                                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                                                                            $sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                                                                                            ->where('tw_id', $tw->id)->first();
                                                                                                                                            if($sub_kegiatan_tw_realisasi)
                                                                                                                                            {
                                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi.'</span></td>';
                                                                                                                                                $html .= '<td> <span class="sub-kegiatan-span-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.' data-sub-kegiatan-tw-realisasi-id-'.$cek_kegiatan_tw_realisasi->id.'">'.$cek_kegiatan_tw_realisasi->realisasi_rp.'</span></td>';
                                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sub-kegiatan-edit-sub-kegiatan-tw-realisasi" type="button" data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'"data-sub-kegiatan-tw-realisasi-id="'.$cek_kegiatan_tw_realisasi->id.'">
                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                                                                            </button>
                                                                                                                                                            </td>';
                                                                                                                                            } else {
                                                                                                                                                $html .= '<td><input type="text" class="form-control sub-kegiatan-add-realisasi '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                $html .= '<td><input type="number" class="form-control sub-kegiatan-add-realisasi-rp '.$tw->id.' data-sub-kegiatan-target-satuan-rp-realisasi-'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-sub-kegiatan-tw-realisasi" type="button"  data-sub-kegiatan-target-satuan-rp-realisasi-id="'.$cek_sub_kegiatan_target_satuan_rp_realisasi->id.'" data-tw-id="'.$tw->id.'">
                                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                                                                                </button>
                                                                                                                                                            </td>';
                                                                                                                                            }
                                                                                                                                        $html .='</tr>';
                                                                                                                                    }
                                                                                                                                    $c++;
                                                                                                                                }
                                                                                                                            } else {
                                                                                                                                $html .= '<tr>';
                                                                                                                                $html .= '<td></td>';
                                                                                                                                $html .= '<td></td>';
                                                                                                                                $html .= '<td></td>';
                                                                                                                                $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                                $html .= '<td><input type="text" class="form-control sub-kegiatan-add-satuan '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                                $html .= '<td><input type="number" class="form-control sub-kegiatan-add-target-rp '.$tahun.' data-opd-sub-kegiatan-indikator-kinerja-'.$opd_sub_kegiatan_indikator_kinerja->id.'"></td>';
                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sub-kegiatan-target-satuan-rp-realisasi" type="button" data-opd-sub-kegiatan-indikator-kinerja-id="'.$opd_sub_kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
